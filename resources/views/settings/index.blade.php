@extends('layouts.admin')
@php
    $logo=asset(Storage::url('logo/'));
@endphp

@section('page-title')
    {{__('Settings')}}
@endsection

@section('content')
    <style type="text/css">
        .text-right {
            text-align: right !important;
            float: right !important;
        }
    </style>
    <div class="row">
        <div class="col-12">
            <div class="setting-tab">
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li>
                        <a class="active" data-toggle="tab" href="#site-setting" role="tab" aria-controls="" aria-selected="false">{{__('Site Setting')}}</a>
                    </li>
                    <li>
                        <a id="profile-tab3" data-toggle="tab" href="#email-setting" role="tab" aria-controls="" aria-selected="false">{{__('Email Setting')}}</a>
                    </li>
                    <li>
                        <a id="profile-tab3" data-toggle="tab" href="#payment-setting" role="tab" aria-controls="" aria-selected="false">{{__('Payment Setting')}}</a>
                    </li>
                    <li>
                        <a id="profile-tab4" data-toggle="tab" href="#pusher-setting" role="tab" aria-controls="" aria-selected="false">{{__('Pusher Setting')}}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade fade show active" id="site-setting" role="tabpanel" aria-labelledby="profile-tab3">
                        {{Form::open(array('url'=>'systems','method'=>'POST','enctype' => "multipart/form-data"))}}
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 col-md-6">
                                <h4 class="small-title">{{__('Logo')}}</h4>
                                <div class="card setting-card">
                                    <div class="logo-content">
                                        <img src="{{ asset(Storage::url('logo/logo.png')) }}" class="big-logo">
                                    </div>
                                    <div class="input-file btn-file">{{__('Select image')}}
                                        <input type="file" class="form-control" name="logo" id="logo" data-filename="logo_update" accept=".jpeg,.jpg,.png">
                                    </div>
                                    <p class="logo_update"></p>
                                    @error('logo')
                                    <span class="invalid-logo text-xs text-danger" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-md-6">
                                <h4 class="small-title">{{__('Favicon')}}</h4>
                                <div class="card setting-card">
                                    <div class="logo-content">
                                        <img src="{{ asset(Storage::url('logo/favicon.png')) }}" class="small-logo">
                                    </div>
                                    <div class="input-file btn-file">{{__('Select image')}}
                                        <input type="file" class="form-control" name="favicon" id="favicon" data-filename="favicon_update" accept=".jpeg,.jpg,.png">
                                    </div>
                                    <p class="favicon_update"></p>
                                    @error('favicon')
                                    <span class="invalid-favicon text-xs text-danger" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-md-6">
                                <h4 class="small-title">{{__('Landing Page Logo')}}</h4>
                                <div class="card setting-card">
                                    <div class="logo-content">
                                        <img src="{{ asset(Storage::url('logo/landing_logo.png')) }}" class="big-logo">
                                    </div>
                                    <div class="input-file btn-file">{{__('Select image')}}
                                        <input type="file" class="form-control" name="landing_logo" id="landing_logo" data-filename="landing_logo_update" accept=".jpeg,.jpg,.png">
                                    </div>
                                    <p class="landing_logo_update"></p>
                                    @error('landing_logo')
                                    <span class="invalid-landing_logo text-xs text-danger" role="alert">{{ $message }}</span>
                                    @enderror
                                    <div class="form-group">
                                        <br>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="enable_landing" value="yes" class="custom-control-input" id="enable_landing" {{ (Utility::getValByName('enable_landing') == 'yes') ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold text-dark text-xs" for="enable_landing">{{ __('Enable Landing Page') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 col-md-6">
                                <h4 class="small-title">{{__('Settings')}}</h4>
                                <div class="card setting-card">
                                    <div class="form-group">
                                        {{Form::label('header_text',__('Title Text'),['class'=>'form-control-label']) }}
                                        {{Form::text('header_text',Utility::getValByName('header_text'),array('class'=>'form-control','placeholder'=>__('Enter Header Title Text')))}}
                                        @error('header_text')
                                        <span class="invalid-header_text text-danger text-xs" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        {{Form::label('footer_text',__('Footer Text'),['class'=>'form-control-label']) }}
                                        {{Form::text('footer_text',Utility::getValByName('footer_text'),array('class'=>'form-control','placeholder'=>__('Enter Footer Text')))}}
                                        @error('footer_text')
                                        <span class="invalid-footer_text text-danger text-xs" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        {{Form::label('default_language',__('Default Language'),['class'=>'form-control-label']) }}
                                        <select name="default_language" id="default_language" class="form-control select2">
                                            @foreach(Utility::languages() as $language)
                                                <option @if(Utility::getValByName('default_language') == $language) selected @endif value="{{$language}}">{{Str::upper($language)}}</option>
                                            @endforeach
                                        </select>
                                        @error('default_language')
                                        <span class="invalid-default_language text-danger text-xs" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        {{Form::label('SITE_RTL',__('RTL'),array('class'=>'form-control-label')) }}
                                        <div class="">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="SITE_RTL" id="SITE_RTL" {{ env('SITE_RTL') == 'on' ? 'checked="checked"' : '' }}>
                                                <label class="custom-control-label form-control-label" for="SITE_RTL"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        {{Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                    <div class="tab-pane fade" id="email-setting" role="tabpanel">
                        {{Form::open(array('route'=>'email.settings','method'=>'post'))}}
                        <div class="card bg-none">
                            <div class="row company-setting">
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('mail_driver',__('Mail Driver'),['class'=>'form-control-label']) }}
                                    {{Form::text('mail_driver',env('MAIL_DRIVER'),array('class'=>'form-control','placeholder'=>__('Enter Mail Driver')))}}
                                    @error('mail_driver')
                                    <span class="text-xs text-danger invalid-mail_driver" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('mail_host',__('Mail Host'),['class'=>'form-control-label']) }}
                                    {{Form::text('mail_host',env('MAIL_HOST'),array('class'=>'form-control ','placeholder'=>__('Enter Mail Driver')))}}
                                    @error('mail_host')
                                    <span class="text-xs text-danger invalid-mail_driver" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('mail_port',__('Mail Port'),['class'=>'form-control-label']) }}
                                    {{Form::text('mail_port',env('MAIL_PORT'),array('class'=>'form-control','placeholder'=>__('Enter Mail Port')))}}
                                    @error('mail_port')
                                    <span class="text-xs text-danger invalid-mail_port" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('mail_username',__('Mail Username'),['class'=>'form-control-label']) }}
                                    {{Form::text('mail_username',env('MAIL_USERNAME'),array('class'=>'form-control','placeholder'=>__('Enter Mail Username')))}}
                                    @error('mail_username')
                                    <span class="text-xs text-danger invalid-mail_username" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('mail_password',__('Mail Password'),['class'=>'form-control-label']) }}
                                    {{Form::text('mail_password',env('MAIL_PASSWORD'),array('class'=>'form-control','placeholder'=>__('Enter Mail Password')))}}
                                    @error('mail_password')
                                    <span class="text-xs text-danger invalid-mail_password" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('mail_encryption',__('Mail Encryption'),['class'=>'form-control-label']) }}
                                    {{Form::text('mail_encryption',env('MAIL_ENCRYPTION'),array('class'=>'form-control','placeholder'=>__('Enter Mail Encryption')))}}
                                    @error('mail_encryption')
                                    <span class="text-xs text-danger invalid-mail_encryption" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('mail_from_address',__('Mail From Address'),['class'=>'form-control-label']) }}
                                    {{Form::text('mail_from_address',env('MAIL_FROM_ADDRESS'),array('class'=>'form-control','placeholder'=>__('Enter Mail From Address')))}}
                                    @error('mail_from_address')
                                    <span class="text-xs text-danger invalid-mail_from_address" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('mail_from_name',__('Mail From Name'),['class'=>'form-control-label']) }}
                                    {{Form::text('mail_from_name',env('MAIL_FROM_NAME'),array('class'=>'form-control','placeholder'=>__('Enter Mail From Name')))}}
                                    @error('mail_from_name')
                                    <span class="text-xs text-danger invalid-mail_from_name" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-12 text-right">
                                    <a href="#" class="btn btn-xs btn-white btn-icon-only bg-warning width-auto" data-ajax-popup="true" data-title="{{__('Send Test Mail')}}" data-url="{{route('test.email')}}">
                                        {{__('Test Mail')}}
                                    </a>
                                    {{Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))}}
                                </div>
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                    <div class="tab-pane fade" id="payment-setting" role="tabpanel">
                        <h4 class="header-title mb-3">{{__('Payment Setting')}}</h4>
                        <small class="text-dark font-weight-bold">{{__("This detail will use for make purchase of plan")}}</small></br></br>
                        
                        <form id="setting-form" method="post" action="{{route('payment.settings')}}">
                            @csrf
                            <div class="row">
                                <div class="col-8">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                    <label class="form-control-label">{{__('Currency')}} *</label>
                                                    <input type="text" name="currency" class="form-control" id="currency" value="{{(!isset($payment['currency']) || is_null($payment['currency'])) ? '' : $payment['currency']}}" required>
                                                    <small class="text-xs">
                                                        {{ __('Note: Add currency code as per three-letter ISO code') }}.
                                                        <a href="https://stripe.com/docs/currencies" target="_blank">{{ __('you can find out here..') }}</a>
                                                    </small>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                    <label for="currency_symbol" class="form-control-label">{{__('Currency Symbol')}}</label>
                                                    <input type="text" name="currency_symbol" class="form-control" id="currency_symbol" value="{{(!isset($payment['currency_symbol']) || is_null($payment['currency_symbol'])) ? '' : $payment['currency_symbol']}}" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="accordion-2" class="accordion accordion-spaced">
                                <!-- Strip -->
                                <div class="card">
                                    <div class="card-header py-4" id="heading-2-2" data-toggle="collapse" role="button" data-target="#collapse-2-2" aria-expanded="false" aria-controls="collapse-2-2">
                                        <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Stripe') }}</h6>

                                    </div>
                                    <div id="collapse-2-2" class="collapse" aria-labelledby="heading-2-2" data-parent="#accordion-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 py-2">
                                                    <h5 class="h5">{{ __('Stripe') }}</h5>
                                                </div>
                                                <div class="col-6 py-2 text-right">
                                                    <div class="custom-control custom-switch text-right">
                                                        <input type="hidden" name="is_stripe_enabled" value="off">
                                                        <input type="checkbox" class="custom-control-input" name="is_stripe_enabled" id="is_stripe_enabled" {{ isset($payment['is_stripe_enabled']) && $payment['is_stripe_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-control-label" for="is_stripe_enabled">{{ __('Enable Stripe') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="stripe_key">{{__('Stripe Key')}}</label>
                                                        <input class="form-control" placeholder="{{__('Stripe Key')}}" name="stripe_key" type="text" value="{{(!isset($payment['stripe_key']) || is_null($payment['stripe_key'])) ? '' : $payment['stripe_key']}}" id="stripe_key">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="stripe_secret">{{__('Stripe Secret')}}</label>
                                                        <input class="form-control " placeholder="{{ __('Stripe Secret') }}" name="stripe_secret" type="text" value="{{(!isset($payment['stripe_secret']) || is_null($payment['stripe_secret'])) ? '' : $payment['stripe_secret']}}" id="stripe_secret">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="stripe_secret">{{__('Stripe_Webhook_Secret')}}</label>
                                                        <input class="form-control " placeholder="{{ __('Enter Stripe Webhook Secret') }}" name="stripe_webhook_secret" type="text" value="{{(!isset($payment['stripe_webhook_secret']) || is_null($payment['stripe_webhook_secret'])) ? '' : $payment['stripe_webhook_secret']}}" id="stripe_webhook_secret">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Paypal -->
                                <div class="card">
                                    <div class="card-header py-4" id="heading-2-3" data-toggle="collapse" role="button" data-target="#collapse-2-3" aria-expanded="false" aria-controls="collapse-2-3">
                                        <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Paypal') }}</h6>
                                    </div>
                                    <div id="collapse-2-3" class="collapse" aria-labelledby="heading-2-3" data-parent="#accordion-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 py-2">
                                                    <h5 class="h5">{{ __('Paypal') }}</h5>
                                                </div>
                                                <div class="col-6 py-2 text-right">
                                                    <div class="custom-control custom-switch text-right">
                                                        <input type="hidden" name="is_paypal_enabled" value="off">
                                                        <input type="checkbox" class="custom-control-input" name="is_paypal_enabled" id="is_paypal_enabled" {{ isset($payment['is_paypal_enabled']) && $payment['is_paypal_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-control-label" for="is_paypal_enabled">{{ __('Enable Paypal') }}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pb-4">
                                                    <label class="paypal-label form-control-label" for="paypal_mode">{{__('Paypal Mode')}}</label> <br>
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label class="btn btn-primary btn-sm {{ !isset($payment['paypal_mode']) || $payment['paypal_mode'] == '' || $payment['paypal_mode'] == 'sandbox' ? 'active' : '' }}">
                                                            <input type="radio" name="paypal_mode" value="sandbox" {{ !isset($payment['paypal_mode']) || $payment['paypal_mode'] == '' || $payment['paypal_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>{{__('Sandbox')}}  
                                                        </label>
                                                        <label class="btn btn-primary btn-sm {{ isset($payment['paypal_mode']) && $payment['paypal_mode'] == 'live' ? 'active' : '' }}">
                                                            <input type="radio" name="paypal_mode" value="live" {{ isset($payment['paypal_mode']) && $payment['paypal_mode'] == 'live' ? 'checked="checked"' : '' }}>{{__('Live')}}
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="paypal_client_id">{{ __('Client ID') }}</label>
                                                        <input type="text" name="paypal_client_id" id="paypal_client_id" class="form-control" value="{{(!isset($payment['paypal_client_id']) || is_null($payment['paypal_client_id'])) ? '' : $payment['paypal_client_id']}}" placeholder="{{ __('Client ID') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="paypal_secret_key">{{ __('Secret Key') }}</label>
                                                        <input type="text" name="paypal_secret_key" id="paypal_secret_key" class="form-control" value="{{(!isset($payment['paypal_secret_key']) || is_null($payment['paypal_secret_key'])) ? '' : $payment['paypal_secret_key']}}" placeholder="{{ __('Secret Key') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Paystack -->
                                <div class="card">
                                    <div class="card-header py-4" id="heading-2-6" data-toggle="collapse" role="button" data-target="#collapse-2-6" aria-expanded="false" aria-controls="collapse-2-6">
                                        <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Paystack') }}</h6>
                                    </div>
                                    <div id="collapse-2-6" class="collapse" aria-labelledby="heading-2-6" data-parent="#accordion-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 py-2">
                                                    <h5 class="h5">{{ __('Paystack') }}</h5>
                                                    
                                                </div>
                                                <div class="col-6 py-2 text-right">
                                                    <div class="custom-control custom-switch text-right">
                                                        <input type="hidden" name="is_paystack_enabled" value="off">
                                                        <input type="checkbox" class="custom-control-input" name="is_paystack_enabled" id="is_paystack_enabled" {{ isset($payment['is_paystack_enabled']) && $payment['is_paystack_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-control-label" for="is_paystack_enabled">{{ __('Enable Paystack') }} </label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="paypal_client_id">{{ __('Public Key')}}</label>
                                                        <input type="text" name="paystack_public_key" id="paystack_public_key" class="form-control" value="{{(!isset($payment['paystack_public_key']) || is_null($payment['paystack_public_key'])) ? '' : $payment['paystack_public_key']}}" placeholder="{{ __('Public Key')}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="paystack_secret_key">{{ __('Secret Key') }}</label>
                                                        <input type="text" name="paystack_secret_key" id="paystack_secret_key" class="form-control" value="{{(!isset($payment['paystack_secret_key']) || is_null($payment['paystack_secret_key'])) ? '' : $payment['paystack_secret_key']}}" placeholder="{{ __('Secret Key') }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- FLUTTERWAVE -->
                                <div class="card">
                                    <div class="card-header py-4" id="heading-2-7" data-toggle="collapse" role="button" data-target="#collapse-2-7" aria-expanded="false" aria-controls="collapse-2-7">
                                        <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Flutterwave') }}</h6>
                                    </div>
                                    <div id="collapse-2-7" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 py-2">
                                                    <h5 class="h5">{{ __('Flutterwave') }}</h5>
                                                    
                                                </div>
                                                <div class="col-6 py-2 text-right">
                                                    <div class="custom-control custom-switch text-right">
                                                        <input type="hidden" name="is_flutterwave_enabled" value="off">
                                                        <input type="checkbox" class="custom-control-input" name="is_flutterwave_enabled" id="is_flutterwave_enabled" {{ isset($payment['is_flutterwave_enabled']) && $payment['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-control-label" for="is_flutterwave_enabled">{{ __('Enable Flutterwave') }}</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="paypal_client_id">{{ __('Public Key')}}</label>
                                                        <input type="text" name="flutterwave_public_key" id="flutterwave_public_key" class="form-control" value="{{(!isset($payment['flutterwave_public_key']) || is_null($payment['flutterwave_public_key'])) ? '' : $payment['flutterwave_public_key']}}" placeholder="Public Key">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="paystack_secret_key">{{ __('Secret Key') }}</label>
                                                        <input type="text" name="flutterwave_secret_key" id="flutterwave_secret_key" class="form-control" value="{{(!isset($payment['flutterwave_secret_key']) || is_null($payment['flutterwave_secret_key'])) ? '' : $payment['flutterwave_secret_key']}}" placeholder="Secret Key">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Razorpay -->
                                <div class="card">
                                    <div class="card-header py-4" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-8" aria-expanded="false" aria-controls="collapse-2-8">
                                        <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Razorpay') }}</h6>
                                    </div>
                                    <div id="collapse-2-8" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 py-2">
                                                    <h5 class="h5">{{ __('Razorpay') }}</h5>
                                                    
                                                </div>
                                                <div class="col-6 py-2 text-right">
                                                    <div class="custom-control custom-switch text-right">
                                                        <input type="hidden" name="is_razorpay_enabled" value="off">
                                                        <input type="checkbox" class="custom-control-input" name="is_razorpay_enabled" id="is_razorpay_enabled" {{ isset($payment['is_razorpay_enabled']) && $payment['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-control-label" for="is_razorpay_enabled">Enable Razorpay</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="paypal_client_id">Public Key</label>

                                                        <input type="text" name="razorpay_public_key" id="razorpay_public_key" class="form-control" value="{{(!isset($payment['razorpay_public_key']) || is_null($payment['razorpay_public_key'])) ? '' : $payment['razorpay_public_key']}}" placeholder="Public Key">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="paystack_secret_key">Secret Key</label>
                                                        <input type="text" name="razorpay_secret_key" id="razorpay_secret_key" class="form-control" value="{{(!isset($payment['razorpay_secret_key']) || is_null($payment['razorpay_secret_key'])) ? '' : $payment['razorpay_secret_key']}}" placeholder="Secret Key">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Paytm -->
                                <div class="card">
                                    <div class="card-header py-4" id="heading-2-14" data-toggle="collapse" role="button" data-target="#collapse-2-14" aria-expanded="false" aria-controls="collapse-2-14">
                                        <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Paytm') }}</h6>
                                    </div>
                                    <div id="collapse-2-14" class="collapse" aria-labelledby="heading-2-14" data-parent="#accordion-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 py-2">
                                                    <h5 class="h5">{{ __('Paytm') }}</h5>
                                                    
                                                </div>
                                                <div class="col-6 py-2 text-right">
                                                    <div class="custom-control custom-switch text-right">
                                                        <input type="hidden" name="is_paytm_enabled" value="off">
                                                        <input type="checkbox" class="custom-control-input" name="is_paytm_enabled" id="is_paytm_enabled" {{ isset($payment['is_paytm_enabled']) && $payment['is_paytm_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-control-label" for="is_paytm_enabled">Enable Paytm</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pb-4">
                                                    <label class="paypal-label form-control-label" for="paypal_mode">Paytm Environment</label> <br>
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label class="btn btn-primary btn-sm {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'active' : '' }}">
                                                            <input type="radio" name="paytm_mode" value="local" {{ !isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'checked="checked"' : '' }}>Local
                                                        </label>
                                                        <label class="btn btn-primary btn-sm {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'active' : '' }}">
                                                            <input type="radio" name="paytm_mode" value="production" {{ isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'checked="checked"' : '' }}>Production
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="paytm_public_key">Merchant ID</label>
                                                        <input type="text" name="paytm_merchant_id" id="paytm_merchant_id" class="form-control" value="{{(!isset($payment['paytm_merchant_id']) || is_null($payment['paytm_merchant_id'])) ? '' : $payment['paytm_merchant_id']}}" placeholder="Merchant ID">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="paytm_secret_key">Merchant Key</label>
                                                        <input type="text" name="paytm_merchant_key" id="paytm_merchant_key" class="form-control" value="{{(!isset($payment['paytm_merchant_key']) || is_null($payment['paytm_merchant_key'])) ? '' : $payment['paytm_merchant_key']}}" placeholder="Merchant Key">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="paytm_industry_type">Industry Type</label>
                                                        <input type="text" name="paytm_industry_type" id="paytm_industry_type" class="form-control" value="{{(!isset($payment['paytm_industry_type']) || is_null($payment['paytm_industry_type'])) ? '' : $payment['paytm_industry_type']}}" placeholder="Industry Type">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mercado Pago-->
                                <div class="card">
                                    <div class="card-header py-4" id="heading-2-12" data-toggle="collapse" role="button" data-target="#collapse-2-12" aria-expanded="false" aria-controls="collapse-2-12">
                                        <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Mercado Pago') }}</h6>
                                    </div>
                                    <div id="collapse-2-12" class="collapse" aria-labelledby="heading-2-12" data-parent="#accordion-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 py-2">
                                                    <h5 class="h5">{{ __('Mercado Pago') }}</h5>
                                                </div>
                                                <div class="col-6 py-2 text-right">
                                                    <div class="custom-control custom-switch text-right">
                                                        <input type="hidden" name="is_mercado_enabled" value="off">
                                                        <input type="checkbox" class="custom-control-input" name="is_mercado_enabled" id="is_mercado_enabled" {{ isset($payment['is_mercado_enabled']) && $payment['is_mercado_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-control-label" for="is_mercado_enabled">Enable Mercado Pago</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="mercado_app_id">App ID</label>
                                                        <input type="text" name="mercado_app_id" id="mercado_app_id" class="form-control" value="{{(!isset($payment['mercado_app_id']) || is_null($payment['mercado_app_id'])) ? '' : $payment['mercado_app_id']}}" placeholder="App ID">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="mercado_secret_key">App Secret KEY</label>
                                                        <input type="text" name="mercado_secret_key" id="mercado_secret_key" class="form-control" value="{{(!isset($payment['mercado_secret_key']) || is_null($payment['mercado_secret_key'])) ? '' : $payment['mercado_secret_key']}}" placeholder="App Secret Key">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mollie -->
                                <div class="card">
                                    <div class="card-header py-4" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-10" aria-expanded="false" aria-controls="collapse-2-10">
                                        <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Mollie') }}</h6>
                                    </div>
                                    <div id="collapse-2-10" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 py-2">
                                                    <h5 class="h5">{{ __('Mollie') }}</h5>
                                                    
                                                </div>
                                                <div class="col-6 py-2 text-right">
                                                    <div class="custom-control custom-switch text-right">
                                                        <input type="hidden" name="is_mollie_enabled" value="off">
                                                        <input type="checkbox" class="custom-control-input" name="is_mollie_enabled" id="is_mollie_enabled" {{ isset($payment['is_mollie_enabled']) && $payment['is_mollie_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-control-label" for="is_mollie_enabled">Enable Mollie</label>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="mollie_api_key">Mollie Api Key</label>
                                                        <input type="text" name="mollie_api_key" id="mollie_api_key" class="form-control" value="{{(!isset($payment['mollie_api_key']) || is_null($payment['mollie_api_key'])) ? '' : $payment['mollie_api_key']}}" placeholder="Mollie Api Key">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="mollie_profile_id">Mollie Profile Id</label>
                                                        <input type="text" name="mollie_profile_id" id="mollie_profile_id" class="form-control" value="{{(!isset($payment['mollie_profile_id']) || is_null($payment['mollie_profile_id'])) ? '' : $payment['mollie_profile_id']}}" placeholder="Mollie Profile Id">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="mollie_partner_id">Mollie Partner Id</label>
                                                        <input type="text" name="mollie_partner_id" id="mollie_partner_id" class="form-control" value="{{(!isset($payment['mollie_partner_id']) || is_null($payment['mollie_partner_id'])) ? '' : $payment['mollie_partner_id']}}" placeholder="Mollie Partner Id">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Skrill -->
                                <div class="card">
                                    <div class="card-header py-4" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-13" aria-expanded="false" aria-controls="collapse-2-10">
                                        <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('Skrill') }}</h6>
                                    </div>
                                    <div id="collapse-2-13" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 py-2">
                                                    <h5 class="h5">{{ __('Skrill') }}</h5>
                                                    
                                                </div>
                                                <div class="col-6 py-2 text-right">
                                                    <div class="custom-control custom-switch text-right">
                                                        <input type="hidden" name="is_skrill_enabled" value="off">
                                                        <input type="checkbox" class="custom-control-input" name="is_skrill_enabled" id="is_skrill_enabled" {{ isset($payment['is_skrill_enabled']) && $payment['is_skrill_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-control-label" for="is_skrill_enabled">Enable Skrill</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="mollie_api_key">Skrill Email</label>
                                                        <input type="text" name="skrill_email" id="skrill_email" class="form-control" value="{{(!isset($payment['skrill_email']) || is_null($payment['skrill_email'])) ? '' : $payment['skrill_email']}}" placeholder="Enter Skrill Email">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CoinGate -->
                                <div class="card">
                                    <div class="card-header py-4 collapsed" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-15" aria-expanded="false" aria-controls="collapse-2-10">
                                        <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i>{{ __('CoinGate') }}</h6>
                                    </div>
                                    <div id="collapse-2-15" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2" style="">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 py-2">
                                                    <h5 class="h5">{{ __('CoinGate') }}</h5>
                                                </div>
                                                <div class="col-6 py-2 text-right">
                                                    <div class="custom-control custom-switch text-right">
                                                        <input type="hidden" name="is_coingate_enabled" value="off">
                                                        <input type="checkbox" class="custom-control-input" name="is_coingate_enabled" id="is_coingate_enabled" {{ isset($payment['is_coingate_enabled']) && $payment['is_coingate_enabled'] == 'on' ? 'checked="checked"' : '' }}>
                                                        <label class="custom-control-label form-control-label" for="is_coingate_enabled">Enable CoinGate</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pb-4">
                                                    <label class="coingate-label form-control-label" for="coingate_mode">CoinGate Mode</label> <br>
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label class="btn btn-primary btn-sm {{ !isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'active' : '' }}">
                                                            <input type="radio" name="coingate_mode" value="sandbox" {{ !isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'checked="checked"' : '' }}>Sandbox
                                                        </label>
                                                        <label class="btn btn-primary btn-sm {{ isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'active' :'' }}">
                                                            <input type="radio" name="coingate_mode" value="live" {{ isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'checked="checked"' : '' }}>Live
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="coingate_auth_token">CoinGate Auth Token</label>
                                                        <input type="text" name="coingate_auth_token" id="coingate_auth_token" class="form-control" value="{{(!isset($payment['coingate_auth_token']) || is_null($payment['coingate_auth_token'])) ? '' : $payment['coingate_auth_token']}}" placeholder="CoinGate Auth Token">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-right">
                                <div class="form-group">
                                    <input class="btn-create badge-blue" type="submit" value="{{__('Save Changes')}}">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="pusher-setting" role="tabpanel">
                        {{Form::open(array('route'=>'pusher.settings','method'=>'post'))}}
                        <div class="card bg-none">
                            <div class="row company-setting">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" name="enable_chat" value="yes" class="custom-control-input" id="enable_chat" @if(env('CHAT_MODULE') =='yes') checked @endif>
                                            <label class="custom-control-label font-weight-bold text-dark text-sm" for="enable_chat">{{ __('Enable Chat') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('pusher_app_id',__('Pusher App Id'),['class'=>'form-control-label']) }}
                                    {{Form::text('pusher_app_id',env('PUSHER_APP_ID'),array('class'=>'form-control','placeholder'=>__('Enter Pusher App Id')))}}
                                    @error('pusher_app_id')
                                    <span class="text-xs text-danger invalid-pusher_app_id" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('pusher_app_key',__('Pusher App Key'),['class'=>'form-control-label']) }}
                                    {{Form::text('pusher_app_key',env('PUSHER_APP_KEY'),array('class'=>'form-control ','placeholder'=>__('Enter Pusher App Key')))}}
                                    @error('pusher_app_key')
                                    <span class="text-xs text-danger invalid-pusher_app_key" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('pusher_app_secret',__('Pusher App Secret'),['class'=>'form-control-label']) }}
                                    {{Form::text('pusher_app_secret',env('PUSHER_APP_SECRET'),array('class'=>'form-control ','placeholder'=>__('Enter Pusher App Secret')))}}
                                    @error('pusher_app_secret')
                                    <span class="text-xs text-danger invalid-pusher_app_secret" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                    {{Form::label('pusher_app_cluster',__('Pusher App Cluster'),['class'=>'form-control-label']) }}
                                    {{Form::text('pusher_app_cluster',env('PUSHER_APP_CLUSTER'),array('class'=>'form-control ','placeholder'=>__('Enter Pusher App Cluster')))}}
                                    @error('pusher_app_cluster')
                                    <span class="text-xs text-danger invalid-pusher_app_cluster" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-12 text-right">
                                    {{Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))}}
                                </div>
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
