@extends('layouts.admin')
@push('script-page')
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            
        });

        $(document).on('click', '.apply-coupon', function (e) {
            e.preventDefault();
            var where = $(this).attr('data-from');
            applyCoupon($('#' + where + '_coupon').val(), where);
        })

        function applyCoupon(coupon_code, where) {
          
            if (coupon_code != null && coupon_code != '') {
                $.ajax({
                    url: '{{route('apply.coupon')}}',
                    datType: 'json',
                    data: {
                        plan_id: '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}',
                        coupon: coupon_code
                    },
                    success: function (data) {
                        if (data.is_success) {
                            $('.' + where + '-coupon-tr').show().find('.' + where + '-coupon-price').text(data.discount_price);
                            $('.' + where + '-final-price').text(data.final_price);
                            // show_toastr('Success', data.message, 'success');
                        } else {
                            $('.' + where + '-coupon-tr').hide().find('.' + where + '-coupon-price').text('');
                            $('.' + where + '-final-price').text(data.final_price);
                            show_toastr('Error', data.message, 'error');
                        }
                    }
                })
            } else {
                show_toastr('Error', '{{__('Invalid Coupon Code.')}}', 'error');
                $('.' + where + '-coupon-tr').hide().find('.' + where + '-coupon-price').text('');
            }
        }
    </script>

    @if(isset($admin_payment_setting['is_stripe_enabled']) && $admin_payment_setting['is_stripe_enabled']== 'on' && !empty($admin_payment_setting['stripe_key']) && !empty($admin_payment_setting['stripe_secret']))
        
        <?php $stripe_session = Session::get('stripe_session');?>
        <?php if(isset($stripe_session) && $stripe_session): ?>
        <script>
            var stripe = Stripe('{{ $admin_payment_setting['stripe_key'] }}');
            stripe.redirectToCheckout({
                sessionId: '{{ $stripe_session->id }}',
            }).then((result) => {
                console.log(result);
            });
        </script>
        <?php endif ?>
    @endif

    @if(isset($admin_payment_setting['is_paystack_enabled']) && $admin_payment_setting['is_paystack_enabled'] == 'on')

        <script src="https://js.paystack.co/v1/inline.js"></script>
    
        <script>
            $(document).on("click", "#pay_with_paystack", function () {

                $('#paystack-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){
                        var coupon_id = res.coupon;

                        var paystack_callback = "{{ url('/plan/paystack') }}";
                        var order_id = '{{time()}}';
                        var handler = PaystackPop.setup({
                            key: '{{ $admin_payment_setting['paystack_public_key']  }}',
                            email: res.email,
                            amount: res.total_price*100,
                            currency: res.currency,
                            ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                1
                            ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                            metadata: {
                                custom_fields: [{
                                    display_name: "Email",
                                    variable_name: "email",
                                    value: res.email,
                                }]
                            },

                            callback: function(response) {
                                console.log(response.reference,order_id);
                                window.location.href = paystack_callback +'/' + response.reference+'/'+'{{encrypt($plan->id)}}'+'?coupon_id=' + coupon_id
                            },
                            onClose: function() {
                                alert('window closed');
                            }
                        });
                        handler.openIframe();
                    }else if(res.flag == 2){

                    }else{
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        </script>
    @endif

    @if(isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on')
        <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    
        <script>

        //    Flaterwave Payment
        $(document).on("click", "#pay_with_flaterwave", function () {
          
            $('#flaterwave-payment-form').ajaxForm(function (res) {
                if(res.flag == 1){
                    var coupon_id = res.coupon;
                    var API_publicKey = '';
                    if("{{ isset($admin_payment_setting['flutterwave_public_key'] ) }}"){
                        API_publicKey = "{{$admin_payment_setting['flutterwave_public_key']}}";
                    }
                    var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                    var flutter_callback = "{{ url('/plan/flaterwave') }}";
                    var x = getpaidSetup({
                        PBFPubKey: API_publicKey,
                        customer_email: '{{Auth::user()->email}}',
                        amount: res.total_price,
                        currency: res.currency,
                        txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) + 'fluttpay_online-' +
                            {{ date('Y-m-d') }},
                        meta: [{
                            metaname: "payment_id",
                            metavalue: "id"
                        }],
                        onclose: function () {
                        },
                        callback: function (response) {
                            var txref = response.tx.txRef;
                            if (
                                response.tx.chargeResponseCode == "00" ||
                                response.tx.chargeResponseCode == "0"
                            ) {
                                window.location.href = flutter_callback + '/' + txref + '/' + '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}?coupon_id=' + coupon_id+'&payment_frequency='+res.payment_frequency;
                            } else {
                                // redirect to a failure page.
                            }
                            x.close(); // use this to close the modal immediately after payment.
                        }});
                }else if(res.flag == 2){

                }else{
                    show_toastr('Error', data.message, 'msg');
                }

            }).submit();
        });
        </script>
    @endif

    @if(isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on')
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>

            // Razorpay Payment
            $(document).on("click", "#pay_with_razorpay", function () {
                $('#razorpay-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){

                        var razorPay_callback = '{{url('/plan/razorpay')}}';
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if("{{isset( $admin_payment_setting['razorpay_public_key']  )}}"){
                            API_publicKey = "{{$admin_payment_setting['razorpay_public_key']}}";
                        }
                        var options = {
                            "key": API_publicKey, // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Plan',
                            "currency": res.currency,
                            "description": "",
                            "handler": function (response) {
                                window.location.href = razorPay_callback + '/' + response.razorpay_payment_id + '/' + '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}?coupon_id=' + coupon_id+'&payment_frequency='+res.payment_frequency;
                            },
                            "theme": {
                                "color": "#528FF0"
                            }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    }else if(res.flag == 2){

                    }else{
                        show_toastr('Error', data.message, 'msg');
                    }

                }).submit();
            });
        </script>
    @endif
@endpush
@php
    $dir= asset(Storage::url('plan'));
    $dir_payment= asset(Storage::url('payments'));
@endphp
@section('page-title')
    {{__('Order Summary')}}
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-4 col-xl-3 col-md-6 col-sm-6">
            <div class="plan-3">
                <h6 class="text-center">{{ $plan->name }}</h6>
                <p class="price">
                    <sup>{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}</sup>
                    {{ number_format($plan->price) }}
                    <sub>/ {{$plan->duration}}</sub>
                </p>
                <ul class="plan-detail">
                    <li>{{ ($plan->max_users < 0) ? __('Unlimited'):$plan->max_users }} {{__('Users')}}</li>
                    <li>{{ ($plan->max_clients < 0) ? __('Unlimited'):$plan->max_clients }} {{__('Clients')}}</li>
                    <li>{{ ($plan->max_projects < 0) ? __('Unlimited'):$plan->max_projects }} {{__('Projects')}}</li>
                </ul>
                @if($plan->description)
                    <p class="server-plan">
                        {{$plan->description}}
                    </p>
                @endif
            </div>
        </div>
        <div class="col-lg-8 col-xl-9 col-md-6 col-sm-6">
            @if($plan->price <= 0.0)
            {{ Form::open(['route' => ['avtivePlan'], 'id' => 'payment-form', 'class' => 'require-validation', 'method'=>'post']) }}
            <input type="hidden" name="plan_id" value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
            <input type="hidden" name="name" value="{{$plan->name}}">
            {{Form::submit(__('Activate Free Plan'),['class'=>'btn btn-primary'])}}
            {{ Form::close() }}
            @else
                <div class="card">
                    <div class="card-body">
                       
                        <ul class="nav nav-tabs" role="tablist">
                            @if(isset($admin_payment_setting['is_stripe_enabled']) && $admin_payment_setting['is_stripe_enabled'] == 'on')
                            <li>
                                <a class="active" data-toggle="tab" href="#stripe-payment" role="tab" aria-controls="stripe" aria-selected="true">{{ __('Stripe') }}</a>
                            </li>
                            @endif
                            @if(isset($admin_payment_setting['is_paypal_enabled']) && $admin_payment_setting['is_paypal_enabled'] == 'on')
                            <li>
                                <a data-toggle="tab" href="#paypal-payment" role="tab" aria-controls="paypal" aria-selected="false">{{ __('Paypal') }}</a>
                            </li>
                            @endif
                            @if(isset($admin_payment_setting['is_paystack_enabled']) && $admin_payment_setting['is_paystack_enabled'] == 'on')
                            <li>
                                <a data-toggle="tab" href="#paystack-payment" role="tab" aria-controls="paystack" aria-selected="false">{{__('Paystack')}}</a>
                            </li>
                            @endif
                            @if(isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on')
                            <li>
                                <a data-toggle="tab" href="#flutterwave-payment" role="tab" aria-controls="flutterwave" aria-selected="false">{{__('Flutterwave')}}</a>
                            </li>
                            @endif
                            @if(isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on')
                            <li>
                                <a data-toggle="tab" href="#razorpay-payment" role="tab" aria-controls="razorpay" aria-selected="false">{{__('Razorpay')}}</a>
                            </li>
                            @endif
                            @if(isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on')
                            <li>
                                <a data-toggle="tab" href="#paytm-payment" role="tab" aria-controls="paytm" aria-selected="false">{{__('Paytm')}}</a>
                            </li>
                            @endif
                            @if(isset($admin_payment_setting['is_mercado_enabled']) && $admin_payment_setting['is_mercado_enabled'] == 'on')
                            <li>
                                <a data-toggle="tab" href="#mercadopago-payment" role="tab" aria-controls="mercadopago" aria-selected="false">{{__('Mercado Pago')}}</a>
                            </li>
                            @endif
                            @if(isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on')
                            <li>
                                <a data-toggle="tab" href="#mollie-payment" role="tab" aria-controls="mollie" aria-selected="false">{{__('Mollie')}}</a>
                            </li>
                            @endif
                            @if(isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on')
                            <li>
                                <a data-toggle="tab" href="#skrill-payment" role="tab" aria-controls="skrill" aria-selected="false">{{__('Skrill')}}</a>
                            </li>
                            @endif
                            @if(isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on')
                            <li>
                                <a data-toggle="tab" href="#coingate-payment" role="tab" aria-controls="coingate" aria-selected="false">{{__('Coingate')}}</a>
                            </li>
                            @endif
                        </ul>
                       
                        <div class="tab-content mt-3">

                            @if(isset($admin_payment_setting['is_stripe_enabled']) && $admin_payment_setting['is_stripe_enabled'] == 'on')
                                <div class="tab-pane fade {{ (isset($admin_payment_setting['is_stripe_enabled']) && $admin_payment_setting['is_stripe_enabled'] == 'on') ? "show active" : "" }}" id="stripe-payment" role="tabpanel" aria-labelledby="paypal-payment">
                                    <form role="form" action="{{ route('prepare.payment') }}" method="post" class="require-validation" id="stripe-payment-form">
                                        @csrf
                                        <br><h5 class="h6 mb-1">{{__('Pay Using Stripe')}}</h5>
                                        <div class="py-3 stripe-payment-div">
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <div class="form-group">
                                                        <label for="stripe_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="stripe_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group apply-stripe-btn-coupon">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon"  data-from="stripe">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right stripe-coupon-tr" style="display: none">
                                                    <b>{{__('Coupon Discount')}}</b> : <b class="stripe-coupon-price"></b>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="text-sm-right">
                                                <input type="hidden" id="stripe" value="stripe" name="payment_processor" class="custom-control-input">
                                                <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="submit">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}} (<span class="stripe-final-price">{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}{{$plan->price }}</span>)
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @if(isset($admin_payment_setting['is_paypal_enabled']) && $admin_payment_setting['is_paypal_enabled'] == 'on')
                                <div class="tab-pane fade" id="paypal-payment" role="tabpanel" aria-labelledby="paypal-payment">
                                    <form role="form" action="{{ route('plan.pay.with.paypal') }}" method="post" class="require-validation" id="paypal-payment-form">
                                        @csrf
                                        <br><h5 class="h6 mb-1">{{__('Pay Using Paypal')}}</h5>
                                        <div class="py-3 paypal-payment-div">
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <div class="form-group">
                                                        <label for="paypal_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="paypal_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}" >
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group apply-paypal-btn-coupon">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon" data-from="paypal">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right paypal-coupon-tr" style="display: none">
                                                    <b>{{__('Coupon Discount')}}</b> : <b class="paypal-coupon-price"></b>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="text-sm-right">
                                                <input type="hidden" id="paypal" value="paypal" name="payment_processor" class="custom-control-input">
                                                <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="submit">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}} (<span class="paypal-final-price">{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}{{$plan->price }}</span>)
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @if(isset($admin_payment_setting['is_paystack_enabled']) && $admin_payment_setting['is_paystack_enabled'] == 'on')

                                <div class="tab-pane fade" id="paystack-payment" role="tabpanel" aria-labelledby="paypal-payment">
                                    <form role="form" action="{{ route('plan.pay.with.paystack') }}" method="post" class="require-validation" id="paystack-payment-form">
                                        @csrf
                                        <br><h5 class="h6 mb-1">{{__('Pay Using Paystack')}}</h5>
                                        <div class="py-3 paystack-payment-div">
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <div class="form-group">
                                                        <label for="paystack_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="paystack_coupon" name="coupon" class="form-control coupon" data-from="paystack" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group pt-3 mt-3">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon" data-from="paystack">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right paystack-coupon-tr" style="display: none">
                                                    <b>{{__('Coupon Discount')}}</b> : <b class="paystack-coupon-price"></b>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="error" style="display: none;">
                                                        <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="text-sm-right">
                                                <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="button" id="pay_with_paystack">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}} (<span class="paystack-final-price">{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}{{$plan->price }}</span>)</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                            
                            @if(isset($admin_payment_setting['is_flutterwave_enabled']) && $admin_payment_setting['is_flutterwave_enabled'] == 'on')


                                <div class="tab-pane fade" id="flutterwave-payment" role="tabpanel" aria-labelledby="flutterwave-payment">

                                    <form role="form" action="{{ route('plan.pay.with.flaterwave') }}" method="post" class="require-validation" id="flaterwave-payment-form">
                                        @csrf
                                        <br><h5 class=" h6 mb-0">{{__('Pay Using Flutterwave')}}</h5>
                                        <div class="py-3 paypal-payment-div">
                                            <div class="row">
                                                
                                                <div class="col-md-11">
                                                    <div class="form-group">
                                                        <label for="flaterwave_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="flaterwave_coupon" name="coupon" class="form-control coupon" data-from="flaterwave" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group pt-3 mt-3">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon" data-from="flaterwave">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right flaterwave-coupon-tr" style="display: none">
                                                    <b>{{__('Coupon Discount')}}</b> : <b class="flaterwave-coupon-price"></b>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="error" style="display: none;">
                                                        <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="text-sm-right">
                                                <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="button" id="pay_with_flaterwave">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}} (<span class="flaterwave-final-price">{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}{{$plan->price }}</span>)
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            @endif

                            @if(isset($admin_payment_setting['is_razorpay_enabled']) && $admin_payment_setting['is_razorpay_enabled'] == 'on')

                                <div class="tab-pane fade" id="razorpay-payment" role="tabpanel" aria-labelledby="razorpay-payment">
                                    <form role="form" action="{{ route('plan.pay.with.razorpay') }}" method="post" class="require-validation" id="razorpay-payment-form">
                                        @csrf
                                        <br><h5 class="h6 mb-1">{{__('Pay Using Paystack')}}</h5>
                                        <div class="py-3 razorpay-payment-div">
                                            <div class="row">
                                                
                                                <div class="col-11">
                                                    <div class="form-group">
                                                        <label for="razorpay_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="razorpay_coupon" name="coupon" class="form-control coupon" data-from="razorpay" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group pt-3 mt-3">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon" data-from="razorpay">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right razorpay-coupon-tr" style="display: none">
                                                    <b>{{__('Coupon Discount')}}</b> : <b class="razorpay-coupon-price"></b>
                                                </div>
                                                
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="error" style="display: none;">
                                                        <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                           
                                            <div class="text-sm-right">
                                                <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="button" id="pay_with_razorpay">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}} (<span class="razorpay-final-price">{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}{{ $plan->price }}</span>)
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif


                            @if(isset($admin_payment_setting['is_paytm_enabled']) && $admin_payment_setting['is_paytm_enabled'] == 'on')
                                <div class="tab-pane fade" id="paytm-payment" role="tabpanel" aria-labelledby="paytm-payment">

                                    <form role="form" action="{{ route('plan.pay.with.paytm') }}" method="post" class="require-validation" id="paytm-payment-form">
                                        @csrf
                                        <div class="py-3 paypal-payment-div">
                                            <div class="row">
                                                <div class="col-11">
                                                    <div class="form-group">
                                                        <label for="paytm_coupon" class="form-control-label text-dark">{{__('Mobile Number')}}</label>
                                                        <input type="text" id="mobile" name="mobile" class="form-control mobile" data-from="mobile" placeholder="{{ __('Enter Mobile Number') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-11">
                                                    <div class="form-group">
                                                        <label for="paytm_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="paytm_coupon" name="coupon" class="form-control coupon" data-from="paytm" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group pt-3 mt-3">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon" data-from="paytm">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right paytm-coupon-tr" style="display: none">
                                                    <b>{{__('Coupon Discount')}}</b> : <b class="paytm-coupon-price"></b>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="error" style="display: none;">
                                                        <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            
                                            <div class="text-sm-right">
                                                <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="submit" id="pay_with_paytm">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}} (<span class="paytm-final-price">{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}{{$plan->price }}</span>)
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @if(isset($admin_payment_setting['is_mercado_enabled']) && $admin_payment_setting['is_mercado_enabled'] == 'on')
                                <div class="tab-pane fade" id="mercadopago-payment" role="tabpanel" aria-labelledby="mercadopago-payment">

                                    <form role="form" action="{{ route('plan.pay.with.mercado') }}" method="post" class="require-validation" id="mercado-payment-form">
                                        @csrf
                                        <div class="py-3 mercado-payment-div">
                                            <div class="row">
                                                <div class="col-11">
                                                    <div class="form-group">
                                                        <label for="mercado_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="mercado_coupon" name="coupon" class="form-control coupon" data-from="mercado" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group pt-3 mt-3">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon" data-from="mercado">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right mercado-coupon-tr" style="display: none">
                                                    <b>{{__('Coupon Discount')}}</b> : <b class="mercado-coupon-price"></b>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="error" style="display: none;">
                                                        <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            
                                            <div class="text-sm-right">
                                                <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="submit" id="pay_with_paytm">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}} (<span class="mercado-final-price">{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}{{$plan->price }}</span>)
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @if(isset($admin_payment_setting['is_mollie_enabled']) && $admin_payment_setting['is_mollie_enabled'] == 'on')

                                <div class="tab-pane fade" id="mollie-payment" role="tabpanel" aria-labelledby="mollie-payment">
                                    <form role="form" action="{{ route('plan.pay.with.mollie') }}" method="post" class="require-validation" id="mollie-payment-form">
                                        @csrf
                                        <div class="py-3 mollie-payment-div">
                                            <div class="row">
                                                <div class="col-11">
                                                    <div class="form-group">
                                                        <label for="mollie_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="mollie_coupon" name="coupon" class="form-control coupon" data-from="mollie" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group pt-3 mt-3">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon" data-from="mollie">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right mollie-coupon-tr" style="display: none">
                                                    <b>{{__('Coupon Discount')}}</b> : <b class="mollie-coupon-price"></b>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="error" style="display: none;">
                                                        <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            
                                            <div class="text-sm-right">
                                                <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="submit" id="pay_with_mollie">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}} (<span class="mollie-final-price">{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}{{$plan->price }}</span>)
                                                </button>
                                            </div>
                                      
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @if(isset($admin_payment_setting['is_skrill_enabled']) && $admin_payment_setting['is_skrill_enabled'] == 'on')

                                <div class="tab-pane fade" id="skrill-payment" role="tabpanel" aria-labelledby="skrill-payment">
                                    <form role="form" action="{{ route('plan.pay.with.skrill') }}" method="post" class="require-validation" id="skrill-payment-form">
                                        @csrf
                                        <div class="py-3 skrill-payment-div">
                                            <div class="row">
                                                <div class="col-11">
                                                    <div class="form-group">
                                                        <label for="skrill_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="skrill_coupon" name="coupon" class="form-control coupon" data-from="skrill" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group pt-3 mt-3">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon" data-from="skrill">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right skrill-coupon-tr" style="display: none">
                                                    <b>{{__('Coupon Discount')}}</b> : <b class="skrill-coupon-price"></b>
                                                </div>
                                            </div>
                                            @php
                                                $skrill_data = [
                                                    'transaction_id' => md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id'),
                                                    'user_id' => 'user_id',
                                                    'amount' => 'amount',
                                                    'currency' => 'currency',
                                                ];
                                                session()->put('skrill_data', $skrill_data);
                                            @endphp
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="error" style="display: none;">
                                                        <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                           
                                            <div class="text-sm-right">
                                                <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="submit" id="pay_with_skrill">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}} (<span class="skrill-final-price">{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}{{ $plan->price }}</span>)
                                                </button>
                                            </div>
                                            
                                        </div>
                                    </form>
                                </div>
                            @endif

                            @if(isset($admin_payment_setting['is_coingate_enabled']) && $admin_payment_setting['is_coingate_enabled'] == 'on')

                                <div class="tab-pane fade" id="coingate-payment" role="tabpanel" aria-labelledby="coingate-payment">
                                    <form role="form" action="{{ route('plan.pay.with.coingate') }}" method="post" class="require-validation" id="coingate-payment-form">
                                        @csrf
                                        <div class="py-3 coingate-payment-div">
                                            <div class="row">
                                                <div class="col-11">
                                                    <div class="form-group">
                                                        <label for="coingate_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="coingate_coupon" name="coupon" class="form-control coupon" data-from="coingate" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group pt-3 mt-3">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon" data-from="coingate">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right coingate-coupon-tr" style="display: none">
                                                    <b>{{__('Coupon Discount')}}</b> : <b class="coingate-coupon-price"></b>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="error" style="display: none;">
                                                        <div class='alert-danger alert'>{{__('Please correct the errors and try again.')}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                           
                                            <div class="text-sm-right">
                                                <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="submit" id="pay_with_coingate">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}} (<span class="coingate-final-price">{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')}}{{ $plan->price }}</span>)
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection



