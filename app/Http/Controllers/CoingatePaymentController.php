<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Order;
use App\Plan;
use App\UserCoupon;
use App\Utility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use CoinGate\CoinGate;
use App\InvoicePayment;
use App\Invoice;
use Illuminate\Support\Facades\Validator;

class CoingatePaymentController extends Controller
{
    //


    public $mode;
    public $coingate_auth_token;
    public $is_enabled;
    public $currancy;
    
    public function __construct()
    {
        $this->middleware(['auth','XSS']);
    }

    public function planPayWithCoingate(Request $request){

        $this->paymentSetting();

        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan           = Plan::find($planID);
        $authuser       = Auth::user();
        $coupons_id ='';

        if($plan)
        {
            /* Check for code usage */
            $plan->discounted_price = false;
            $price                  = $plan->price;

            if(isset($request->coupon) && !empty($request->coupon))
            {
                $request->coupon = trim($request->coupon);
                $coupons         = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();

                if(!empty($coupons))
                {
                    $usedCoupun             = $coupons->used_coupon();
                    $discount_value         = ($price / 100) * $coupons->discount;
                    $plan->discounted_price = $price - $discount_value;
                    $coupons_id = $coupons->id;
                    if($usedCoupun >= $coupons->limit)
                    {
                        return redirect()->back()->with('error', __('This coupon code has expired.'));
                    }
                    $price = $price - $discount_value;
                }
                else
                {
                    return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                }
            }

            if($price <= 0)
            {
                $authuser->plan = $plan->id;
                $authuser->save();

                $assignPlan = $authuser->assignPlan($plan->id);

                if($assignPlan['is_success'] == true && !empty($plan))
                {

                    $orderID = time();
                    Order::create(
                        [
                            'order_id' => $orderID,
                            'name' => null,
                            'email' => null,
                            'card_number' => null,
                            'card_exp_month' => null,
                            'card_exp_year' => null,
                            'plan_name' => $plan->name,
                            'plan_id' => $plan->id,
                            'price' => $price==null?0:$price,
                            'price_currency' => !empty($this->currancy) ? $this->currancy : 'usd',
                            'txn_id' => '',
                            'payment_type' => 'coingate',
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $assignPlan = $authuser->assignPlan($plan->id, $request->coingate_payment_frequency);
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                }
                else
                {
                    return redirect()->back()->with('error', __('Plan fail to upgrade.'));
                }
            }
            try{
                CoinGate::config(
                    array(
                        'environment' => $this->mode,
                        'auth_token' => $this->coingate_auth_token,
                        'curlopt_ssl_verifypeer' => FALSE
                    )
                );
                $post_params = array(
                    'order_id' => time(),
                    'price_amount' => $price,
                    'price_currency' => $this->currancy,
                    'receive_currency' => $this->currancy,
                    'callback_url' => route('plan.coingate',[$request->plan_id,'payment_frequency='.$request->coingate_payment_frequency,'coupon_id='.$coupons_id]),
                    'cancel_url' => route('plan.coingate',[$request->plan_id]),
                    'success_url' => route('plan.coingate',[$request->plan_id,'payment_frequency='.$request->coingate_payment_frequency,'coupon_id='.$coupons_id]),
                    'title' => 'Plan #' . time(),
                );

                $order = \CoinGate\Merchant\Order::create($post_params);
                if($order)
                {
                    return redirect($order->payment_url);
                }
                else
                {
                    return redirect()->back()->with('error', __('opps something wren wrong.'));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        }
        else
        {
            return redirect()->back()->with('error', 'Plan is deleted.');
        }
    }
    
    public function getPaymentStatus(Request $request,$plan){

        $this->paymentSetting();

        $planID         = \Illuminate\Support\Facades\Crypt::decrypt($plan);
        $plan           = Plan::find($planID);
        $price                  = $plan->{$request->payment_frequency . '_price'};
        $user = Auth::user();
        $orderID = time();
        if($plan)
        {
            try
            {
                if($request->has('coupon_id') && $request->coupon_id != '')
                {
                    $coupons = Coupon::find($request->coupon_id);

                    if(!empty($coupons))
                    {
                        $userCoupon            = new UserCoupon();
                        $userCoupon->user   = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order  = $orderID;
                        $userCoupon->save();
                        $discount_value         = ($price / 100) * $coupons->discount;
                        $price = $price - $discount_value;
                        $usedCoupun = $coupons->used_coupon();
                        if($coupons->limit <= $usedCoupun)
                        {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }
                    }
                }

                $order                 = new Order();
                $order->order_id       = $orderID;
                $order->name           = $user->name;
                $order->card_number    = '';
                $order->card_exp_month = '';
                $order->card_exp_year  = '';
                $order->plan_name      = $plan->name;
                $order->plan_id        = $plan->id;
                $order->price          = $price;
                $order->price_currency = $this->currancy;
                $order->txn_id         = isset($request->transaction_id) ? $request->transaction_id : '';
                $order->payment_type   = __('paytm');
                $order->payment_status = 'success';
                $order->receipt        = '';
                $order->user_id        = $user->id;
                $order->save();

                $assignPlan = $user->assignPlan($plan->id, $request->payment_frequency);

                if($assignPlan['is_success'])
                {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }

            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __('Plan not found!'));
            }
        }
    }

    public function invoicePayWithCoingate(Request $request){
        $this->paymentSetting();

        $validatorArray = [
            'amount' => 'required',
            'invoice_id' => 'required'
        ];
        $validator      = Validator::make(
            $request->all(), $validatorArray
        )->setAttributeNames(
            ['invoice_id' => 'Invoice']
        );
        if($validator->fails())
        {
            return Utility::error_res($validator->errors()->first());
        }
        $invoice = Invoice::find($request->invoice_id);
        if($invoice->getDue() < $request->amount){
            return Utility::error_res('not correct amount');
        }

        try{

            CoinGate::config(
                array(
                    'environment' => $this->mode,
                    'auth_token' => $this->coingate_auth_token,
                    'curlopt_ssl_verifypeer' => FALSE
                )
            );

            $post_params = array(
                'order_id' => time(),
                'price_amount' => $request->amount,
                'price_currency' => $this->currancy,
                'receive_currency' => $this->currancy,
                'callback_url' => route('invoice.coingate',encrypt([$request->invoice_id])),
                'cancel_url' => route('invoice.coingate',encrypt([$request->invoice_id])),
                'success_url' => route('invoice.coingate',encrypt([$request->invoice_id])),
                'title' => 'Invoice #' . time(),
            );

            $order = \CoinGate\Merchant\Order::create($post_params);
            if($order)
            {
                $data = [
                    'amount' => $request->amount
                ];
                session()->put('coingate_data', $data);
                return redirect($order->payment_url);
            }
            else
            {
                return redirect()->back()->with('error', __('opps something wren wrong.'));
            }
        }
        catch(\Exception $e)
        {
            return redirect()->route('invoices.index')->with('error', __($e->getMessage()));
        }
    }

    public function getInvociePaymentStatus(Request $request,$invoice_id){
        if(!empty($invoice_id))
        {
            $invoice_id = decrypt($invoice_id);
            $invoice    = Invoice::find($invoice_id);
            if($invoice)
            {
                try
                {
                    if(session()->has('coingate_data'))
                    {
                        $invoice_payment                 = new InvoicePayment();
                        $invoice_payment->transaction_id = app('App\Http\Controllers\InvoiceController')->transactionNumber();
                        $invoice_payment->invoice_id     = $invoice_id;
                        $invoice_payment->amount         = isset($get_data['amount']) ? $get_data['amount'] : 0;
                        $invoice_payment->date           = date('Y-m-d');
                        $invoice_payment->payment_id     = 0;
                        $invoice_payment->payment_type   = 'skrill';
                        $invoice_payment->client_id      =  Auth::user()->id;
                        $invoice_payment->notes          = '';
                        $invoice_payment->save();

                        if(($invoice->getDue() - $invoice_payment->amount) == 0)
                        {
                            $invoice->status = 'paid';
                            $invoice->save();
                        }

                        return redirect()->route('invoices.show', $invoice_id)->with('success', __('Payment added Successfully'));
                    }else{
                        return redirect()->route('invoices.show',$invoice_id)->with('error', __('Transaction has been failed! '));
                    }
                }
                catch(\Exception $e)
                {
                   return redirect()->route('invoices.index')->with('error', __('Invoice not found.'));
                }
            }else{
                return redirect()->route('invoices.show',$invoice_id)->with('error', __('Invoice not found.'));
            }
        }else{
            return redirect()->route('invoices.index')->with('error', __('Invoice not found.'));
        }
    }


    public function paymentSetting()
    {
        $admin_payment_setting = Utility::payment_settings();
        $this->currancy =isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
        $this->coingate_auth_token = isset($admin_payment_setting['coingate_auth_token'])?$admin_payment_setting['coingate_auth_token']:'';
        $this->mode = isset($admin_payment_setting['coingate_mode'])?$admin_payment_setting['coingate_mode']:'off';
        $this->is_enabled = isset($admin_payment_setting['is_coingate_enabled'])?$admin_payment_setting['is_coingate_enabled']:'off';

        return;
    }
}