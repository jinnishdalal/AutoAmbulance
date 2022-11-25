<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Order;
use App\Plan;
use App\Utility;
use App\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use LivePixel\MercadoPago\MP;
use App\UserCoupon;
use Illuminate\Support\Facades\Validator;

class MercadoPaymentController extends Controller
{
    public $secret_key;
    public $app_id;
    public $is_enabled;
    public $currancy;
    
    public function __construct()
    {
        $this->middleware(['auth','XSS']);
    }

    public function planPayWithMercado(Request $request){

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
                            'payment_type' => 'Paystack',
                            'payment_status' => 'succeeded',
                            'receipt' => null,
                            'user_id' => $authuser->id,
                        ]
                    );
                    $res['msg'] = __("Plan successfully upgraded.");
                    $res['flag'] = 2;
                    return $res;
                }
                else
                {
                    return Utility::error_res( __('Plan fail to upgrade.'));
                }
            }

            $preference_data       = array(
                "items" => array(
                    array(
                        "title" => "Plan : " . $plan->name,
                        "quantity" => 1,
                        "currency_id" => $this->currancy,
                        "unit_price" => (float)$price,
                    ),
                ),
            );

            try
            {

                $mp         = new MP($this->app_id, $this->secret_key);
                $preference = $mp->create_preference($preference_data);

                return redirect($preference['response']['init_point']);
            }
            catch(Exception $e)
            {
                return redirect()->back()->with('error', $e->getMessage());
            }
            // callback url :  domain.com/plan/mercado

        }
        else
        {
            return redirect()->back()->with('error', 'Plan is deleted.');
        }
    }

    public function getPaymentStatus(Request $request){
        $this->paymentSetting();
        Log::info(json_encode($request->all()));
    }

    public function invoicePayWithMercado(Request $request){

        $this->paymentSetting();

        $validatorArray = [
            'amount' => 'required',
            'invoice_id' => 'required',
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

        $preference_data       = array(
            "items" => array(
                array(
                    "title" => "Invoice Payment",
                    "quantity" => 1,
                    "currency_id" => $this->currancy,
                    "unit_price" => (float)$request->amount,
                ),
            ),
        );

        try
        {

            $mp         = new MP($this->app_id, $this->secret_key);
            $preference = $mp->create_preference($preference_data);



            return redirect($preference['response']['init_point']);
        }
        catch(Exception $e)
        {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function getInvociePaymentStatus(Request $request){
        $this->paymentSetting();
        Log::info(json_encode($request->all()));
    }

    public function paymentSetting()
    {
        
        $admin_payment_setting = Utility::payment_settings();
        $this->currancy =isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
        $this->secret_key = isset($admin_payment_setting['mercado_secret_key'])?$admin_payment_setting['mercado_secret_key']:'';
        $this->app_id = isset($admin_payment_setting['mercado_app_id'])?$admin_payment_setting['mercado_app_id']:'';
        $this->is_enabled = isset($admin_payment_setting['is_mercado_enabled'])?$admin_payment_setting['is_mercado_enabled']:'off';
        return;
    }
}
