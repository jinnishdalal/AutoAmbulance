@extends('layouts.admin')

@section('page-title')
    {{__('Invoice Detail')}}
@endsection
@push('script-page')
    @php
        $settings = \App\Utility::settings();
        $dir_payment = asset(Storage::url('payments'));
    @endphp
    <script>
        function getTask(obj, project_id) {
            $('#task_id').empty();
            var milestone_id = obj.value;
            $.ajax({
                url: '{!! route('invoices.milestone.task') !!}',
                data: {
                    "milestone_id": milestone_id,
                    "project_id": project_id,
                    "_token": $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                success: function (data) {
                    $('#task_id').empty();
                    var html = '';
                    for (var i = 0; i < data.length; i++) {
                        html += '<option value=' + data[i].id + '>' + data[i].title + '</option>';
                    }
                    $('#task_id').append(html);
                    $('#task_id').select2('refresh');
                },
                error: function (data) {
                    data = data.responseJSON;
                    show_toastr('{{__("Error")}}', data.error, 'error')
                }
            });
        }

        function hide_show(obj) {
            if (obj.value == 'milestone') {
                document.getElementById('milestone').style.display = 'block';
                document.getElementById('other').style.display = 'none';
            } else {
                document.getElementById('other').style.display = 'block';
                document.getElementById('milestone').style.display = 'none';
            }
        }
    </script>

    @if(Auth::user()->type == 'client' && $invoice->getDue() > 0 && $settings['site_enable_stripe'] == 'on')
        <?php $stripe_session = Session::get('stripe_session');?>
        <?php if(isset($stripe_session) && $stripe_session): ?>
        <script src="https://js.stripe.com/v3/"></script>
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

    @if(Auth::user()->type == 'client' && $invoice->getDue() > 0 && isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on')

        <script src="https://js.paystack.co/v1/inline.js"></script>

        <script type="text/javascript">
            $(document).on("click", "#pay_with_paystack", function () {

                $('#paystack-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){
                        var coupon_id = res.coupon;

                        var paystack_callback = "{{ url('/invoice-pay-with-paystack') }}";
                        var order_id = '{{time()}}';
                        var handler = PaystackPop.setup({
                            key: '{{ $payment_setting['paystack_public_key']  }}',
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
                                window.location.href = "{{url('/invoice/paystack')}}/"+response.reference+"/{{encrypt($invoice->id)}}";
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

    @if(Auth::user()->type == 'client' && $invoice->getDue() > 0 && isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on')

        <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>

        <script type="text/javascript">

            //    Flaterwave Payment
            $(document).on("click", "#pay_with_flaterwave", function () {

                $('#flaterwave-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if("{{ isset($payment_setting['flutterwave_public_key'] ) }}"){
                            API_publicKey = "{{$payment_setting['flutterwave_public_key']}}";
                        }
                        var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                        var flutter_callback = "{{ url('/invoice-pay-with-flaterwave') }}";
                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: '{{Auth::user()->email}}',
                            amount: res.total_price,
                            currency: '{{$payment_setting['currency']}}',
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
                                if(response.tx.chargeResponseCode == "00" || response.tx.chargeResponseCode == "0") {
                                    window.location.href = "{{url('/invoice/flaterwave')}}/"+txref+"/{{encrypt($invoice->id)}}";
                                }else{
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

    @if(Auth::user()->type == 'client' && $invoice->getDue() > 0 && isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on')

        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

        <script type="text/javascript">
            // Razorpay Payment
            $(document).on("click", "#pay_with_razorpay", function () {
                $('#razorpay-payment-form').ajaxForm(function (res) {
                    
                    if(res.flag == 1){

                        var razorPay_callback = "{{url('/invoice-pay-with-razorpay')}}";
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if("{{isset($payment_setting['razorpay_public_key'])}}"){
                            API_publicKey = "{{$payment_setting['razorpay_public_key']}}";
                        }
                        var options = {
                            "key": API_publicKey, // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Invoice Payment',
                            "currency": '{{$payment_setting['currency']}}',
                            "description": "",
                            "handler": function (response) {
                                window.location.href = "{{url('/invoice/razorpay')}}/"+response.razorpay_payment_id +"/{{encrypt($invoice->id)}}";
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

@push('css-page')
    <style>
        #card-element {
            border: 1px solid #e4e6fc;
            border-radius: 5px;
            padding: 10px;
        }
    </style>
@endpush

@section('action-button')
    <div class="all-button-box row d-flex justify-content-end">
        @can('create invoice payment')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('invoices.payments.create',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Add Payment')}}"><i class="fas fa-plus"></i> {{__('Add Payment')}}</a>
            </div>
        @endcan
        @if(\Auth::user()->type == 'client' && $invoice->getDue() > 0 && (($settings['site_enable_stripe'] == 'on' && !empty($settings['site_stripe_key']) && !empty($settings['site_stripe_secret'])) || ($settings['site_enable_paypal'] == 'on' && !empty($settings['site_paypal_client_id']) && !empty($settings['site_paypal_secret_key']))))
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-toggle="modal" data-target="#paymentModal"><i class="fas fa-plus"></i> {{__('Pay Now')}}</a>
            </div>
        @endif
        @can('edit invoice')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('invoices.edit',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Edit Invoice')}}" data-original-title="{{__('Edit')}}"><i class="fas fa-pencil-alt"></i> {{__('Edit')}}</a>
            </div>
        @endcan
        @can('send invoice')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="{{ route('invoice.sent',$invoice->id) }}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-reply"></i> {{__('Send Invoice Mail')}}</a>
            </div>
        @endcan
        @can('payment reminder invoice')
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                <a href="{{ route('invoice.payment.reminder',$invoice->id) }}" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-money-check"></i> {{__('Payment Reminder')}}</a>
            </div>
        @endcan
        @can('custom mail send invoice')
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="{{ route('invoice.custom.send',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Send Invoice')}}" title="{{__('send Invoice')}}"><i class="fas fa-pencil-alt"></i> {{__('Send Invoice Mail')}}</a>
            </div>
        @endcan
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-12 col-12">
            <a href="{{ route('get.invoice',Crypt::encrypt($invoice->id)) }}" class="btn btn-xs bg-warning btn-white btn-icon-only width-auto" title="{{__('Print Invoice')}}" target="_blanks"><i class="fas fa-print"></i> {{__('Print')}}</a>
        </div>

    </div>
@endsection

@section('content')
    <div class="card">
        <div class="invoice-title">{{ Utility::invoiceNumberFormat($invoice->id) }}</div>
        <div class="invoice-detail">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="address-detail">
                        <strong>{{__('From')}} : </strong><br>
                        {{$settings['company_name']}}<br>
                        {{$settings['company_address']}}<br>
                        {{$settings['company_city']}}
                        @if(isset($settings['company_city']) && !empty($settings['company_city'])), @endif
                        {{$settings['company_state']}}
                        @if(isset($settings['company_zipcode']) && !empty($settings['company_zipcode']))-@endif {{$settings['company_zipcode']}}<br>
                        {{$settings['company_country']}}
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="address-detail text-right float-right">
                        <strong>{{__('To')}}:</strong>
                        <div class="invoice-number">{{(!empty($user))?$user->name:''}}</div>
                        <div class="invoice-number">{{(!empty($user))?$user->email:''}}</div>
                    </div>
                </div>
            </div>
            <div class="status-section">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-6">
                        <div class="text-status"><strong>{{__('Status')}}:</strong>
                            <div class="font-weight-bold">
                                @if($invoice->status == 0)
                                    <span class="badge badge-pill badge-primary">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                @elseif($invoice->status == 1)
                                    <span class="badge badge-pill badge-danger">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                @elseif($invoice->status == 2)
                                    <span class="badge badge-pill badge-warning">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                @elseif($invoice->status == 3)
                                    <span class="badge badge-pill badge-success">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                @elseif($invoice->status == 4)
                                    <span class="badge badge-pill badge-info">{{ __(\App\Invoice::$statues[$invoice->status]) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(!empty($invoice->project))
                        <div class="col-md-3 col-sm-6 col-6">
                            <div class="text-status text-right">{{__('Project')}}:
                                <strong>{{ (!empty($invoice->project)?$invoice->project->name:'') }}</strong>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3 col-sm-6 col-6">
                        <div class="text-status text-right">{{__('Issue Date')}}:
                            <strong>{{ Auth::user()->dateFormat($invoice->issue_date) }}</strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-6">
                        <div class="text-status text-right">{{__('Due Date')}}:
                            <strong>{{ Auth::user()->dateFormat($invoice->due_date) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="justify-content-between align-items-center d-flex">
                        <h4 class="h4 font-weight-400 float-left">{{__('Order Summary')}}</h4>
                        @can('create invoice product')
                            <a href="#" class="btn btn-sm btn-white float-right add-small" data-url="{{ route('invoices.products.add',$invoice->id) }}" data-ajax-popup="true" data-title="{{__('Add Item')}}">
                                <i class="fas fa-plus"></i> {{__('Add item')}}
                            </a>
                        @endcan
                    </div>
                    <div class="card">
                        <div class="table-responsive order-table">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th>{{__('Action')}}</th>
                                    <th>#</th>
                                    <th>{{__('Item')}}</th>
                                    <th class="text-right">{{__('Price')}}</th>
                                </tr>
                                </thead>
                                <tbody class="list">
                                @php $i=0; @endphp
                                @foreach($invoice->items as $items)
                                    <tr>
                                        <td class="Action">
                                            <span>
                                                @can('delete invoice product')
                                                    <a href="#" class="delete-icon" data-confirm="{{__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="document.getElementById('delete-form-{{$items->id}}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['invoices.products.delete', $invoice->id,$items->id],'id'=>'delete-form-'.$items->id]) !!}
                                                    {!! Form::close() !!}
                                                @endcan
                                            </span>
                                        </td>
                                        <td>
                                            {{++$i}}
                                        </td>
                                        <td>
                                            {{$items->iteam}}
                                        </td>
                                        <td class="text-right">
                                            {{\Auth::user()->priceFormat($items->price)}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row order-price">
                @php
                    $subTotal = $invoice->getSubTotal();
                    $tax = $invoice->getTax();
                @endphp
                <div class="col-md-3">
                    <div class="text-status"><strong>{{__('Subtotal')}} :</strong> {{Auth::user()->priceFormat($subTotal)}}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-status"><strong>{{__('Discount')}} :</strong> {{Auth::user()->priceFormat($invoice->discount)}}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-status"><strong>{{(!empty($invoice->tax)?$invoice->tax->name:'Tax')}} ({{(!empty($invoice->tax)?$invoice->tax->rate:'0')}} %) :</strong> {{\Auth::user()->priceFormat($tax)}}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-status"><strong>{{__('Total')}} :</strong> {{Auth::user()->priceFormat($subTotal-$invoice->discount+$tax)}}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-status text-right"><strong>{{__('Due Amount')}} :</strong> {{Auth::user()->priceFormat($invoice->getDue())}}</div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h4 class="h4 font-weight-400 float-left">{{__('Payment History')}}</h4>
    </div>
    <div class="card">
        <div class="table-responsive order-table">
            <table class="table align-items-center mb-0">
                <thead>
                <tr>
                    <th>{{__('Transaction ID')}}</th>
                    <th>{{__('Payment Date')}}</th>
                    <th>{{__('Payment Method')}}</th>
                    <th>{{__('Payment Type')}}</th>
                    <th>{{__('Note')}}</th>
                    <th class="text-right">{{__('Amount')}}</th>
                </tr>
                </thead>
                <tbody class="list">
                @php $i=0; @endphp
                @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{sprintf("%05d", $payment->transaction_id)}}</td>
                        <td>{{ Auth::user()->dateFormat($payment->date) }}</td>
                        <td>{{(!empty($payment->payment)?$payment->payment->name:'-')}}</td>
                        <td>{{$payment->payment_type}}</td>
                        <td>{{!empty($payment->notes) ? $payment->notes : '-'}}</td>
                        <td class="text-right">{{\Auth::user()->priceFormat($payment->amount)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if(\Auth::user()->type == 'client')
        @if($invoice->getDue() > 0)
            <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentModalLabel">{{ __('Add Payment') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card bg-none card-box">
                                <div class="row w-100">
                                    <div class="col-12">
                                        
                                        <ul class="nav nav-tabs" role="tablist">
                                        @if(isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on')
                                            @if((isset($payment_setting['stripe_key']) && !empty($payment_setting['stripe_key'])) && 
                                            (isset($payment_setting['stripe_secret']) && !empty($payment_setting['stripe_secret'])))
                                                <li>
                                                    <a class="active" data-toggle="tab" href="#stripe-payment" role="tab" aria-controls="stripe" aria-selected="true">{{ __('Stripe') }}</a>
                                                </li>
                                            @endif
                                        @endif
                                        @if(isset($payment_setting['is_paypal_enabled']) && $payment_setting['is_paypal_enabled'] == 'on')
                                            @if((isset($payment_setting['paypal_client_id']) && !empty($payment_setting['paypal_client_id'])) && 
                                            (isset($payment_setting['paypal_secret_key']) && !empty($payment_setting['paypal_secret_key'])))
                                                <li>
                                                    <a data-toggle="tab" href="#paypal-payment" role="tab" aria-controls="paypal" aria-selected="false">{{ __('Paypal') }}</a>
                                                </li>
                                            @endif
                                        @endif
                                        @if(isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on')
                                            @if((isset($payment_setting['paystack_public_key']) && !empty($payment_setting['paystack_public_key'])) && 
                                            (isset($payment_setting['paystack_secret_key']) && !empty($payment_setting['paystack_secret_key'])))
                                                <li>
                                                    <a data-toggle="tab" href="#paystack-payment" role="tab" aria-controls="paystack" aria-selected="false">{{ __('Paystack') }}</a>
                                                </li>
                                            @endif
                                        @endif
                                        @if(isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on')
                                            @if((isset($payment_setting['flutterwave_secret_key']) && !empty($payment_setting['flutterwave_secret_key'])) && 
                                            (isset($payment_setting['flutterwave_public_key']) && !empty($payment_setting['flutterwave_public_key'])))
                                                <li>
                                                    <a data-toggle="tab" href="#flutterwave-payment" role="tab" aria-controls="flutterwave" aria-selected="false">{{ __('Flutterwave') }}</a>
                                                </li>
                                            @endif
                                        @endif
                                        @if(isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on')
                                            @if((isset($payment_setting['razorpay_public_key']) && !empty($payment_setting['razorpay_public_key'])) && 
                                            (isset($payment_setting['razorpay_secret_key']) && !empty($payment_setting['razorpay_secret_key'])))
                                                <li>
                                                    <a data-toggle="tab" href="#razorpay-payment" role="tab" aria-controls="razorpay" aria-selected="false">{{ __('Razorpay') }}</a>
                                                </li>
                                            @endif
                                        @endif
                                        @if(isset($payment_setting['is_mercado_enabled']) && $payment_setting['is_mercado_enabled'] == 'on')
                                            @if((isset($payment_setting['mercado_app_id']) && !empty($payment_setting['mercado_app_id'])) && 
                                            (isset($payment_setting['mercado_secret_key']) && !empty($payment_setting['mercado_secret_key'])))
                                                <li>
                                                    <a data-toggle="tab" href="#mercado-payment" role="tab" aria-controls="mercado" aria-selected="false">{{ __('Mercado Pago') }}</a>
                                                </li>
                                            @endif
                                        @endif
                                        @if(isset($payment_setting['is_paytm_enabled']) && $payment_setting['is_paytm_enabled'] == 'on')
                                            @if((isset($payment_setting['paytm_merchant_id']) && !empty($payment_setting['paytm_merchant_id'])) && 
                                            (isset($payment_setting['paytm_merchant_key']) && !empty($payment_setting['paytm_merchant_key'])))
                                                <li>
                                                    <a data-toggle="tab" href="#paytm-payment" role="tab" aria-controls="paytm" aria-selected="false">{{ __('Paytm') }}</a>
                                                </li>
                                            @endif
                                        @endif
                                        @if(isset($payment_setting['is_mollie_enabled']) && $payment_setting['is_mollie_enabled'] == 'on')
                                            @if((isset($payment_setting['mollie_api_key']) && !empty($payment_setting['mollie_api_key'])) && 
                                            (isset($payment_setting['mollie_profile_id']) && !empty($payment_setting['mollie_profile_id'])))
                                                <li>
                                                    <a data-toggle="tab" href="#mollie-payment" role="tab" aria-controls="mollie" aria-selected="false">{{ __('Mollie') }}</a>
                                                </li>
                                            @endif
                                        @endif
                                        @if(isset($payment_setting['is_skrill_enabled']) && $payment_setting['is_skrill_enabled'] == 'on')
                                            @if((isset($payment_setting['skrill_email']) && !empty($payment_setting['skrill_email'])))
                                                <li>
                                                    <a data-toggle="tab" href="#skrill-payment" role="tab" aria-controls="skrill" aria-selected="false">{{ __('Skrill') }}</a>
                                                </li>
                                            @endif
                                        @endif
                                        @if(isset($payment_setting['is_coingate_enabled']) && $payment_setting['is_coingate_enabled'] == 'on')
                                            @if((isset($payment_setting['coingate_auth_token']) && !empty($payment_setting['coingate_auth_token'])))
                                                <li>
                                                    <a data-toggle="tab" href="#coingate-payment" role="tab" aria-controls="coingate" aria-selected="false">{{ __('CoinGate') }}</a>
                                                </li>
                                            @endif
                                        @endif
                                        </ul>
                                    </div>
                                    <div class="col-12">
                                        <div class="tab-content">
                                           
                                            @if(isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on')
                                                @if((isset($payment_setting['stripe_key']) && !empty($payment_setting['stripe_key'])) && 
                                                    (isset($payment_setting['stripe_secret']) && !empty($payment_setting['stripe_secret'])))
                                                    <div class="tab-pane fade {{ isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on'  ? 'show active' : ''}}" id="stripe-payment" role="tabpanel" aria-labelledby="stripe-payment">
                                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form" action="{{ route('invoice.pay.with.stripe') }}">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <div class="form-icon-addon">
                                                                        <span>{{ $payment_setting['currency_symbol'] }}</span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                        <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                                    </div>
                                                                    @error('amount')
                                                                    <span class="invalid-amount text-danger text-xs" role="alert">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-12 form-group mt-3 text-right">
                                                                    <input type="submit" value="{{__('Make Payment')}}" class="btn-create badge-blue">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(isset($payment_setting['is_paypal_enabled']) && $payment_setting['is_paypal_enabled'] == 'on')
                                                @if((isset($payment_setting['paypal_client_id']) && !empty($payment_setting['paypal_client_id'])) && 
                                                    (isset($payment_setting['paypal_secret_key']) && !empty($payment_setting['paypal_secret_key'])))
                                                    <div class="tab-pane fade" id="paypal-payment" role="tabpanel" aria-labelledby="paypal-payment">
                                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form" action="{{ route('client.pay.with.paypal', $invoice->id) }}">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <div class="form-icon-addon">
                                                                        <span>{{ $payment_setting['currency_symbol'] }}</span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                    </div>
                                                                    @error('amount')
                                                                    <span class="invalid-amount text-danger text-xs" role="alert">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                                <div class="col-12 form-group mt-3 text-right">
                                                                    <input type="submit" value="{{__('Make Payment')}}" class="btn-create badge-blue">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on')
                                                @if((isset($payment_setting['paystack_public_key']) && !empty($payment_setting['paystack_public_key'])) && 
                                                    (isset($payment_setting['paystack_secret_key']) && !empty($payment_setting['paystack_secret_key'])))
                                                    <div class="tab-pane fade" id="paystack-payment" role="tabpanel" aria-labelledby="paystack-payment">
                                                        <form method="post" action="{{route('invoice.pay.with.paystack')}}" class="require-validation" id="paystack-payment-form">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <div class="form-icon-addon">
                                                                        <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                        <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="button" value="{{__('Make Payment')}}" class="btn-create badge-blue" id="pay_with_paystack">
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on')
                                                @if((isset($payment_setting['flutterwave_secret_key']) && !empty($payment_setting['flutterwave_secret_key'])) && 
                                                    (isset($payment_setting['flutterwave_public_key']) && !empty($payment_setting['flutterwave_public_key'])))
                                                    <div class="tab-pane fade " id="flutterwave-payment" role="tabpanel" aria-labelledby="flutterwave-payment">
                                                        <form method="post" action="{{route('invoice.pay.with.flaterwave')}}" class="require-validation" id="flaterwave-payment-form">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <div class="form-icon-addon">
                                                                        <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                        <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="button" value="{{__('Make Payment')}}" class="btn-create badge-blue" id="pay_with_flaterwave">
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on')
                                                @if((isset($payment_setting['razorpay_public_key']) && !empty($payment_setting['razorpay_public_key'])) && 
                                                    (isset($payment_setting['razorpay_secret_key']) && !empty($payment_setting['razorpay_secret_key'])))
                                                    <div class="tab-pane fade " id="razorpay-payment" role="tabpanel" aria-labelledby="flutterwave-payment">
                                                        <form method="post" action="{{route('invoice.pay.with.razorpay')}}" class="require-validation" id="razorpay-payment-form">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <div class="form-icon-addon">
                                                                        <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                        <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="button" value="{{__('Make Payment')}}" class="btn-create badge-blue" id="pay_with_razorpay">
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(isset($payment_setting['is_mollie_enabled']) && $payment_setting['is_mollie_enabled'] == 'on')
                                                @if((isset($payment_setting['mollie_api_key']) && !empty($payment_setting['mollie_api_key'])) && 
                                                    (isset($payment_setting['mollie_profile_id']) && !empty($payment_setting['mollie_profile_id'])))
                                                    <div class="tab-pane fade " id="mollie-payment" role="tabpanel" aria-labelledby="mollie-payment">
                                                        <form method="post" action="{{route('invoice.pay.with.mollie')}}" class="require-validation" id="mollie-payment-form">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <div class="form-icon-addon">
                                                                        <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                        <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="submit" value="{{__('Make Payment')}}" class="btn-create badge-blue">
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(isset($payment_setting['is_mercado_enabled']) && $payment_setting['is_mercado_enabled'] == 'on')
                                                @if((isset($payment_setting['mercado_app_id']) && !empty($payment_setting['mercado_app_id'])) && 
                                                    (isset($payment_setting['mercado_secret_key']) && !empty($payment_setting['mercado_secret_key'])))
                                                    <div class="tab-pane fade " id="mercado-payment" role="tabpanel" aria-labelledby="mercado-payment">
                                                        <form method="post" action="{{route('invoice.pay.with.mercado')}}" class="require-validation" id="mercado-payment-form">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <div class="form-icon-addon">
                                                                        <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                        <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="submit" value="{{__('Make Payment')}}" class="btn-create badge-blue">
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(isset($payment_setting['is_paytm_enabled']) && $payment_setting['is_paytm_enabled'] == 'on')
                                                @if((isset($payment_setting['paytm_merchant_id']) && !empty($payment_setting['paytm_merchant_id'])) && 
                                                    (isset($payment_setting['paytm_merchant_key']) && !empty($payment_setting['paytm_merchant_key'])))
                                                    <div class="tab-pane fade " id="paytm-payment" role="tabpanel" aria-labelledby="paytm-payment">
                                                        <form method="post" action="{{route('invoice.pay.with.paytm')}}" class="require-validation" id="paytm-payment-form">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    
                                                                    <div class="form-group">
                                                                        
                                                                        <label for="mobile" class="form-control-label text-dark">{{__('Mobile Number')}}</label>
                                                                        <input type="text" id="mobile" name="mobile" class="form-control mobile" data-from="mobile" placeholder="{{ __('Enter Mobile Number') }}" required>
                                                                    </div>
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <div class="form-icon-addon">
                                                                        <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                        <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="submit" value="{{__('Make Payment')}}" class="btn-create badge-blue">
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(isset($payment_setting['is_skrill_enabled']) && $payment_setting['is_skrill_enabled'] == 'on')
                                                @if((isset($payment_setting['skrill_email']) && !empty($payment_setting['skrill_email'])))
                                                    <div class="tab-pane fade " id="skrill-payment" role="tabpanel" aria-labelledby="skrill-payment">
                                                        <form method="post" action="{{route('invoice.pay.with.skrill')}}" class="require-validation" id="skrill-payment-form">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <div class="form-icon-addon">
                                                                        <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                        <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                                    </div>
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
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="submit" value="{{__('Make Payment')}}" class="btn-create badge-blue">
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                            @if(isset($payment_setting['is_coingate_enabled']) && $payment_setting['is_coingate_enabled'] == 'on')
                                                @if((isset($payment_setting['coingate_auth_token']) && !empty($payment_setting['coingate_auth_token'])))
                                                    <div class="tab-pane fade " id="coingate-payment" role="tabpanel" aria-labelledby="coingate-payment">
                                                        <form method="post" action="{{route('invoice.pay.with.coingate')}}" class="require-validation" id="coingate-payment-form">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label">{{ __('Amount') }}</label>
                                                                    <div class="form-icon-addon">
                                                                        <span>{{isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'}}</span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="{{$invoice->getDue()}}" min="0" step="0.01" max="{{$invoice->getDue()}}" id="amount">
                                                                        <input type="hidden" value="{{$invoice->id}}" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="submit" value="{{__('Make Payment')}}" class="btn-create badge-blue">
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection
