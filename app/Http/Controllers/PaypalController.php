<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Invoice;
use App\InvoicePayment;
use App\Order;
use App\Plan;
use App\UserCoupon;
use App\Utility;
use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class PaypalController extends Controller
{
    private $_api_context;

    public $paypal_client_id;
    public $paypal_mode;
    public $paypal_secret_key;
    public $currancy_symbol;
    public $currancy;

    public function setApiContext()
    {
        $this->paymentSetting();

        $user = Auth::user();

        $settings = Utility::settings();

        $paypal_conf = config('paypal');

        
        $paypal_conf['settings']['mode'] = $this->paypal_mode;
        $paypal_conf['client_id']        = $this->paypal_client_id;
        $paypal_conf['secret_key']       = $this->paypal_secret_key;
        
        $this->_api_context = new ApiContext(
            new OAuthTokenCredential(
                $paypal_conf['client_id'], $paypal_conf['secret_key']
            )
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function planPayWithPaypal(Request $request)
    {
        $this->paymentSetting();

        $planID = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
        $plan   = Plan::find($planID);

        if($plan)
        {
            try
            {
                $coupon_id = null;
                $price     = $plan->price;
                if(!empty($request->coupon))
                {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if(!empty($coupons))
                    {
                        $usedCoupun     = $coupons->used_coupon();
                        $discount_value = ($plan->price / 100) * $coupons->discount;
                        $price          = $plan->price - $discount_value;

                        if($coupons->limit == $usedCoupun)
                        {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                        $coupon_id = $coupons->id;
                    }
                    else
                    {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                $this->setApiContext();

                $name = $plan->name;

                $payer = new Payer();
                $payer->setPaymentMethod('paypal');

                $item_1 = new Item();
                $item_1->setName($name)->setCurrency($this->currancy)->setQuantity(1)->setPrice($price);

                $item_list = new ItemList();
                $item_list->setItems([$item_1]);

                $amount = new Amount();
                $amount->setCurrency($this->currancy)->setTotal($price);

                $transaction = new Transaction();
                $transaction->setAmount($amount)->setItemList($item_list)->setDescription($name);

                $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(
                    route('plan.get.payment.status', [ $plan->id, 'coupon_id' => $coupon_id ])
                )->setCancelUrl(
                    route('plan.get.payment.status', [ $plan->id, 'coupon_id' => $coupon_id ])
                );

                $payment = new Payment();
                $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions([$transaction]);

                try
                {
                    $payment->create($this->_api_context);
                }
                catch(\PayPal\Exception\PayPalConnectionException $ex) //PPConnectionException
                {
                    if(config('app.debug'))
                    {
                        return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Connection timeout'));
                    }
                    else
                    {
                        return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Some error occur, sorry for inconvenient'));
                    }
                }
                foreach($payment->getLinks() as $link)
                {
                    if($link->getRel() == 'approval_url')
                    {
                        $redirect_url = $link->getHref();
                        break;
                    }
                }
                Session::put('paypal_payment_id', $payment->getId());
                if(isset($redirect_url))
                {
                    return Redirect::away($redirect_url);
                }

                return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Unknown error occurred'));
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __($e->getMessage()));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function planGetPaymentStatus(Request $request, $plan_id)
    {
        $this->paymentSetting();

        $user = Auth::user();
        $plan = Plan::find($plan_id);

        if($plan)
        {
            $this->setApiContext();

            $payment_id = Session::get('paypal_payment_id');

            Session::forget('paypal_payment_id');

            if(empty($request->PayerID || empty($request->token)))
            {
                return redirect()->route('payment', \Illuminate\Support\Facades\Crypt::encrypt($plan->id))->with('error', __('Payment failed'));
            }

            $payment = Payment::get($payment_id, $this->_api_context);

            $execution = new PaymentExecution();
            $execution->setPayerId($request->PayerID);

            try
            {
                $result = $payment->execute($execution, $this->_api_context)->toArray();

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                $status = ucwords(str_replace('_', ' ', $result['state']));

                if($result['state'] == 'approved')
                {
                    if($request->has('coupon_id') && $request->coupon_id != '')
                    {
                        $coupons = Coupon::find($request->coupon_id);
                        if(!empty($coupons))
                        {
                            $userCoupon         = new UserCoupon();
                            $userCoupon->user_id   = $user->id;
                            $userCoupon->coupon_id = $coupons->id;
                            $userCoupon->order_id  = $orderID;
                            $userCoupon->save();

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
                    $order->price          = $result['transactions'][0]['amount']['total'];
                    $order->price_currency = $this->currancy;
                    $order->txn_id         = $payment_id;
                    $order->payment_type   = __('PAYPAL');
                    $order->payment_status = $result['state'];
                    $order->receipt        = '';
                    $order->user_id        = $user->id;
                    $order->save();

                    $assignPlan = $user->assignPlan($plan->id);
                    if($assignPlan['is_success'])
                    {
                        return redirect()->route('plans.index')->with('success', __('Plan activated Successfully!'));
                    }
                    else
                    {
                        return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                    }
                }
                else
                {
                    return redirect()->route('plans.index')->with('error', __('Transaction has been ') . __($status));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('plans.index')->with('error', __('Transaction has been failed!'));
            }
        }
        else
        {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function clientPayWithPaypal(Request $request, $invoice_id)
    {
        $this->paymentSetting();

        $user = Auth::user();
        $settings = Utility::settings();

        $get_amount = $request->amount;

        $request->validate(['amount' => 'required|numeric|min:0']);

        $invoice = Invoice::find($invoice_id);

        if($invoice)
        {
            if($get_amount > $invoice->getDue())
            {
                return redirect()->back()->with('error', __('Invalid amount.'));
            }
            else
            {
                $this->setApiContext();

                $name = $settings['company_name'] . " - " . Utility::invoiceNumberFormat($invoice->invoice_id);

                $payer = new Payer();
                $payer->setPaymentMethod('paypal');

                $item_1 = new Item();
                $item_1->setName($name)->setCurrency($settings['site_currency'])->setQuantity(1)->setPrice($get_amount);

                $item_list = new ItemList();
                $item_list->setItems([$item_1]);

                $amount = new Amount();
                $amount->setCurrency($settings['site_currency'])->setTotal($get_amount);

                $transaction = new Transaction();
                $transaction->setAmount($amount)->setItemList($item_list)->setDescription($name);

                $redirect_urls = new RedirectUrls();
                $redirect_urls->setReturnUrl(route('client.get.payment.status', $invoice->id))->setCancelUrl(route('client.get.payment.status', $invoice->id));

                $payment = new Payment();
                $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions([$transaction]);

                try
                {
                    $payment->create($this->_api_context);
                }
                catch(\PayPal\Exception\PayPalConnectionException $ex) //PPConnectionException
                {
                    if(\Config::get('app.debug'))
                    {
                        return redirect()->route('invoices.show', $invoice_id)->with('error', __('Connection timeout'));
                    }
                    else
                    {
                        return redirect()->route('invoices.show', $invoice_id)->with('error', __('Some error occur, sorry for inconvenient'));
                    }
                }
                foreach($payment->getLinks() as $link)
                {
                    if($link->getRel() == 'approval_url')
                    {
                        $redirect_url = $link->getHref();
                        break;
                    }
                }
                Session::put('paypal_payment_id', $payment->getId());
                if(isset($redirect_url))
                {
                    return Redirect::away($redirect_url);
                }
                return redirect()->route('invoices.show', $invoice_id)->with('error', __('Unknown error occurred'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function clientGetPaymentStatus(Request $request, $invoice_id)
    {
        $this->paymentSetting();

        $user = Auth::user();

        $invoice = Invoice::find($invoice_id);

        if($invoice)
        {
            $this->setApiContext();

            $payment_id = Session::get('paypal_payment_id');

            Session::forget('paypal_payment_id');

            if(empty($request->PayerID || empty($request->token)))
            {
                return redirect()->route('invoices.show', $invoice_id)->with('error', __('Payment failed'));
            }

            $payment   = Payment::get($payment_id, $this->_api_context);

            $execution = new PaymentExecution();
            $execution->setPayerId($request->PayerID);

            try
            {
                $result = $payment->execute($execution, $this->_api_context)->toArray();

                $status = ucwords(str_replace('_', ' ', $result['state']));

                if($result['state'] == 'approved')
                {
                    $invoice_payment = new InvoicePayment();
                    $invoice_payment->transaction_id =  app('App\Http\Controllers\InvoiceController')->transactionNumber();
                    $invoice_payment->invoice_id = $invoice->id;
                    $invoice_payment->amount = $result['transactions'][0]['amount']['total'];
                    $invoice_payment->date = date('Y-m-d');
                    $invoice_payment->payment_id = 0;
                    $invoice_payment->payment_type = __('PAYPAL');
                    $invoice_payment->client_id = $user->id;
                    $invoice_payment->notes = '';
                    $invoice_payment->save();

                    if(($invoice->getDue() - $invoice_payment->amount) == 0) {
                        $invoice->status = 'paid';
                        $invoice->save();
                    }

                    return redirect()->route('invoices.show', $invoice_id)->with('success', __('Payment added Successfully'));
                }
                else
                {
                    return redirect()->route('invoices.show', $invoice_id)->with('error', __('Transaction has been ' . $status));
                }

            } catch(\Exception $e) {
                return redirect()->route('invoices.show', $invoice_id)->with('error', __('Transaction has been failed!'));
            }
        } else {
            return redirect()->back()->with('error',__('Permission denied.'));
        }
    }

    public function paymentSetting(){

        $admin_payment_setting = Utility::payment_settings();

        $this->currancy_symbol = isset($admin_payment_setting['currency_symbol'])?$admin_payment_setting['currency_symbol']:'';
        $this->currancy = isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
        $this->paypal_client_id = isset($admin_payment_setting['paypal_client_id'])?$admin_payment_setting['paypal_client_id']:'';
        $this->paypal_mode = isset($admin_payment_setting['paypal_mode'])?$admin_payment_setting['paypal_mode']:'';
        $this->paypal_secret_key = isset($admin_payment_setting['paypal_secret_key'])?$admin_payment_setting['paypal_secret_key']:'';
    }
}
