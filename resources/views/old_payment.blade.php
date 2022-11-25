@extends('layouts.admin')
@push('script-page')
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(document).on('click', '.apply-coupon', function () {

                var ele = $(this);
                var coupon = ele.closest('.row').find('.coupon').val();
                if (coupon != '') {
                    $.ajax({
                        url: '{{route('apply.coupon')}}',
                        datType: 'json',
                        data: {
                            plan_id: '{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}',
                            coupon: coupon
                        },
                        success: function (data) {
                            $('.final-price').text(data.final_price);
                            $('#stripe_coupon, #paypal_coupon').val(coupon);
                            if (data.is_success) {
                                show_toastr('{{__("Success")}}', data.message, 'success');
                            } else {
                                show_toastr('{{__("Error")}}', data.message, 'error');
                            }
                        }
                    })
                } else {
                    show_toastr('{{__("Error")}}', '{{ __('This coupon code is invalid or has expired.') }}', 'error');
                }
            });
        });

            @if($plan->price > 0.0 && env('ENABLE_STRIPE') == 'on' && !empty(env('STRIPE_KEY')) && !empty(env('STRIPE_SECRET')))

        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();

        // Custom styling can be passed to options when creating an Element.
        var style = {
            base: {
                // Add your base input styles here. For example:
                fontSize: '14px',
                color: '#32325d',
            },
        };

        // Create an instance of the card Element.
        var card = elements.create('card', {style: style});

        // Add an instance of the card Element into the `card-element` <div>.
        card.mount('#card-element');

        // Create a token or display an error when the form is submitted.
        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            stripe.createToken(card).then(function (result) {
                if (result.error) {
                    $("#card-errors").html(result.error.message);
                } else {
                    // Send the token to your server.
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            // Insert the token ID into the form so it gets submitted to the server
            var form = document.getElementById('payment-form');
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);

            // Submit the form
            form.submit();
        }
        @endif
    </script>
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
            <div class="card">
                <div class="card-body">
                    @if($plan->price <= 0.0)
                        {{ Form::open(['route' => ['stripe.post'], 'id' => 'payment-form', 'class' => 'require-validation', 'method'=>'post']) }}
                        <input type="hidden" name="plan_id" value="{{ \Illuminate\Support\Facades\Crypt::encrypt($plan->id) }}">
                        {{Form::submit(__('Activate Free Plan'),['class'=>'btn btn-primary'])}}
                        {{ Form::close() }}
                    @else
                        @if((env('ENABLE_STRIPE') == 'on' && !empty(env('STRIPE_KEY')) && !empty(env('STRIPE_SECRET'))) && (env('ENABLE_PAYPAL') == 'on' && !empty(env('PAYPAL_CLIENT_ID')) && !empty(env('PAYPAL_SECRET_KEY'))))
                            <ul class="nav nav-tabs" role="tablist">
                                <li>
                                    <a class="active" data-toggle="tab" href="#stripe-payment" role="tab" aria-controls="stripe" aria-selected="true">{{ __('Stripe') }}</a>
                                </li>
                                <li>
                                    <a data-toggle="tab" href="#paypal-payment" role="tab" aria-controls="paypal" aria-selected="false">{{ __('Paypal') }}</a>
                                </li>
                            </ul>
                        @endif
                        <div class="tab-content mt-3">
                            @if(env('ENABLE_STRIPE') == 'on' && !empty(env('STRIPE_KEY')) && !empty(env('STRIPE_SECRET')))
                                <div class="tab-pane fade {{ ((env('ENABLE_STRIPE') == 'on' && env('ENABLE_PAYPAL') == 'on') || env('ENABLE_STRIPE') == 'on') ? "show active" : "" }}" id="stripe-payment" role="tabpanel" aria-labelledby="stripe-payment">
                                    <form role="form" action="{{ route('stripe.post') }}" method="post" class="require-validation" id="payment-form">
                                        @csrf
                                        <div class="py-3 stripe-payment-div">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <label class="font-weight-bold text-dark">{{__('Credit / Debit Card')}}</label>
                                                    <p class="text-sm">{{__('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.')}}</p>
                                                </div>
                                                <div class="col-sm-4 text-sm-right mt-3 mt-sm-0">
                                                    <img src="{{$dir_payment.'/master.png'}}" height="24" alt="master-card-img">
                                                    <img src="{{$dir_payment.'/discover.png'}}" height="24" alt="discover-card-img">
                                                    <img src="{{$dir_payment.'/visa.png'}}" height="24" alt="visa-card-img">
                                                    <img src="{{$dir_payment.'/american express.png'}}" height="24" alt="american-express-card-img">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="card-name-on" class="form-control-label text-dark">{{__('Name on card')}}</label>
                                                        <input type="text" name="name" id="card-name-on" class="form-control required" placeholder="{{\Auth::user()->name}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div id="card-element">
                                                        <!-- A Stripe Element will be inserted here. -->
                                                    </div>
                                                    <div id="card-errors" role="alert"></div>
                                                </div>
                                                <div class="col-md-11">
                                                    <br>
                                                    <div class="form-group">
                                                        <label for="stripe_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="stripe_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group apply-stripe-btn-coupon">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon">{{ __('Apply') }}</a>
                                                    </div>
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
                                        <div class="row mt-3">
                                            <div class="col-sm-12">
                                                <div class="text-sm-right">
                                                    <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                                    <button class="btn-create badge-blue rounded-pill text-sm" type="submit">
                                                        <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                            @if(env('ENABLE_PAYPAL') == 'on' && !empty(env('PAYPAL_CLIENT_ID')) && !empty(env('PAYPAL_SECRET_KEY')))
                                <div class="tab-pane fade {{ (env('ENABLE_STRIPE') == 'off' && env('ENABLE_PAYPAL') == 'on') ? "show active" : "" }}" id="paypal-payment" role="tabpanel" aria-labelledby="paypal-payment">
                                    <form method="POST" id="payment-form" action="{{ route('plan.pay.with.paypal') }}">
                                        @csrf
                                        <input type="hidden" name="plan_id" value="{{\Illuminate\Support\Facades\Crypt::encrypt($plan->id)}}">
                                        <div class="py-3">
                                            <div class="row">
                                                <div class="col-md-11">
                                                    <div class="form-group">
                                                        <label for="paypal_coupon" class="form-control-label text-dark">{{__('Coupon')}}</label>
                                                        <input type="text" id="paypal_coupon" name="coupon" class="form-control coupon" placeholder="{{ __('Enter Coupon Code') }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group apply-paypal-btn-coupon">
                                                        <a href="#" class="btn badge-blue btn-xs rounded-pill my-auto text-white apply-coupon">{{ __('Apply') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-3">
                                            <div class="text-sm-right">
                                                <button class="btn-create badge-blue rounded-pill text-sm" type="submit">
                                                    <i class="mdi mdi-cash-multiple mr-1"></i> {{__('Pay Now')}}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
