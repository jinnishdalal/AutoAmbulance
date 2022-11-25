<?php
    $logo=asset(Storage::url('logo/'));
    $company_logo=Utility::getValByName('company_logo');
    $company_favicon=Utility::getValByName('company_favicon');
?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Settings')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>
    <script>
        $(document).on("change", "select[name='invoice_template'], input[name='invoice_color']", function () {
            var template = $("select[name='invoice_template']").val();
            var color = $("input[name='invoice_color']:checked").val();
            $('#invoice_frame').attr('src', '<?php echo e(url('/invoices/preview')); ?>/' + template + '/' + color);
        });

        $(document).on("change", "select[name='estimation_template'], input[name='estimation_color']", function () {
            var template = $("select[name='estimation_template']").val();
            var color = $("input[name='estimation_color']:checked").val();
            $('#estimation_frame').attr('src', '<?php echo e(url('/estimations/preview')); ?>/' + template + '/' + color);
        });
    </script>
    <script type="text/javascript">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('on-off email template')): ?>
        $(document).on("click", ".email-template-checkbox", function () {
            var chbox = $(this);
            $.ajax({
                url: chbox.attr('data-url'),
                data: {_token: $('meta[name="csrf-token"]').attr('content'), status: chbox.val()},
                type: 'POST',
                success: function (response) {
                    if (response.is_success) {
                        show_toastr('<?php echo e(__("Success")); ?>', response.success, 'success');
                        if (chbox.val() == 1) {
                            $('#' + chbox.attr('id')).val(0);
                        } else {
                            $('#' + chbox.attr('id')).val(1);
                        }
                    } else {
                        show_toastr('<?php echo e(__("Error")); ?>', response.error, 'error');
                    }
                },
                error: function (response) {
                    response = response.responseJSON;
                    if (response.is_success) {
                        show_toastr('<?php echo e(__("Error")); ?>', response.error, 'error');
                    } else {
                        show_toastr('<?php echo e(__("Error")); ?>', response, 'error');
                    }
                }
            })
        });
        <?php endif; ?>
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <style type="text/css">
        .text-right {
            text-align: right !important;
            float: right !important;
        }
    </style>
    <div class="row">
        <div class="col-12">
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li>
                    <a class="active" id="contact-tab2" data-toggle="tab" href="#business-setting" role="tab" aria-controls="" aria-selected="false"><?php echo e(__('Business Setting')); ?></a>
                </li>
                <li>
                    <a id="contact-tab4" data-toggle="tab" href="#system-setting" role="tab" aria-controls="" aria-selected="false"><?php echo e(__('System Setting')); ?></a>
                </li>
                <li>
                    <a id="profile-tab3" data-toggle="tab" href="#company-setting" role="tab" aria-controls="" aria-selected="false"><?php echo e(__('Company Setting')); ?></a>
                </li>
                <li>
                    <a id="profile-tab4" data-toggle="tab" href="#company-payment-setting" role="tab" aria-controls="" aria-selected="false"><?php echo e(__('Payment Setting')); ?></a>
                </li>
                <li>
                    <a id="profile-tab6" data-toggle="tab" href="#invoice-setting" role="tab" aria-controls="" aria-selected="false"><?php echo e(__('Invoice Print Setting')); ?></a>
                </li>
                <li>
                    <a id="profile-tab7" data-toggle="tab" href="#estimation-setting" role="tab" aria-controls="" aria-selected="false"><?php echo e(__('Estimation Print Setting')); ?></a>
                </li>
                <li>
                    <a id="profile-tab8" data-toggle="tab" href="#email-notification" role="tab" aria-controls="" aria-selected="false"><?php echo e(__('Email Notification')); ?></a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade fade show active" id="business-setting" role="tabpanel" aria-labelledby="profile-tab3">
                    <?php echo e(Form::model($settings,array('route'=>'business.setting','method'=>'POST','enctype' => "multipart/form-data"))); ?>

                    <div class="row">
                        <div class="col-lg-3 col-sm-6 col-md-6">
                            <h4 class="small-title"><?php echo e(__('Logo')); ?></h4>
                            <div class="card setting-card">
                                <div class="logo-content">
                                    <img src="<?php echo e($logo.'/'.(isset($company_logo) && !empty($company_logo)?$company_logo:'logo.png')); ?>" class="big-logo">
                                </div>
                                <div class="input-file btn-file"><?php echo e(__('Select image')); ?>

                                    <input type="file" class="form-control" name="company_logo" id="company_logo" data-filename="company_logo_update" accept=".jpeg,.jpg,.png">
                                </div>
                                <p class="company_logo_update"></p>
                                <?php $__errorArgs = ['company_logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-company_logo text-xs text-danger" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <p class="lh-160 mb-0 pt-3"><?php echo e(__('These Logo will appear on Estimations and Invoice as well.')); ?></p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-6">
                            <h4 class="small-title"><?php echo e(__('Favicon')); ?></h4>
                            <div class="card setting-card">
                                <div class="logo-content">
                                    <img src="<?php echo e($logo.'/'.(isset($company_favicon) && !empty($company_favicon)?$company_favicon:'favicon.png')); ?>" class="small-logo" alt="">
                                </div>
                                <div class="input-file btn-file"><?php echo e(__('Select image')); ?>

                                    <input type="file" class="form-control" name="company_favicon" id="company_favicon" data-filename="company_favicon_update" accept=".jpeg,.jpg,.png">
                                </div>
                                <p class="company_favicon_update"></p>
                                <?php $__errorArgs = ['company_favicon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-company_favicon text-xs text-danger" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-6">
                            <h4 class="small-title"><?php echo e(__('Settings')); ?></h4>
                            <div class="card setting-card">
                                <div class="form-group">
                                    <?php echo e(Form::label('header_text',__('Header Text'),['class'=>'form-control-label'])); ?>

                                    <?php echo e(Form::text('header_text',null,array('class'=>'form-control','placeholder'=>__('Header Text')))); ?>

                                    <?php $__errorArgs = ['header_text'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <span class="invalid-header_text text-xs text-danger" role="alert"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="form-group">
                                    <?php echo e(Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo e(Form::close()); ?>

                </div>
                <div class="tab-pane fade" id="system-setting" role="tabpanel" aria-labelledby="profile-tab3">
                    <?php echo e(Form::model($settings,array('route'=>'system.settings','method'=>'post'))); ?>

                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-lg-4 col-md-6 col-sm-12 form-group">
                                <?php echo e(Form::label('site_currency',__('Currency *'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('site_currency',null,array('class'=>'form-control '))); ?>

                                <small class="text-xs">
                                    <?php echo e(__('Note: Add currency code as per three-letter ISO code')); ?>.
                                    <a href="https://stripe.com/docs/currencies" target="_blank"><?php echo e(__('you can find out here..')); ?></a>
                                </small>
                                <?php $__errorArgs = ['site_currency'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <br>
                                <span class="text-xs text-danger invalid-site_currency" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 form-group">
                                <?php echo e(Form::label('site_currency_symbol',__('Currency Symbol *'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('site_currency_symbol',null,array('class'=>'form-control'))); ?>

                                <?php $__errorArgs = ['site_currency_symbol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-site_currency_symbol" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 form-group">
                                <label class="form-control-label"><?php echo e(__('Currency Symbol Position')); ?></label>
                                <div class="d-flex radio-check">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="pre" value="pre" name="site_currency_symbol_position" class="custom-control-input" <?php if($settings['site_currency_symbol_position'] == 'pre'): ?> checked <?php endif; ?>>
                                        <label class="custom-control-label form-control-label" for="pre"><?php echo e(__('Pre')); ?></label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="post" value="post" name="site_currency_symbol_position" class="custom-control-input" <?php if($settings['site_currency_symbol_position'] == 'post'): ?> checked <?php endif; ?>>
                                        <label class="custom-control-label form-control-label" for="post"><?php echo e(__('Post')); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 form-group">
                                <label for="site_date_format" class="form-control-label"><?php echo e(__('Date Format')); ?></label>
                                <select type="text" name="site_date_format" class="form-control select2" id="site_date_format">
                                    <option value="M j, Y" <?php if(@$settings['site_date_format'] == 'M j, Y'): ?> selected="selected" <?php endif; ?>>Jan 1,2015</option>
                                    <option value="d-m-Y" <?php if(@$settings['site_date_format'] == 'd-m-Y'): ?> selected="selected" <?php endif; ?>>d-m-y</option>
                                    <option value="m-d-Y" <?php if(@$settings['site_date_format'] == 'm-d-Y'): ?> selected="selected" <?php endif; ?>>m-d-y</option>
                                    <option value="Y-m-d" <?php if(@$settings['site_date_format'] == 'Y-m-d'): ?> selected="selected" <?php endif; ?>>y-m-d</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 form-group">
                                <label for="site_time_format" class="form-control-label"><?php echo e(__('Time Format')); ?></label>
                                <select type="text" name="site_time_format" class="form-control select2" id="site_time_format">
                                    <option value="g:i A" <?php if(@$settings['site_time_format'] == 'g:i A'): ?> selected="selected" <?php endif; ?>>10:30 PM</option>
                                    <option value="g:i a" <?php if(@$settings['site_time_format'] == 'g:i a'): ?> selected="selected" <?php endif; ?>>10:30 pm</option>
                                    <option value="H:i" <?php if(@$settings['site_time_format'] == 'H:i'): ?> selected="selected" <?php endif; ?>>22:30</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 form-group">
                                <?php echo e(Form::label('invoice_prefix',__('Invoice Prefix'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('invoice_prefix',null,array('class'=>'form-control'))); ?>

                                <?php $__errorArgs = ['invoice_prefix'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-invoice_prefix" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 form-group">
                                <?php echo e(Form::label('bug_prefix',__('Bug Prefix'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('bug_prefix',null,array('class'=>'form-control'))); ?>

                                <?php $__errorArgs = ['bug_prefix'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-bug_prefix" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 form-group">
                                <?php echo e(Form::label('estimation_prefix',__('Estimation Prefix'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('estimation_prefix',null,array('class'=>'form-control'))); ?>

                                <?php $__errorArgs = ['estimation_prefix'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-estimation_prefix" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 form-group">
                                <label for="footer_title" class="form-control-label"><?php echo e(__('Invoice/Estimation Title')); ?>  </label>
                                <input type="text" name="footer_title" class="form-control" id="footer_title" value="<?php echo e($settings['footer_title']); ?>">
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 form-group">
                                <label for="footer_note" class="form-control-label"><?php echo e(__('Invoice/Estimation Note')); ?>  </label>
                                <textarea name="footer_note" class="form-control" id="footer_note"><?php echo e($settings['footer_note']); ?></textarea>
                            </div>
                            <div class="form-group col-md-12 text-right">
                                <?php echo e(Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))); ?>

                            </div>
                        </div>
                    </div>
                    <?php echo e(Form::close()); ?>

                </div>
                <div class="tab-pane fade" id="company-setting" role="tabpanel" aria-labelledby="contact-tab4">
                    <?php echo e(Form::model($settings,array('route'=>'company.settings','method'=>'post'))); ?>

                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                <?php echo e(Form::label('company_name *',__('Company Name *'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('company_name',null,array('class'=>'form-control '))); ?>

                                <?php $__errorArgs = ['company_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-company_name" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                <?php echo e(Form::label('company_address',__('Address'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('company_address',null,array('class'=>'form-control '))); ?>

                                <?php $__errorArgs = ['company_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-company_address" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                <?php echo e(Form::label('company_city',__('City'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('company_city',null,array('class'=>'form-control '))); ?>

                                <?php $__errorArgs = ['company_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-company_city" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                <?php echo e(Form::label('company_state',__('State'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('company_state',null,array('class'=>'form-control '))); ?>

                                <?php $__errorArgs = ['company_state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-company_state" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                <?php echo e(Form::label('company_zipcode',__('Zip/Post Code'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('company_zipcode',null,array('class'=>'form-control'))); ?>

                                <?php $__errorArgs = ['company_zipcode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-company_zipcode" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                <?php echo e(Form::label('company_country',__('Country'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('company_country',null,array('class'=>'form-control '))); ?>

                                <?php $__errorArgs = ['company_country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-company_country" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                <?php echo e(Form::label('company_telephone',__('Telephone'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('company_telephone',null,array('class'=>'form-control'))); ?>

                                <?php $__errorArgs = ['company_telephone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-company_telephone" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                <?php echo e(Form::label('company_email',__('System Email *'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('company_email',null,array('class'=>'form-control'))); ?>

                                <?php $__errorArgs = ['company_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-company_email" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 form-group">
                                <?php echo e(Form::label('company_email_from_name',__('Email (From Name) *'),['class'=>'form-control-label'])); ?>

                                <?php echo e(Form::text('company_email_from_name',null,array('class'=>'form-control '))); ?>

                                <?php $__errorArgs = ['company_email_from_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-xs text-danger invalid-company_email_from_name" role="alert"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="form-group col-md-12 text-right">
                                <?php echo e(Form::submit(__('Save Change'),array('class'=>'btn-create badge-blue'))); ?>

                            </div>
                        </div>
                    </div>
                    <?php echo e(Form::close()); ?>

                </div>
                <div class="tab-pane fade" id="company-payment-setting" role="tabpanel" aria-labelledby="contact-tab4">
                    <h4 class="header-title mb-3"><?php echo e(__('Payment Setting')); ?></h4>
                    <small class="text-dark font-weight-bold"><?php echo e(__("This detail will use for collect payment on invoice from clients. On invoice client will find out pay now button based on your below configuration.")); ?></small></br></br>
                    
                    <form id="setting-form" method="post" action="<?php echo e(route('payment.settings')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row">
                            <div class="col-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                <label class="form-control-label"><?php echo e(__('Currency')); ?> *</label>
                                                <input type="text" name="currency" class="form-control" id="currency" value="<?php echo e((!isset($payment['currency']) || is_null($payment['currency'])) ? '' : $payment['currency']); ?>" required>
                                                <small class="text-xs">
                                                    <?php echo e(__('Note: Add currency code as per three-letter ISO code')); ?>.
                                                    <a href="https://stripe.com/docs/currencies" target="_blank"><?php echo e(__('you can find out here..')); ?></a>
                                                </small>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 form-group">
                                                <label for="currency_symbol" class="form-control-label"><?php echo e(__('Currency Symbol')); ?></label>
                                                <input type="text" name="currency_symbol" class="form-control" id="currency_symbol" value="<?php echo e((!isset($payment['currency_symbol']) || is_null($payment['currency_symbol'])) ? '' : $payment['currency_symbol']); ?>" required>
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
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i><?php echo e(__('Stripe')); ?></h6>

                                </div>
                                <div id="collapse-2-2" class="collapse" aria-labelledby="heading-2-2" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5"><?php echo e(__('Stripe')); ?></h5>
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch text-right">
                                                    <input type="hidden" name="is_stripe_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_stripe_enabled" id="is_stripe_enabled" <?php echo e(isset($payment['is_stripe_enabled']) && $payment['is_stripe_enabled'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="is_stripe_enabled"><?php echo e(__('Enable Stripe')); ?></label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stripe_key"><?php echo e(__('Stripe Key')); ?></label>
                                                    <input class="form-control" placeholder="<?php echo e(__('Stripe Key')); ?>" name="stripe_key" type="text" value="<?php echo e((!isset($payment['stripe_key']) || is_null($payment['stripe_key'])) ? '' : $payment['stripe_key']); ?>" id="stripe_key">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stripe_secret"><?php echo e(__('Stripe Secret')); ?></label>
                                                    <input class="form-control " placeholder="<?php echo e(__('Stripe Secret')); ?>" name="stripe_secret" type="text" value="<?php echo e((!isset($payment['stripe_secret']) || is_null($payment['stripe_secret'])) ? '' : $payment['stripe_secret']); ?>" id="stripe_secret">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="stripe_secret"><?php echo e(__('Stripe_Webhook_Secret')); ?></label>
                                                    <input class="form-control " placeholder="<?php echo e(__('Enter Stripe Webhook Secret')); ?>" name="stripe_webhook_secret" type="text" value="<?php echo e((!isset($payment['stripe_webhook_secret']) || is_null($payment['stripe_webhook_secret'])) ? '' : $payment['stripe_webhook_secret']); ?>" id="stripe_webhook_secret">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paypal -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-3" data-toggle="collapse" role="button" data-target="#collapse-2-3" aria-expanded="false" aria-controls="collapse-2-3">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i><?php echo e(__('Paypal')); ?></h6>
                                </div>
                                <div id="collapse-2-3" class="collapse" aria-labelledby="heading-2-3" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5"><?php echo e(__('Paypal')); ?></h5>
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch text-right">
                                                    <input type="hidden" name="is_paypal_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_paypal_enabled" id="is_paypal_enabled" <?php echo e(isset($payment['is_paypal_enabled']) && $payment['is_paypal_enabled'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="is_paypal_enabled"><?php echo e(__('Enable Paypal')); ?></label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pb-4">
                                                <label class="paypal-label form-control-label" for="paypal_mode"><?php echo e(__('Paypal Mode')); ?></label> <br>
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-primary btn-sm <?php echo e(!isset($payment['paypal_mode']) || $payment['paypal_mode'] == '' || $payment['paypal_mode'] == 'sandbox' ? 'active' : ''); ?>">
                                                        <input type="radio" name="paypal_mode" value="sandbox" <?php echo e(!isset($payment['paypal_mode']) || $payment['paypal_mode'] == '' || $payment['paypal_mode'] == 'sandbox' ? 'checked="checked"' : ''); ?>><?php echo e(__('Sandbox')); ?>  
                                                    </label>
                                                    <label class="btn btn-primary btn-sm <?php echo e(isset($payment['paypal_mode']) && $payment['paypal_mode'] == 'live' ? 'active' : ''); ?>">
                                                        <input type="radio" name="paypal_mode" value="live" <?php echo e(isset($payment['paypal_mode']) && $payment['paypal_mode'] == 'live' ? 'checked="checked"' : ''); ?>><?php echo e(__('Live')); ?>

                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paypal_client_id"><?php echo e(__('Client ID')); ?></label>
                                                    <input type="text" name="paypal_client_id" id="paypal_client_id" class="form-control" value="<?php echo e((!isset($payment['paypal_client_id']) || is_null($payment['paypal_client_id'])) ? '' : $payment['paypal_client_id']); ?>" placeholder="<?php echo e(__('Client ID')); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paypal_secret_key"><?php echo e(__('Secret Key')); ?></label>
                                                    <input type="text" name="paypal_secret_key" id="paypal_secret_key" class="form-control" value="<?php echo e((!isset($payment['paypal_secret_key']) || is_null($payment['paypal_secret_key'])) ? '' : $payment['paypal_secret_key']); ?>" placeholder="<?php echo e(__('Secret Key')); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paystack -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-6" data-toggle="collapse" role="button" data-target="#collapse-2-6" aria-expanded="false" aria-controls="collapse-2-6">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i><?php echo e(__('Paystack')); ?></h6>
                                </div>
                                <div id="collapse-2-6" class="collapse" aria-labelledby="heading-2-6" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5"><?php echo e(__('Paystack')); ?></h5>
                                                
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch text-right">
                                                    <input type="hidden" name="is_paystack_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_paystack_enabled" id="is_paystack_enabled" <?php echo e(isset($payment['is_paystack_enabled']) && $payment['is_paystack_enabled'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="is_paystack_enabled"><?php echo e(__('Enable Paystack')); ?> </label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paypal_client_id"><?php echo e(__('Public Key')); ?></label>
                                                    <input type="text" name="paystack_public_key" id="paystack_public_key" class="form-control" value="<?php echo e((!isset($payment['paystack_public_key']) || is_null($payment['paystack_public_key'])) ? '' : $payment['paystack_public_key']); ?>" placeholder="<?php echo e(__('Public Key')); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paystack_secret_key"><?php echo e(__('Secret Key')); ?></label>
                                                    <input type="text" name="paystack_secret_key" id="paystack_secret_key" class="form-control" value="<?php echo e((!isset($payment['paystack_secret_key']) || is_null($payment['paystack_secret_key'])) ? '' : $payment['paystack_secret_key']); ?>" placeholder="<?php echo e(__('Secret Key')); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FLUTTERWAVE -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-7" data-toggle="collapse" role="button" data-target="#collapse-2-7" aria-expanded="false" aria-controls="collapse-2-7">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i><?php echo e(__('Flutterwave')); ?></h6>
                                </div>
                                <div id="collapse-2-7" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5"><?php echo e(__('Flutterwave')); ?></h5>
                                                
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch text-right">
                                                    <input type="hidden" name="is_flutterwave_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_flutterwave_enabled" id="is_flutterwave_enabled" <?php echo e(isset($payment['is_flutterwave_enabled']) && $payment['is_flutterwave_enabled'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="is_flutterwave_enabled"><?php echo e(__('Enable Flutterwave')); ?></label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paypal_client_id"><?php echo e(__('Public Key')); ?></label>
                                                    <input type="text" name="flutterwave_public_key" id="flutterwave_public_key" class="form-control" value="<?php echo e((!isset($payment['flutterwave_public_key']) || is_null($payment['flutterwave_public_key'])) ? '' : $payment['flutterwave_public_key']); ?>" placeholder="Public Key">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paystack_secret_key"><?php echo e(__('Secret Key')); ?></label>
                                                    <input type="text" name="flutterwave_secret_key" id="flutterwave_secret_key" class="form-control" value="<?php echo e((!isset($payment['flutterwave_secret_key']) || is_null($payment['flutterwave_secret_key'])) ? '' : $payment['flutterwave_secret_key']); ?>" placeholder="Secret Key">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Razorpay -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-8" aria-expanded="false" aria-controls="collapse-2-8">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i><?php echo e(__('Razorpay')); ?></h6>
                                </div>
                                <div id="collapse-2-8" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5"><?php echo e(__('Razorpay')); ?></h5>
                                                
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch text-right">
                                                    <input type="hidden" name="is_razorpay_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_razorpay_enabled" id="is_razorpay_enabled" <?php echo e(isset($payment['is_razorpay_enabled']) && $payment['is_razorpay_enabled'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="is_razorpay_enabled">Enable Razorpay</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paypal_client_id">Public Key</label>

                                                    <input type="text" name="razorpay_public_key" id="razorpay_public_key" class="form-control" value="<?php echo e((!isset($payment['razorpay_public_key']) || is_null($payment['razorpay_public_key'])) ? '' : $payment['razorpay_public_key']); ?>" placeholder="Public Key">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="paystack_secret_key">Secret Key</label>
                                                    <input type="text" name="razorpay_secret_key" id="razorpay_secret_key" class="form-control" value="<?php echo e((!isset($payment['razorpay_secret_key']) || is_null($payment['razorpay_secret_key'])) ? '' : $payment['razorpay_secret_key']); ?>" placeholder="Secret Key">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paytm -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-14" data-toggle="collapse" role="button" data-target="#collapse-2-14" aria-expanded="false" aria-controls="collapse-2-14">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i><?php echo e(__('Paytm')); ?></h6>
                                </div>
                                <div id="collapse-2-14" class="collapse" aria-labelledby="heading-2-14" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5"><?php echo e(__('Paytm')); ?></h5>
                                                
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch text-right">
                                                    <input type="hidden" name="is_paytm_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_paytm_enabled" id="is_paytm_enabled" <?php echo e(isset($payment['is_paytm_enabled']) && $payment['is_paytm_enabled'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="is_paytm_enabled">Enable Paytm</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pb-4">
                                                <label class="paypal-label form-control-label" for="paypal_mode">Paytm Environment</label> <br>
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-primary btn-sm <?php echo e(!isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'active' : ''); ?>">
                                                        <input type="radio" name="paytm_mode" value="local" <?php echo e(!isset($payment['paytm_mode']) || $payment['paytm_mode'] == '' || $payment['paytm_mode'] == 'local' ? 'checked="checked"' : ''); ?>>Local
                                                    </label>
                                                    <label class="btn btn-primary btn-sm <?php echo e(isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'active' : ''); ?>">
                                                        <input type="radio" name="paytm_mode" value="production" <?php echo e(isset($payment['paytm_mode']) && $payment['paytm_mode'] == 'production' ? 'checked="checked"' : ''); ?>>Production
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="paytm_public_key">Merchant ID</label>
                                                    <input type="text" name="paytm_merchant_id" id="paytm_merchant_id" class="form-control" value="<?php echo e((!isset($payment['paytm_merchant_id']) || is_null($payment['paytm_merchant_id'])) ? '' : $payment['paytm_merchant_id']); ?>" placeholder="Merchant ID">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="paytm_secret_key">Merchant Key</label>
                                                    <input type="text" name="paytm_merchant_key" id="paytm_merchant_key" class="form-control" value="<?php echo e((!isset($payment['paytm_merchant_key']) || is_null($payment['paytm_merchant_key'])) ? '' : $payment['paytm_merchant_key']); ?>" placeholder="Merchant Key">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="paytm_industry_type">Industry Type</label>
                                                    <input type="text" name="paytm_industry_type" id="paytm_industry_type" class="form-control" value="<?php echo e((!isset($payment['paytm_industry_type']) || is_null($payment['paytm_industry_type'])) ? '' : $payment['paytm_industry_type']); ?>" placeholder="Industry Type">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mercado Pago-->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-12" data-toggle="collapse" role="button" data-target="#collapse-2-12" aria-expanded="false" aria-controls="collapse-2-12">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i><?php echo e(__('Mercado Pago')); ?></h6>
                                </div>
                                <div id="collapse-2-12" class="collapse" aria-labelledby="heading-2-12" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5"><?php echo e(__('Mercado Pago')); ?></h5>
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch text-right">
                                                    <input type="hidden" name="is_mercado_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_mercado_enabled" id="is_mercado_enabled" <?php echo e(isset($payment['is_mercado_enabled']) && $payment['is_mercado_enabled'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="is_mercado_enabled">Enable Mercado Pago</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mercado_app_id">App ID</label>
                                                    <input type="text" name="mercado_app_id" id="mercado_app_id" class="form-control" value="<?php echo e((!isset($payment['mercado_app_id']) || is_null($payment['mercado_app_id'])) ? '' : $payment['mercado_app_id']); ?>" placeholder="App ID">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mercado_secret_key">App Secret KEY</label>
                                                    <input type="text" name="mercado_secret_key" id="mercado_secret_key" class="form-control" value="<?php echo e((!isset($payment['mercado_secret_key']) || is_null($payment['mercado_secret_key'])) ? '' : $payment['mercado_secret_key']); ?>" placeholder="App Secret Key">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Mollie -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-10" aria-expanded="false" aria-controls="collapse-2-10">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i><?php echo e(__('Mollie')); ?></h6>
                                </div>
                                <div id="collapse-2-10" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5"><?php echo e(__('Mollie')); ?></h5>
                                                
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch text-right">
                                                    <input type="hidden" name="is_mollie_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_mollie_enabled" id="is_mollie_enabled" <?php echo e(isset($payment['is_mollie_enabled']) && $payment['is_mollie_enabled'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="is_mollie_enabled">Enable Mollie</label>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mollie_api_key">Mollie Api Key</label>
                                                    <input type="text" name="mollie_api_key" id="mollie_api_key" class="form-control" value="<?php echo e((!isset($payment['mollie_api_key']) || is_null($payment['mollie_api_key'])) ? '' : $payment['mollie_api_key']); ?>" placeholder="Mollie Api Key">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mollie_profile_id">Mollie Profile Id</label>
                                                    <input type="text" name="mollie_profile_id" id="mollie_profile_id" class="form-control" value="<?php echo e((!isset($payment['mollie_profile_id']) || is_null($payment['mollie_profile_id'])) ? '' : $payment['mollie_profile_id']); ?>" placeholder="Mollie Profile Id">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mollie_partner_id">Mollie Partner Id</label>
                                                    <input type="text" name="mollie_partner_id" id="mollie_partner_id" class="form-control" value="<?php echo e((!isset($payment['mollie_partner_id']) || is_null($payment['mollie_partner_id'])) ? '' : $payment['mollie_partner_id']); ?>" placeholder="Mollie Partner Id">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Skrill -->
                            <div class="card">
                                <div class="card-header py-4" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-13" aria-expanded="false" aria-controls="collapse-2-10">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i><?php echo e(__('Skrill')); ?></h6>
                                </div>
                                <div id="collapse-2-13" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5"><?php echo e(__('Skrill')); ?></h5>
                                                
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch text-right">
                                                    <input type="hidden" name="is_skrill_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_skrill_enabled" id="is_skrill_enabled" <?php echo e(isset($payment['is_skrill_enabled']) && $payment['is_skrill_enabled'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="is_skrill_enabled">Enable Skrill</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="mollie_api_key">Skrill Email</label>
                                                    <input type="text" name="skrill_email" id="skrill_email" class="form-control" value="<?php echo e((!isset($payment['skrill_email']) || is_null($payment['skrill_email'])) ? '' : $payment['skrill_email']); ?>" placeholder="Enter Skrill Email">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- CoinGate -->
                            <div class="card">
                                <div class="card-header py-4 collapsed" id="heading-2-8" data-toggle="collapse" role="button" data-target="#collapse-2-15" aria-expanded="false" aria-controls="collapse-2-10">
                                    <h6 class="mb-0"><i class="far fa-credit-card mr-3"></i><?php echo e(__('CoinGate')); ?></h6>
                                </div>
                                <div id="collapse-2-15" class="collapse" aria-labelledby="heading-2-7" data-parent="#accordion-2" style="">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6 py-2">
                                                <h5 class="h5"><?php echo e(__('CoinGate')); ?></h5>
                                            </div>
                                            <div class="col-6 py-2 text-right">
                                                <div class="custom-control custom-switch text-right">
                                                    <input type="hidden" name="is_coingate_enabled" value="off">
                                                    <input type="checkbox" class="custom-control-input" name="is_coingate_enabled" id="is_coingate_enabled" <?php echo e(isset($payment['is_coingate_enabled']) && $payment['is_coingate_enabled'] == 'on' ? 'checked="checked"' : ''); ?>>
                                                    <label class="custom-control-label form-control-label" for="is_coingate_enabled">Enable CoinGate</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pb-4">
                                                <label class="coingate-label form-control-label" for="coingate_mode">CoinGate Mode</label> <br>
                                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                    <label class="btn btn-primary btn-sm <?php echo e(!isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'active' : ''); ?>">
                                                        <input type="radio" name="coingate_mode" value="sandbox" <?php echo e(!isset($payment['coingate_mode']) || $payment['coingate_mode'] == '' || $payment['coingate_mode'] == 'sandbox' ? 'checked="checked"' : ''); ?>>Sandbox
                                                    </label>
                                                    <label class="btn btn-primary btn-sm <?php echo e(isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'active' :''); ?>">
                                                        <input type="radio" name="coingate_mode" value="live" <?php echo e(isset($payment['coingate_mode']) && $payment['coingate_mode'] == 'live' ? 'checked="checked"' : ''); ?>>Live
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="coingate_auth_token">CoinGate Auth Token</label>
                                                    <input type="text" name="coingate_auth_token" id="coingate_auth_token" class="form-control" value="<?php echo e((!isset($payment['coingate_auth_token']) || is_null($payment['coingate_auth_token'])) ? '' : $payment['coingate_auth_token']); ?>" placeholder="CoinGate Auth Token">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-right">
                            <div class="form-group">
                                <input class="btn-create badge-blue" type="submit" value="<?php echo e(__('Save Changes')); ?>">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="invoice-setting" role="tabpanel" aria-labelledby="profile-tab6">
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-md-2">
                                <form id="setting-form" method="post" action="<?php echo e(route('template.setting')); ?>" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group">
                                        <label for="address" class="form-control-label"><?php echo e(__('Invoice Template')); ?></label>
                                        <select class="form-control select2" name="invoice_template">
                                            <?php $__currentLoopData = Utility::templateData()['templates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>" <?php echo e((isset($settings['invoice_template']) && $settings['invoice_template'] == $key) ? 'selected' : ''); ?>><?php echo e($template); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label"><?php echo e(__('Color Input')); ?></label>
                                        <div class="row gutters-xs">
                                            <?php $__currentLoopData = Utility::templateData()['colors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-auto">
                                                    <label class="colorinput">
                                                        <input name="invoice_color" type="radio" value="<?php echo e($color); ?>" class="colorinput-input" <?php echo e((isset($settings['invoice_color']) && $settings['invoice_color'] == $color) ? 'checked' : ''); ?>>
                                                        <span class="colorinput-color" style="background: #<?php echo e($color); ?>"></span>
                                                    </label>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label"><?php echo e(__('Invoice Logo')); ?></label>
                                        <div class="choose-file form-group">
                                            <label for="invoice_logo" class="form-control-label">
                                                <div><?php echo e(__('Choose file here')); ?></div>
                                                <input type="file" class="form-control" name="invoice_logo" id="invoice_logo" data-filename="invoice_logo_update" accept=".jpeg,.jpg,.png,.doc,.pdf">
                                            </label><br>
                                            <p class="invoice_logo_update"></p>
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <input type="submit" value="<?php echo e(__('Save')); ?>" class="btn-create badge-blue">
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-10">
                                <?php if(isset($settings['invoice_template']) && isset($settings['invoice_color'])): ?>
                                    <iframe id="invoice_frame" class="w-100 h-1050" frameborder="0" src="<?php echo e(route('invoice.preview',[$settings['invoice_template'],$settings['invoice_color']])); ?>"></iframe>
                                <?php else: ?>
                                    <iframe id="invoice_frame" class="w-100 h-1050" frameborder="0" src="<?php echo e(route('invoice.preview',['template1','fffff'])); ?>"></iframe>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="estimation-setting" role="tabpanel" aria-labelledby="profile-tab7">
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-md-2">
                                <form id="setting-form" method="post" action="<?php echo e(route('template.setting')); ?>" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group">
                                        <label for="address" class="form-control-label"><?php echo e(__('Estimation Template')); ?></label>
                                        <select class="form-control select2" name="estimation_template">
                                            <?php $__currentLoopData = Utility::templateData()['templates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($key); ?>" <?php echo e((isset($settings['estimation_template']) && $settings['estimation_template'] == $key) ? 'selected' : ''); ?>><?php echo e($template); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label"><?php echo e(__('Color Input')); ?></label>
                                        <div class="row gutters-xs">
                                            <?php $__currentLoopData = Utility::templateData()['colors']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="col-auto">
                                                    <label class="colorinput">
                                                        <input name="estimation_color" type="radio" value="<?php echo e($color); ?>" class="colorinput-input" <?php echo e((isset($settings['estimation_color']) && $settings['estimation_color'] == $color) ? 'checked' : ''); ?>>
                                                        <span class="colorinput-color" style="background: #<?php echo e($color); ?>"></span>
                                                    </label>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-control-label"><?php echo e(__('Estimation Logo')); ?></label>
                                        <div class="choose-file form-group">
                                            <label for="estimation_logo" class="form-control-label">
                                                <div><?php echo e(__('Choose file here')); ?></div>
                                                <input type="file" class="form-control" name="estimation_logo" id="estimation_logo" data-filename="estimation_logo_update" accept=".jpeg,.jpg,.png,.doc,.pdf">
                                            </label><br>
                                            <p class="estimation_logo_update"></p>
                                        </div>
                                    </div>
                                    <div class="form-group mt-2">
                                        <input type="submit" value="<?php echo e(__('Save')); ?>" class="btn-create badge-blue">
                                    </div>

                                </form>
                            </div>
                            <div class="col-md-10">
                                <?php if(isset($settings['estimation_template']) && isset($settings['estimation_color'])): ?>
                                    <iframe id="estimation_frame" frameborder="0" class="w-100 h-1050" src="<?php echo e(route('estimations.preview',[$settings['estimation_template'],$settings['estimation_color']])); ?>"></iframe>
                                <?php else: ?>
                                    <iframe id="estimation_frame" frameborder="0" class="w-100 h-1050" src="<?php echo e(route('estimations.preview',['template1','fffff'])); ?>"></iframe>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="email-notification" role="tabpanel" aria-labelledby="profile-tab8">
                    <div class="card bg-none">
                        <div class="row company-setting">
                            <div class="col-12">
                                <table class="table table-striped dataTable">
                                    <thead>
                                    <tr>
                                        <th class="w-75"> <?php echo e(__('Name')); ?></th>
                                        <th class="text-center"> <?php echo e(__('On/Off')); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $__currentLoopData = $EmailTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $EmailTemplate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="">
                                            <td><?php echo e($EmailTemplate->name); ?></td>
                                            <td class="text-center">
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('on-off email template')): ?>
                                                    <div class="form-group col-md-12">
                                                        <label class="switch ml-3">
                                                            <input type="checkbox" class="email-template-checkbox" name="site_enable_stripe" id="email_tempalte_<?php echo e($EmailTemplate->template->id); ?>" <?php if($EmailTemplate->template->is_active == 1): ?> checked="checked" <?php endif; ?> type="checkbox" value="<?php echo e($EmailTemplate->template->is_active); ?>" data-url="<?php echo e(route('status.email.language',[$EmailTemplate->template->id])); ?>">
                                                            <span class="slider1 round"></span>
                                                        </label>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/settings/company.blade.php ENDPATH**/ ?>