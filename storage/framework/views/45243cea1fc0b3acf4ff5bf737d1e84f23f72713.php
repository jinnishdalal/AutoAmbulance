<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Invoice Detail')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>
    <?php
        $settings = \App\Utility::settings();
        $dir_payment = asset(Storage::url('payments'));
    ?>
    <script>
        function getTask(obj, project_id) {
            $('#task_id').empty();
            var milestone_id = obj.value;
            $.ajax({
                url: '<?php echo route('invoices.milestone.task'); ?>',
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
                    show_toastr('<?php echo e(__("Error")); ?>', data.error, 'error')
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

    <?php if(Auth::user()->type == 'client' && $invoice->getDue() > 0 && $settings['site_enable_stripe'] == 'on'): ?>
        <?php $stripe_session = Session::get('stripe_session');?>
        <?php if(isset($stripe_session) && $stripe_session): ?>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            var stripe = Stripe('<?php echo e($admin_payment_setting['stripe_key']); ?>');
            stripe.redirectToCheckout({
                sessionId: '<?php echo e($stripe_session->id); ?>',
            }).then((result) => {
                console.log(result);
            });
        </script>
        <?php endif ?>
    <?php endif; ?>

    <?php if(Auth::user()->type == 'client' && $invoice->getDue() > 0 && isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on'): ?>

        <script src="https://js.paystack.co/v1/inline.js"></script>

        <script type="text/javascript">
            $(document).on("click", "#pay_with_paystack", function () {

                $('#paystack-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){
                        var coupon_id = res.coupon;

                        var paystack_callback = "<?php echo e(url('/invoice-pay-with-paystack')); ?>";
                        var order_id = '<?php echo e(time()); ?>';
                        var handler = PaystackPop.setup({
                            key: '<?php echo e($payment_setting['paystack_public_key']); ?>',
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
                                window.location.href = "<?php echo e(url('/invoice/paystack')); ?>/"+response.reference+"/<?php echo e(encrypt($invoice->id)); ?>";
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
    <?php endif; ?>

    <?php if(Auth::user()->type == 'client' && $invoice->getDue() > 0 && isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on'): ?>

        <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>

        <script type="text/javascript">

            //    Flaterwave Payment
            $(document).on("click", "#pay_with_flaterwave", function () {

                $('#flaterwave-payment-form').ajaxForm(function (res) {
                    if(res.flag == 1){
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if("<?php echo e(isset($payment_setting['flutterwave_public_key'] )); ?>"){
                            API_publicKey = "<?php echo e($payment_setting['flutterwave_public_key']); ?>";
                        }
                        var nowTim = "<?php echo e(date('d-m-Y-h-i-a')); ?>";
                        var flutter_callback = "<?php echo e(url('/invoice-pay-with-flaterwave')); ?>";
                        var x = getpaidSetup({
                            PBFPubKey: API_publicKey,
                            customer_email: '<?php echo e(Auth::user()->email); ?>',
                            amount: res.total_price,
                            currency: '<?php echo e($payment_setting['currency']); ?>',
                            txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) + 'fluttpay_online-' +
                                <?php echo e(date('Y-m-d')); ?>,
                            meta: [{
                                metaname: "payment_id",
                                metavalue: "id"
                            }],
                            onclose: function () {
                            },
                            callback: function (response) {
                                var txref = response.tx.txRef;
                                if(response.tx.chargeResponseCode == "00" || response.tx.chargeResponseCode == "0") {
                                    window.location.href = "<?php echo e(url('/invoice/flaterwave')); ?>/"+txref+"/<?php echo e(encrypt($invoice->id)); ?>";
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

    <?php endif; ?>

    <?php if(Auth::user()->type == 'client' && $invoice->getDue() > 0 && isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on'): ?>

        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

        <script type="text/javascript">
            // Razorpay Payment
            $(document).on("click", "#pay_with_razorpay", function () {
                $('#razorpay-payment-form').ajaxForm(function (res) {
                    
                    if(res.flag == 1){

                        var razorPay_callback = "<?php echo e(url('/invoice-pay-with-razorpay')); ?>";
                        var totalAmount = res.total_price * 100;
                        var coupon_id = res.coupon;
                        var API_publicKey = '';
                        if("<?php echo e(isset($payment_setting['razorpay_public_key'])); ?>"){
                            API_publicKey = "<?php echo e($payment_setting['razorpay_public_key']); ?>";
                        }
                        var options = {
                            "key": API_publicKey, // your Razorpay Key Id
                            "amount": totalAmount,
                            "name": 'Invoice Payment',
                            "currency": '<?php echo e($payment_setting['currency']); ?>',
                            "description": "",
                            "handler": function (response) {
                                window.location.href = "<?php echo e(url('/invoice/razorpay')); ?>/"+response.razorpay_payment_id +"/<?php echo e(encrypt($invoice->id)); ?>";
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
    
    <?php endif; ?>


<?php $__env->stopPush(); ?>

<?php $__env->startPush('css-page'); ?>
    <style>
        #card-element {
            border: 1px solid #e4e6fc;
            border-radius: 5px;
            padding: 10px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('action-button'); ?>
    <div class="all-button-box row d-flex justify-content-end">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create invoice payment')): ?>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="<?php echo e(route('invoices.payments.create',$invoice->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Add Payment')); ?>"><i class="fas fa-plus"></i> <?php echo e(__('Add Payment')); ?></a>
            </div>
        <?php endif; ?>
        <?php if(\Auth::user()->type == 'client' && $invoice->getDue() > 0 && (($settings['site_enable_stripe'] == 'on' && !empty($settings['site_stripe_key']) && !empty($settings['site_stripe_secret'])) || ($settings['site_enable_paypal'] == 'on' && !empty($settings['site_paypal_client_id']) && !empty($settings['site_paypal_secret_key'])))): ?>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-toggle="modal" data-target="#paymentModal"><i class="fas fa-plus"></i> <?php echo e(__('Pay Now')); ?></a>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit invoice')): ?>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="<?php echo e(route('invoices.edit',$invoice->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Invoice')); ?>" data-original-title="<?php echo e(__('Edit')); ?>"><i class="fas fa-pencil-alt"></i> <?php echo e(__('Edit')); ?></a>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('send invoice')): ?>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="<?php echo e(route('invoice.sent',$invoice->id)); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-reply"></i> <?php echo e(__('Send Invoice Mail')); ?></a>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payment reminder invoice')): ?>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-6">
                <a href="<?php echo e(route('invoice.payment.reminder',$invoice->id)); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-money-check"></i> <?php echo e(__('Payment Reminder')); ?></a>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('custom mail send invoice')): ?>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="<?php echo e(route('invoice.custom.send',$invoice->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Send Invoice')); ?>" title="<?php echo e(__('send Invoice')); ?>"><i class="fas fa-pencil-alt"></i> <?php echo e(__('Send Invoice Mail')); ?></a>
            </div>
        <?php endif; ?>
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-12 col-12">
            <a href="<?php echo e(route('get.invoice',Crypt::encrypt($invoice->id))); ?>" class="btn btn-xs bg-warning btn-white btn-icon-only width-auto" title="<?php echo e(__('Print Invoice')); ?>" target="_blanks"><i class="fas fa-print"></i> <?php echo e(__('Print')); ?></a>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="invoice-title"><?php echo e(Utility::invoiceNumberFormat($invoice->id)); ?></div>
        <div class="invoice-detail">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <div class="address-detail">
                        <strong><?php echo e(__('From')); ?> : </strong><br>
                        <?php echo e($settings['company_name']); ?><br>
                        <?php echo e($settings['company_address']); ?><br>
                        <?php echo e($settings['company_city']); ?>

                        <?php if(isset($settings['company_city']) && !empty($settings['company_city'])): ?>, <?php endif; ?>
                        <?php echo e($settings['company_state']); ?>

                        <?php if(isset($settings['company_zipcode']) && !empty($settings['company_zipcode'])): ?>-<?php endif; ?> <?php echo e($settings['company_zipcode']); ?><br>
                        <?php echo e($settings['company_country']); ?>

                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="address-detail text-right float-right">
                        <strong><?php echo e(__('To')); ?>:</strong>
                        <div class="invoice-number"><?php echo e((!empty($user))?$user->name:''); ?></div>
                        <div class="invoice-number"><?php echo e((!empty($user))?$user->email:''); ?></div>
                    </div>
                </div>
            </div>
            <div class="status-section">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-6">
                        <div class="text-status"><strong><?php echo e(__('Status')); ?>:</strong>
                            <div class="font-weight-bold">
                                <?php if($invoice->status == 0): ?>
                                    <span class="badge badge-pill badge-primary"><?php echo e(__(\App\Invoice::$statues[$invoice->status])); ?></span>
                                <?php elseif($invoice->status == 1): ?>
                                    <span class="badge badge-pill badge-danger"><?php echo e(__(\App\Invoice::$statues[$invoice->status])); ?></span>
                                <?php elseif($invoice->status == 2): ?>
                                    <span class="badge badge-pill badge-warning"><?php echo e(__(\App\Invoice::$statues[$invoice->status])); ?></span>
                                <?php elseif($invoice->status == 3): ?>
                                    <span class="badge badge-pill badge-success"><?php echo e(__(\App\Invoice::$statues[$invoice->status])); ?></span>
                                <?php elseif($invoice->status == 4): ?>
                                    <span class="badge badge-pill badge-info"><?php echo e(__(\App\Invoice::$statues[$invoice->status])); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if(!empty($invoice->project)): ?>
                        <div class="col-md-3 col-sm-6 col-6">
                            <div class="text-status text-right"><?php echo e(__('Project')); ?>:
                                <strong><?php echo e((!empty($invoice->project)?$invoice->project->name:'')); ?></strong>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="col-md-3 col-sm-6 col-6">
                        <div class="text-status text-right"><?php echo e(__('Issue Date')); ?>:
                            <strong><?php echo e(Auth::user()->dateFormat($invoice->issue_date)); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-6">
                        <div class="text-status text-right"><?php echo e(__('Due Date')); ?>:
                            <strong><?php echo e(Auth::user()->dateFormat($invoice->due_date)); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="justify-content-between align-items-center d-flex">
                        <h4 class="h4 font-weight-400 float-left"><?php echo e(__('Order Summary')); ?></h4>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create invoice product')): ?>
                            <a href="#" class="btn btn-sm btn-white float-right add-small" data-url="<?php echo e(route('invoices.products.add',$invoice->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Add Item')); ?>">
                                <i class="fas fa-plus"></i> <?php echo e(__('Add item')); ?>

                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card">
                        <div class="table-responsive order-table">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th><?php echo e(__('Action')); ?></th>
                                    <th>#</th>
                                    <th><?php echo e(__('Item')); ?></th>
                                    <th class="text-right"><?php echo e(__('Price')); ?></th>
                                </tr>
                                </thead>
                                <tbody class="list">
                                <?php $i=0; ?>
                                <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="Action">
                                            <span>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete invoice product')): ?>
                                                    <a href="#" class="delete-icon" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($items->id); ?>').submit();">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['invoices.products.delete', $invoice->id,$items->id],'id'=>'delete-form-'.$items->id]); ?>

                                                    <?php echo Form::close(); ?>

                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo e(++$i); ?>

                                        </td>
                                        <td>
                                            <?php echo e($items->iteam); ?>

                                        </td>
                                        <td class="text-right">
                                            <?php echo e(\Auth::user()->priceFormat($items->price)); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row order-price">
                <?php
                    $subTotal = $invoice->getSubTotal();
                    $tax = $invoice->getTax();
                ?>
                <div class="col-md-3">
                    <div class="text-status"><strong><?php echo e(__('Subtotal')); ?> :</strong> <?php echo e(Auth::user()->priceFormat($subTotal)); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-status"><strong><?php echo e(__('Discount')); ?> :</strong> <?php echo e(Auth::user()->priceFormat($invoice->discount)); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-status"><strong><?php echo e((!empty($invoice->tax)?$invoice->tax->name:'Tax')); ?> (<?php echo e((!empty($invoice->tax)?$invoice->tax->rate:'0')); ?> %) :</strong> <?php echo e(\Auth::user()->priceFormat($tax)); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-status"><strong><?php echo e(__('Total')); ?> :</strong> <?php echo e(Auth::user()->priceFormat($subTotal-$invoice->discount+$tax)); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="text-status text-right"><strong><?php echo e(__('Due Amount')); ?> :</strong> <?php echo e(Auth::user()->priceFormat($invoice->getDue())); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h4 class="h4 font-weight-400 float-left"><?php echo e(__('Payment History')); ?></h4>
    </div>
    <div class="card">
        <div class="table-responsive order-table">
            <table class="table align-items-center mb-0">
                <thead>
                <tr>
                    <th><?php echo e(__('Transaction ID')); ?></th>
                    <th><?php echo e(__('Payment Date')); ?></th>
                    <th><?php echo e(__('Payment Method')); ?></th>
                    <th><?php echo e(__('Payment Type')); ?></th>
                    <th><?php echo e(__('Note')); ?></th>
                    <th class="text-right"><?php echo e(__('Amount')); ?></th>
                </tr>
                </thead>
                <tbody class="list">
                <?php $i=0; ?>
                <?php $__currentLoopData = $invoice->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e(sprintf("%05d", $payment->transaction_id)); ?></td>
                        <td><?php echo e(Auth::user()->dateFormat($payment->date)); ?></td>
                        <td><?php echo e((!empty($payment->payment)?$payment->payment->name:'-')); ?></td>
                        <td><?php echo e($payment->payment_type); ?></td>
                        <td><?php echo e(!empty($payment->notes) ? $payment->notes : '-'); ?></td>
                        <td class="text-right"><?php echo e(\Auth::user()->priceFormat($payment->amount)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if(\Auth::user()->type == 'client'): ?>
        <?php if($invoice->getDue() > 0): ?>
            <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="paymentModalLabel"><?php echo e(__('Add Payment')); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="card bg-none card-box">
                                <div class="row w-100">
                                    <div class="col-12">
                                        
                                        <ul class="nav nav-tabs" role="tablist">
                                        <?php if(isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on'): ?>
                                            <?php if((isset($payment_setting['stripe_key']) && !empty($payment_setting['stripe_key'])) && 
                                            (isset($payment_setting['stripe_secret']) && !empty($payment_setting['stripe_secret']))): ?>
                                                <li>
                                                    <a class="active" data-toggle="tab" href="#stripe-payment" role="tab" aria-controls="stripe" aria-selected="true"><?php echo e(__('Stripe')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($payment_setting['is_paypal_enabled']) && $payment_setting['is_paypal_enabled'] == 'on'): ?>
                                            <?php if((isset($payment_setting['paypal_client_id']) && !empty($payment_setting['paypal_client_id'])) && 
                                            (isset($payment_setting['paypal_secret_key']) && !empty($payment_setting['paypal_secret_key']))): ?>
                                                <li>
                                                    <a data-toggle="tab" href="#paypal-payment" role="tab" aria-controls="paypal" aria-selected="false"><?php echo e(__('Paypal')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on'): ?>
                                            <?php if((isset($payment_setting['paystack_public_key']) && !empty($payment_setting['paystack_public_key'])) && 
                                            (isset($payment_setting['paystack_secret_key']) && !empty($payment_setting['paystack_secret_key']))): ?>
                                                <li>
                                                    <a data-toggle="tab" href="#paystack-payment" role="tab" aria-controls="paystack" aria-selected="false"><?php echo e(__('Paystack')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on'): ?>
                                            <?php if((isset($payment_setting['flutterwave_secret_key']) && !empty($payment_setting['flutterwave_secret_key'])) && 
                                            (isset($payment_setting['flutterwave_public_key']) && !empty($payment_setting['flutterwave_public_key']))): ?>
                                                <li>
                                                    <a data-toggle="tab" href="#flutterwave-payment" role="tab" aria-controls="flutterwave" aria-selected="false"><?php echo e(__('Flutterwave')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on'): ?>
                                            <?php if((isset($payment_setting['razorpay_public_key']) && !empty($payment_setting['razorpay_public_key'])) && 
                                            (isset($payment_setting['razorpay_secret_key']) && !empty($payment_setting['razorpay_secret_key']))): ?>
                                                <li>
                                                    <a data-toggle="tab" href="#razorpay-payment" role="tab" aria-controls="razorpay" aria-selected="false"><?php echo e(__('Razorpay')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($payment_setting['is_mercado_enabled']) && $payment_setting['is_mercado_enabled'] == 'on'): ?>
                                            <?php if((isset($payment_setting['mercado_app_id']) && !empty($payment_setting['mercado_app_id'])) && 
                                            (isset($payment_setting['mercado_secret_key']) && !empty($payment_setting['mercado_secret_key']))): ?>
                                                <li>
                                                    <a data-toggle="tab" href="#mercado-payment" role="tab" aria-controls="mercado" aria-selected="false"><?php echo e(__('Mercado Pago')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($payment_setting['is_paytm_enabled']) && $payment_setting['is_paytm_enabled'] == 'on'): ?>
                                            <?php if((isset($payment_setting['paytm_merchant_id']) && !empty($payment_setting['paytm_merchant_id'])) && 
                                            (isset($payment_setting['paytm_merchant_key']) && !empty($payment_setting['paytm_merchant_key']))): ?>
                                                <li>
                                                    <a data-toggle="tab" href="#paytm-payment" role="tab" aria-controls="paytm" aria-selected="false"><?php echo e(__('Paytm')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($payment_setting['is_mollie_enabled']) && $payment_setting['is_mollie_enabled'] == 'on'): ?>
                                            <?php if((isset($payment_setting['mollie_api_key']) && !empty($payment_setting['mollie_api_key'])) && 
                                            (isset($payment_setting['mollie_profile_id']) && !empty($payment_setting['mollie_profile_id']))): ?>
                                                <li>
                                                    <a data-toggle="tab" href="#mollie-payment" role="tab" aria-controls="mollie" aria-selected="false"><?php echo e(__('Mollie')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($payment_setting['is_skrill_enabled']) && $payment_setting['is_skrill_enabled'] == 'on'): ?>
                                            <?php if((isset($payment_setting['skrill_email']) && !empty($payment_setting['skrill_email']))): ?>
                                                <li>
                                                    <a data-toggle="tab" href="#skrill-payment" role="tab" aria-controls="skrill" aria-selected="false"><?php echo e(__('Skrill')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if(isset($payment_setting['is_coingate_enabled']) && $payment_setting['is_coingate_enabled'] == 'on'): ?>
                                            <?php if((isset($payment_setting['coingate_auth_token']) && !empty($payment_setting['coingate_auth_token']))): ?>
                                                <li>
                                                    <a data-toggle="tab" href="#coingate-payment" role="tab" aria-controls="coingate" aria-selected="false"><?php echo e(__('CoinGate')); ?></a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        </ul>
                                    </div>
                                    <div class="col-12">
                                        <div class="tab-content">
                                           
                                            <?php if(isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on'): ?>
                                                <?php if((isset($payment_setting['stripe_key']) && !empty($payment_setting['stripe_key'])) && 
                                                    (isset($payment_setting['stripe_secret']) && !empty($payment_setting['stripe_secret']))): ?>
                                                    <div class="tab-pane fade <?php echo e(isset($payment_setting['is_stripe_enabled']) && $payment_setting['is_stripe_enabled'] == 'on'  ? 'show active' : ''); ?>" id="stripe-payment" role="tabpanel" aria-labelledby="stripe-payment">
                                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form" action="<?php echo e(route('invoice.pay.with.stripe')); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label"><?php echo e(__('Amount')); ?></label>
                                                                    <div class="form-icon-addon">
                                                                        <span><?php echo e($payment_setting['currency_symbol']); ?></span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="<?php echo e($invoice->getDue()); ?>" min="0" step="0.01" max="<?php echo e($invoice->getDue()); ?>" id="amount">
                                                                        <input type="hidden" value="<?php echo e($invoice->id); ?>" name="invoice_id">
                                                                    </div>
                                                                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                    <span class="invalid-amount text-danger text-xs" role="alert"><?php echo e($message); ?></span>
                                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                </div>
                                                                <div class="col-12 form-group mt-3 text-right">
                                                                    <input type="submit" value="<?php echo e(__('Make Payment')); ?>" class="btn-create badge-blue">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if(isset($payment_setting['is_paypal_enabled']) && $payment_setting['is_paypal_enabled'] == 'on'): ?>
                                                <?php if((isset($payment_setting['paypal_client_id']) && !empty($payment_setting['paypal_client_id'])) && 
                                                    (isset($payment_setting['paypal_secret_key']) && !empty($payment_setting['paypal_secret_key']))): ?>
                                                    <div class="tab-pane fade" id="paypal-payment" role="tabpanel" aria-labelledby="paypal-payment">
                                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST" id="payment-form" action="<?php echo e(route('client.pay.with.paypal', $invoice->id)); ?>">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label"><?php echo e(__('Amount')); ?></label>
                                                                    <div class="form-icon-addon">
                                                                        <span><?php echo e($payment_setting['currency_symbol']); ?></span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="<?php echo e($invoice->getDue()); ?>" min="0" step="0.01" max="<?php echo e($invoice->getDue()); ?>" id="amount">
                                                                    </div>
                                                                    <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                                    <span class="invalid-amount text-danger text-xs" role="alert"><?php echo e($message); ?></span>
                                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                                </div>
                                                                <div class="col-12 form-group mt-3 text-right">
                                                                    <input type="submit" value="<?php echo e(__('Make Payment')); ?>" class="btn-create badge-blue">
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if(isset($payment_setting['is_paystack_enabled']) && $payment_setting['is_paystack_enabled'] == 'on'): ?>
                                                <?php if((isset($payment_setting['paystack_public_key']) && !empty($payment_setting['paystack_public_key'])) && 
                                                    (isset($payment_setting['paystack_secret_key']) && !empty($payment_setting['paystack_secret_key']))): ?>
                                                    <div class="tab-pane fade" id="paystack-payment" role="tabpanel" aria-labelledby="paystack-payment">
                                                        <form method="post" action="<?php echo e(route('invoice.pay.with.paystack')); ?>" class="require-validation" id="paystack-payment-form">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label"><?php echo e(__('Amount')); ?></label>
                                                                    <div class="form-icon-addon">
                                                                        <span><?php echo e(isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'); ?></span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="<?php echo e($invoice->getDue()); ?>" min="0" step="0.01" max="<?php echo e($invoice->getDue()); ?>" id="amount">
                                                                        <input type="hidden" value="<?php echo e($invoice->id); ?>" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="button" value="<?php echo e(__('Make Payment')); ?>" class="btn-create badge-blue" id="pay_with_paystack">
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if(isset($payment_setting['is_flutterwave_enabled']) && $payment_setting['is_flutterwave_enabled'] == 'on'): ?>
                                                <?php if((isset($payment_setting['flutterwave_secret_key']) && !empty($payment_setting['flutterwave_secret_key'])) && 
                                                    (isset($payment_setting['flutterwave_public_key']) && !empty($payment_setting['flutterwave_public_key']))): ?>
                                                    <div class="tab-pane fade " id="flutterwave-payment" role="tabpanel" aria-labelledby="flutterwave-payment">
                                                        <form method="post" action="<?php echo e(route('invoice.pay.with.flaterwave')); ?>" class="require-validation" id="flaterwave-payment-form">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label"><?php echo e(__('Amount')); ?></label>
                                                                    <div class="form-icon-addon">
                                                                        <span><?php echo e(isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'); ?></span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="<?php echo e($invoice->getDue()); ?>" min="0" step="0.01" max="<?php echo e($invoice->getDue()); ?>" id="amount">
                                                                        <input type="hidden" value="<?php echo e($invoice->id); ?>" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="button" value="<?php echo e(__('Make Payment')); ?>" class="btn-create badge-blue" id="pay_with_flaterwave">
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if(isset($payment_setting['is_razorpay_enabled']) && $payment_setting['is_razorpay_enabled'] == 'on'): ?>
                                                <?php if((isset($payment_setting['razorpay_public_key']) && !empty($payment_setting['razorpay_public_key'])) && 
                                                    (isset($payment_setting['razorpay_secret_key']) && !empty($payment_setting['razorpay_secret_key']))): ?>
                                                    <div class="tab-pane fade " id="razorpay-payment" role="tabpanel" aria-labelledby="flutterwave-payment">
                                                        <form method="post" action="<?php echo e(route('invoice.pay.with.razorpay')); ?>" class="require-validation" id="razorpay-payment-form">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label"><?php echo e(__('Amount')); ?></label>
                                                                    <div class="form-icon-addon">
                                                                        <span><?php echo e(isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'); ?></span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="<?php echo e($invoice->getDue()); ?>" min="0" step="0.01" max="<?php echo e($invoice->getDue()); ?>" id="amount">
                                                                        <input type="hidden" value="<?php echo e($invoice->id); ?>" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="button" value="<?php echo e(__('Make Payment')); ?>" class="btn-create badge-blue" id="pay_with_razorpay">
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if(isset($payment_setting['is_mollie_enabled']) && $payment_setting['is_mollie_enabled'] == 'on'): ?>
                                                <?php if((isset($payment_setting['mollie_api_key']) && !empty($payment_setting['mollie_api_key'])) && 
                                                    (isset($payment_setting['mollie_profile_id']) && !empty($payment_setting['mollie_profile_id']))): ?>
                                                    <div class="tab-pane fade " id="mollie-payment" role="tabpanel" aria-labelledby="mollie-payment">
                                                        <form method="post" action="<?php echo e(route('invoice.pay.with.mollie')); ?>" class="require-validation" id="mollie-payment-form">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label"><?php echo e(__('Amount')); ?></label>
                                                                    <div class="form-icon-addon">
                                                                        <span><?php echo e(isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'); ?></span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="<?php echo e($invoice->getDue()); ?>" min="0" step="0.01" max="<?php echo e($invoice->getDue()); ?>" id="amount">
                                                                        <input type="hidden" value="<?php echo e($invoice->id); ?>" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="submit" value="<?php echo e(__('Make Payment')); ?>" class="btn-create badge-blue">
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if(isset($payment_setting['is_mercado_enabled']) && $payment_setting['is_mercado_enabled'] == 'on'): ?>
                                                <?php if((isset($payment_setting['mercado_app_id']) && !empty($payment_setting['mercado_app_id'])) && 
                                                    (isset($payment_setting['mercado_secret_key']) && !empty($payment_setting['mercado_secret_key']))): ?>
                                                    <div class="tab-pane fade " id="mercado-payment" role="tabpanel" aria-labelledby="mercado-payment">
                                                        <form method="post" action="<?php echo e(route('invoice.pay.with.mercado')); ?>" class="require-validation" id="mercado-payment-form">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label"><?php echo e(__('Amount')); ?></label>
                                                                    <div class="form-icon-addon">
                                                                        <span><?php echo e(isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'); ?></span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="<?php echo e($invoice->getDue()); ?>" min="0" step="0.01" max="<?php echo e($invoice->getDue()); ?>" id="amount">
                                                                        <input type="hidden" value="<?php echo e($invoice->id); ?>" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="submit" value="<?php echo e(__('Make Payment')); ?>" class="btn-create badge-blue">
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if(isset($payment_setting['is_paytm_enabled']) && $payment_setting['is_paytm_enabled'] == 'on'): ?>
                                                <?php if((isset($payment_setting['paytm_merchant_id']) && !empty($payment_setting['paytm_merchant_id'])) && 
                                                    (isset($payment_setting['paytm_merchant_key']) && !empty($payment_setting['paytm_merchant_key']))): ?>
                                                    <div class="tab-pane fade " id="paytm-payment" role="tabpanel" aria-labelledby="paytm-payment">
                                                        <form method="post" action="<?php echo e(route('invoice.pay.with.paytm')); ?>" class="require-validation" id="paytm-payment-form">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    
                                                                    <div class="form-group">
                                                                        
                                                                        <label for="mobile" class="form-control-label text-dark"><?php echo e(__('Mobile Number')); ?></label>
                                                                        <input type="text" id="mobile" name="mobile" class="form-control mobile" data-from="mobile" placeholder="<?php echo e(__('Enter Mobile Number')); ?>" required>
                                                                    </div>
                                                                    <label for="amount" class="form-control-label"><?php echo e(__('Amount')); ?></label>
                                                                    <div class="form-icon-addon">
                                                                        <span><?php echo e(isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'); ?></span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="<?php echo e($invoice->getDue()); ?>" min="0" step="0.01" max="<?php echo e($invoice->getDue()); ?>" id="amount">
                                                                        <input type="hidden" value="<?php echo e($invoice->id); ?>" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="submit" value="<?php echo e(__('Make Payment')); ?>" class="btn-create badge-blue">
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if(isset($payment_setting['is_skrill_enabled']) && $payment_setting['is_skrill_enabled'] == 'on'): ?>
                                                <?php if((isset($payment_setting['skrill_email']) && !empty($payment_setting['skrill_email']))): ?>
                                                    <div class="tab-pane fade " id="skrill-payment" role="tabpanel" aria-labelledby="skrill-payment">
                                                        <form method="post" action="<?php echo e(route('invoice.pay.with.skrill')); ?>" class="require-validation" id="skrill-payment-form">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label"><?php echo e(__('Amount')); ?></label>
                                                                    <div class="form-icon-addon">
                                                                        <span><?php echo e(isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'); ?></span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="<?php echo e($invoice->getDue()); ?>" min="0" step="0.01" max="<?php echo e($invoice->getDue()); ?>" id="amount">
                                                                        <input type="hidden" value="<?php echo e($invoice->id); ?>" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                                $skrill_data = [
                                                                    'transaction_id' => md5(date('Y-m-d') . strtotime('Y-m-d H:i:s') . 'user_id'),
                                                                    'user_id' => 'user_id',
                                                                    'amount' => 'amount',
                                                                    'currency' => 'currency',
                                                                ];
                                                                session()->put('skrill_data', $skrill_data);
                                                            ?>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="submit" value="<?php echo e(__('Make Payment')); ?>" class="btn-create badge-blue">
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if(isset($payment_setting['is_coingate_enabled']) && $payment_setting['is_coingate_enabled'] == 'on'): ?>
                                                <?php if((isset($payment_setting['coingate_auth_token']) && !empty($payment_setting['coingate_auth_token']))): ?>
                                                    <div class="tab-pane fade " id="coingate-payment" role="tabpanel" aria-labelledby="coingate-payment">
                                                        <form method="post" action="<?php echo e(route('invoice.pay.with.coingate')); ?>" class="require-validation" id="coingate-payment-form">
                                                            <?php echo csrf_field(); ?>
                                                            <div class="row">
                                                                <div class="form-group col-md-12">
                                                                    <label for="amount" class="form-control-label"><?php echo e(__('Amount')); ?></label>
                                                                    <div class="form-icon-addon">
                                                                        <span><?php echo e(isset($payment_setting['currency_symbol'])?$payment_setting['currency_symbol']:'$'); ?></span>
                                                                        <input class="form-control" required="required" min="0" name="amount" type="number" value="<?php echo e($invoice->getDue()); ?>" min="0" step="0.01" max="<?php echo e($invoice->getDue()); ?>" id="amount">
                                                                        <input type="hidden" value="<?php echo e($invoice->id); ?>" name="invoice_id">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12 form-group mt-3 text-right">
                                                                <input type="submit" value="<?php echo e(__('Make Payment')); ?>" class="btn-create badge-blue">
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/invoices/view.blade.php ENDPATH**/ ?>