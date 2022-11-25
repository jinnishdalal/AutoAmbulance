<?php

namespace App\Http\Controllers;

use App\EmailTemplate;
use App\Mail\EmailTest;
use App\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SystemController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage system settings'))
        {
            $settings = Utility::settings();
            $payment = Utility::set_payment_settings();

            return view('settings.index', compact('settings','payment'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('manage system settings'))
        {
            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            $header_text = (!isset($request->header_text) && empty($request->header_text)) ? '' : $request->header_text;
            $footer_text = (!isset($request->footer_text) && empty($request->footer_text)) ? '' : $request->footer_text;

            $arrSetting = [
                'header_text' => $header_text,
                'footer_text' => $footer_text,
                'default_language' => $request->default_language,
                'enable_landing' => isset($request->enable_landing) ? $request->enable_landing : 'no',
            ];

            foreach($arrSetting as $key => $val)
            {
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $val,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      \Auth::user()->creatorId(),
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }

            if($request->favicon)
            {
                $request->validate(['favicon' => 'required|image|mimes:png|max:1024',]);
                $faviconName = 'favicon.png';
                $path        = $request->file('favicon')->storeAs('logo', $faviconName);
            }
            if($request->logo)
            {
                $request->validate(['logo' => 'required|image|mimes:png|max:1024',]);
                $logoName = 'logo.png';
                $path     = $request->file('logo')->storeAs('logo', $logoName);
            }
            if($request->landing_logo)
            {
                $request->validate(['landing_logo' => 'required|image|mimes:png|max:1024',]);
                $smallName = 'small-logo.png';
                $path      = $request->file('landing_logo')->storeAs('logo', $smallName);
            }


            $arrEnv = [
                'SITE_RTL' => !isset($request->SITE_RTL) ? 'off' : 'on',
            ];
            Utility::setEnvironmentValue($arrEnv);

            return redirect()->back()->with('success', __('Site Setting successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveEmailSettings(Request $request)
    {
        if(\Auth::user()->can('manage system settings'))
        {
            $request->validate(
                [
                    'mail_driver' => 'required|string|max:255',
                    'mail_host' => 'required|string|max:255',
                    'mail_port' => 'required|string|max:255',
                    'mail_username' => 'required|string|max:255',
                    'mail_password' => 'required|string|max:255',
                    'mail_encryption' => 'required|string|max:255',
                    'mail_from_address' => 'required|string|max:255',
                    'mail_from_name' => 'required|string|max:255',
                ]
            );

            $arrEnv = [
                'MAIL_DRIVER' => $request->mail_driver,
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
                'MAIL_FROM_NAME' => $request->mail_from_name,
            ];

            $env = Utility::setEnvironmentValue($arrEnv);

            if($env)
            {
                return redirect()->back()->with('success', __('Setting successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', 'Something went wrong.');
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveCompanySettings(Request $request)
    {
        if(\Auth::user()->can('manage company settings'))
        {
            $user = \Auth::user();
            $request->validate(
                [
                    'company_name' => 'required|string|max:50',
                    'company_email' => 'required',
                    'company_email_from_name' => 'required|string',
                ]
            );
            $post = $request->all();
            unset($post['_token']);

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach($post as $key => $data)
            {
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $data,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      \Auth::user()->creatorId(),
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function savePaymentSettings(Request $request)
    {
        $user = \Auth::user();

        $validator = \Validator::make(
            $request->all(), [
                'currency' => 'required|string|max:255',
                'currency_symbol' => 'required|string|max:255',
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }else{

            if($user->type == 'Super Admin')
            {
                $arrEnv['CURRENCY_SYMBOL'] = $request->currency_symbol;
                $arrEnv['CURRENCY'] = $request->currency;

                $env = Utility::setEnvironmentValue($arrEnv);
            }

            $post['currency_symbol'] = $request->currency_symbol;
            $post['currency'] = $request->currency;
            
        }

        if(isset($request->is_stripe_enabled) && $request->is_stripe_enabled == 'on')
        {
            $validator = \Validator::make(
                $request->all(), [
                    'stripe_key' => 'required|string',
                    'stripe_secret' => 'required|string',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            
            $post['is_stripe_enabled']     = $request->is_stripe_enabled;
            $post['stripe_secret']         = $request->stripe_secret;
            $post['stripe_key']            = $request->stripe_key;
            $post['stripe_webhook_secret'] = $request->stripe_webhook_secret;
        }
        else
        {
            $post['is_stripe_enabled'] = 'off';
        }
      
        
        if(isset($request->is_paypal_enabled) && $request->is_paypal_enabled == 'on')
        {
            $validator = \Validator::make(
                $request->all(), [
                    'paypal_mode' => 'required|string',
                    'paypal_client_id' => 'required|string',
                    'paypal_secret_key' => 'required|string',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_paypal_enabled'] = $request->is_paypal_enabled;
            $post['paypal_mode']       = $request->paypal_mode;
            $post['paypal_client_id']  = $request->paypal_client_id;
            $post['paypal_secret_key'] = $request->paypal_secret_key;
        }
        else
        {
            $post['is_paypal_enabled'] = 'off';
        }

        if(isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on')
        {

            $validator = \Validator::make(
                $request->all(), [
                    'paystack_public_key' => 'required|string',
                    'paystack_secret_key' => 'required|string',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_paystack_enabled'] = $request->is_paystack_enabled;
            $post['paystack_public_key'] = $request->paystack_public_key;
            $post['paystack_secret_key'] = $request->paystack_secret_key;
        }
        else
        {
            $post['is_paystack_enabled'] = 'off';
        }

        if(isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on')
        {

            $validator = \Validator::make(
                $request->all(), [
                    'flutterwave_public_key' => 'required|string',
                    'flutterwave_secret_key' => 'required|string',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_flutterwave_enabled'] = $request->is_flutterwave_enabled;
            $post['flutterwave_public_key'] = $request->flutterwave_public_key;
            $post['flutterwave_secret_key'] = $request->flutterwave_secret_key;
        }
        else
        {
            $post['is_flutterwave_enabled'] = 'off';
        }

        if(isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on')
        {

            $validator = \Validator::make(
                $request->all(), [
                    'razorpay_public_key' => 'required|string',
                    'razorpay_secret_key' => 'required|string',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_razorpay_enabled'] = $request->is_razorpay_enabled;
            $post['razorpay_public_key'] = $request->razorpay_public_key;
            $post['razorpay_secret_key'] = $request->razorpay_secret_key;
        }
        else
        {
            $post['is_razorpay_enabled'] = 'off';
        }

        if(isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on')
        {

            $validator = \Validator::make(
                $request->all(), [
                    'mercado_app_id' => 'required|string',
                    'mercado_secret_key' => 'required|string',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_mercado_enabled'] = $request->is_mercado_enabled;
            $post['mercado_app_id']     = $request->mercado_app_id;
            $post['mercado_secret_key'] = $request->mercado_secret_key;
        }
        else
        {
            $post['is_mercado_enabled'] = 'off';
        }

        if(isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on')
        {

            $validator = \Validator::make(
                $request->all(), [
                    'paytm_mode' => 'required',
                    'paytm_merchant_id' => 'required|string',
                    'paytm_merchant_key' => 'required|string',
                    'paytm_industry_type' => 'required|string',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_paytm_enabled']    = $request->is_paytm_enabled;
            $post['paytm_mode']          = $request->paytm_mode;
            $post['paytm_merchant_id']   = $request->paytm_merchant_id;
            $post['paytm_merchant_key']  = $request->paytm_merchant_key;
            $post['paytm_industry_type'] = $request->paytm_industry_type;
        }
        else
        {
            $post['is_paytm_enabled'] = 'off';
        }

        if(isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on')
        {
            

            $validator = \Validator::make(
                $request->all(), [
                    'mollie_api_key' => 'required|string',
                    'mollie_profile_id' => 'required|string',
                    'mollie_partner_id' => 'required',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_mollie_enabled'] = $request->is_mollie_enabled;
            $post['mollie_api_key']    = $request->mollie_api_key;
            $post['mollie_profile_id'] = $request->mollie_profile_id;
            $post['mollie_partner_id'] = $request->mollie_partner_id;
        }
        else
        {
            $post['is_mollie_enabled'] = 'off';
        }

        if(isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on')
        {
            


            $validator = \Validator::make(
                $request->all(), [
                    'skrill_email' => 'required|email',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_skrill_enabled'] = $request->is_skrill_enabled;
            $post['skrill_email']      = $request->skrill_email;
        }
        else
        {
            $post['is_skrill_enabled'] = 'off';
        }

        if(isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on')
        {
            

            $validator = \Validator::make(
                $request->all(), [
                    'coingate_mode' => 'required|string',
                    'coingate_auth_token' => 'required|string',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $post['is_coingate_enabled'] = $request->is_coingate_enabled;
            $post['coingate_mode']       = $request->coingate_mode;
            $post['coingate_auth_token'] = $request->coingate_auth_token;
        }
        else
        {
            $post['is_coingate_enabled'] = 'off';
        }

        foreach($post as $key => $data)
        {
            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];

            $insert_payment_setting = \DB::insert(
                'insert into admin_payment_settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', $arr
            );
        }

        return redirect()->back()->with('success', __('Settings updated successfully.'));
    }



    public function savePusherSettings(Request $request)
    {
        if(\Auth::user()->can('manage system settings'))
        {
            if(isset($request->enable_chat))
            {
                $request->validate(
                    [
                        'pusher_app_id' => 'required',
                        'pusher_app_key' => 'required',
                        'pusher_app_secret' => 'required',
                        'pusher_app_cluster' => 'required',
                    ]
                );
            }

            $arrEnvStripe = [
                'CHAT_MODULE' => $request->enable_chat,
                'PUSHER_APP_ID' => $request->pusher_app_id,
                'PUSHER_APP_KEY' => $request->pusher_app_key,
                'PUSHER_APP_SECRET' => $request->pusher_app_secret,
                'PUSHER_APP_CLUSTER' => $request->pusher_app_cluster,
            ];

            $envStripe = Utility::setEnvironmentValue($arrEnvStripe);

            if($envStripe)
            {
                return redirect()->back()->with('success', __('Pusher Setting successfully updated.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something went wrong.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveSystemSettings(Request $request)
    {
        if(\Auth::user()->can('manage company settings'))
        {
            $user = \Auth::user();
            $request->validate(
                [
                    'site_currency' => 'required',
                    'site_currency_symbol' => 'required',
                ]
            );
            $post = $request->all();
            unset($post['_token']);

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach($post as $key => $data)
            {
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $data,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      \Auth::user()->creatorId(),
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveCompanyPaymentSettings(Request $request)
    {
        if(\Auth::user()->can('manage company settings'))
        {
            $post = $request->all();
            unset($post['_token']);

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            $stripe_status = $request->site_enable_stripe ?? 'off';
            $paypal_status = $request->site_enable_paypal ?? 'off';

            $validatorArray = [];

            if($stripe_status == 'on')
            {
                $validatorArray['site_stripe_key']    = 'required|string|max:255';
                $validatorArray['site_stripe_secret'] = 'required|string|max:255';
            }
            if($paypal_status == 'on')
            {
                $validatorArray['site_paypal_client_id']  = 'required|string|max:255';
                $validatorArray['site_paypal_secret_key'] = 'required|string|max:255';
            }

            $validator = Validator::make(
                $request->all(), $validatorArray
            );

            if($validator->fails())
            {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $post['site_enable_stripe'] = $stripe_status;
            $post['site_enable_paypal'] = $paypal_status;

            foreach($post as $key => $data)
            {
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $data,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      \Auth::user()->creatorId(),
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));

        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function companyIndex()
    {
        if(\Auth::user()->can('manage company settings'))
        {
            $settings       = Utility::settings();
            $EmailTemplates = EmailTemplate::all();
            $payment = Utility::set_payment_settings();

            return view('settings.company', compact('settings', 'EmailTemplates','payment'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveBusinessSettings(Request $request)
    {
        if(\Auth::user()->can('manage business settings'))
        {
            $user = \Auth::user();

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            $arrSetting = [];

            if($request->company_logo)
            {
                $request->validate(
                    [
                        'company_logo' => 'image|mimes:png',
                    ]
                );
                $arrSetting['company_logo'] = $user->id . '_logo.png';
                $path                       = $request->file('company_logo')->storeAs('logo', $arrSetting['company_logo']);
                $company_logo               = !empty($request->company_logo) ? $arrSetting['company_logo'] : 'logo.png';
            }

            if($request->company_favicon)
            {
                $request->validate(
                    [
                        'company_favicon' => 'image|mimes:png',
                    ]
                );
                $arrSetting['company_favicon'] = $user->id . '_favicon.png';
                $path                          = $request->file('company_favicon')->storeAs('logo', $arrSetting['company_favicon']);
                $company_favicon               = !empty($request->favicon) ? $arrSetting['company_favicon'] : 'favicon.png';
            }

            $arrSetting['header_text'] = (!isset($request->header_text) && empty($request->header_text)) ? '' : $request->header_text;

            foreach($arrSetting as $key => $data)
            {
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $data,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      \Auth::user()->creatorId(),
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }

            return redirect()->back()->with('success', 'Logo successfully updated.');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function saveTemplateSettings(Request $request)
    {
        $user = \Auth::user();
        $post = $request->all();
        unset($post['_token']);

        if(isset($post['invoice_template']) && (!isset($post['invoice_color']) || empty($post['invoice_color'])))
        {
            $post['invoice_color'] = "ffffff";
        }

        if(isset($post['estimation_template']) && (!isset($post['estimation_color']) || empty($post['estimation_color'])))
        {
            $post['estimation_color'] = "ffffff";
        }

        if($request->invoice_logo)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'invoice_logo' => 'image|mimes:png|max:2048',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoice_logo         = $user->id . '_invoice_logo.png';
            $path                 = $request->file('invoice_logo')->storeAs('invoice_logo', $invoice_logo);
            $post['invoice_logo'] = $invoice_logo;
        }

        if($request->estimation_logo)
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'estimation_logo' => 'image|mimes:png|max:2048',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $estimation_logo         = $user->id . '_estimation_logo.png';
            $path                    = $request->file('estimation_logo')->storeAs('estimation_logo', $estimation_logo);
            $post['estimation_logo'] = $estimation_logo;
        }

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        foreach($post as $key => $data)
        {
            \DB::insert(
                'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                  $data,
                                                                                                                                                                                                                  $key,
                                                                                                                                                                                                                  \Auth::user()->creatorId(),
                                                                                                                                                                                                                  $created_at,
                                                                                                                                                                                                                  $updated_at,
                                                                                                                                                                                                              ]
            );
        }

        if(isset($post['invoice_template']))
        {
            return redirect()->back()->with('success', __('Invoice Setting updated successfully'));
        }

        if(isset($post['estimation_template']))
        {
            return redirect()->back()->with('success', __('Estimation Setting updated successfully'));
        }
    }

    // Test Mail
    public function testEmail()
    {
        $user = \Auth::user();
        if($user->type == 'super admin')
        {
            return view('settings.test_email');
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function testEmailSend(Request $request)
    {
        $validator = \Validator::make($request->all(), ['email' => 'required|email']);
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        try
        {
            Mail::to($request->email)->send(new EmailTest());
        }
        catch(\Exception $e)
        {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', __('Email send Successfully'));
    }
}
