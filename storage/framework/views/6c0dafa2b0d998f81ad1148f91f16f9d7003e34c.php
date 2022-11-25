<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Edit Profile')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-xl-3 col-lg-4 col-md-4 col-sm-12">
            <div class="card profile-card">
                <div class="icon-user avatar rounded-circle">
                    <img src="<?php echo e((!empty($userDetail->avatar))? asset(Storage::url('avatar/'.$userDetail->avatar)) : asset(Storage::url('avatar/avatar.png'))); ?>" class="icon-user avatar rounded-circle">
                </div>
                <h4 class="h4 mb-0 mt-2"><?php echo e($userDetail->name); ?></h4>
                <div class="sal-right-card">
                    <span class="badge badge-pill badge-blue"><?php echo e($userDetail->type); ?></span>
                </div>
                <h6 class="office-time mb-0 mt-4"><?php echo e($userDetail->email); ?></h6>
            </div>
        </div>
        <div class="col-xl-9 col-lg-8 col-md-8 col-sm-12">
            <section class="col-lg-12 pricing-plan card">
                <div class="our-system password-card p-3">
                    <div class="row">
                        <ul class="nav nav-tabs my-4">
                            <li>
                                <a data-toggle="tab" href="#personal_info" class="active"><?php echo e(__('Personal info')); ?></a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#change_password" class=""><?php echo e(__('Change Password')); ?></a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div id="personal_info" class="tab-pane in active">
                                <?php echo e(Form::model($userDetail,array('route' => array('update.account'), 'method' => 'POST', 'enctype' => "multipart/form-data"))); ?>

                                <div class="row">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="name" class="form-control-label text-dark"><?php echo e(__('Name')); ?></label>
                                            <input class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="name" type="text" id="name" placeholder="<?php echo e(__('Enter Your Name')); ?>" value="<?php echo e($userDetail->name); ?>" autocomplete="name">
                                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback text-danger text-xs" role="alert"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="email" class="form-control-label text-dark"><?php echo e(__('Email')); ?></label>
                                            <input class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" type="text" id="email" placeholder="<?php echo e(__('Enter Your Email Address')); ?>" value="<?php echo e($userDetail->email); ?>" autocomplete="email">
                                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback text-danger text-xs" role="alert"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label text-dark"><?php echo e(__('Avatar')); ?></label>
                                            <div class="choose-file">
                                                <label for="avatar">
                                                    <div><?php echo e(__('Choose file here')); ?></div>
                                                    <input class="form-control" name="profile" type="file" id="avatar" accept="image/*" data-filename="profile_update">
                                                </label>
                                                <p class="profile_update"></p>
                                            </div>
                                            <?php $__errorArgs = ['avatar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <span class="invalid-feedback text-danger text-xs" role="alert"><?php echo e($message); ?></span>
                                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        </div>
                                        <span class="clearfix"></span>
                                        <span class="text-xs text-muted"><?php echo e(__('Please upload a valid image file. Size of image should not be more than 2MB.')); ?></span>
                                    </div>
                                    <div class="col-lg-12 text-right">
                                        <input type="submit" value="<?php echo e(__('Save Changes')); ?>" class="btn-create badge-blue">
                                    </div>
                                </div>
                                <?php echo e(Form::close()); ?>

                            </div>
                            <div id="change_password" class="tab-pane">
                                <?php echo e(Form::model($userDetail,array('route' => array('update.password',$userDetail->id), 'method' => 'POST'))); ?>

                                <div class="row">
                                    <div class="col-lg-6 col-sm-6 form-group">
                                        <label for="current_password" class="form-control-label text-dark"><?php echo e(__('Current Password')); ?></label>
                                        <input class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="current_password" type="password" id="current_password" autocomplete="current_password" placeholder="<?php echo e(__('Enter Current Password')); ?>">
                                        <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback text-danger text-xs" role="alert"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>

                                    <div class="col-lg-6 col-sm-6 form-group">
                                        <label for="new_password" class="form-control-label text-dark"><?php echo e(__('Password')); ?></label>
                                        <input class="form-control <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="new_password" type="password" autocomplete="new_password" id="new_password" placeholder="<?php echo e(__('Enter New Password')); ?>">
                                        <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback text-danger text-xs" role="alert"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="col-lg-6 col-sm-6 form-group">
                                        <label for="confirm_password" class="form-control-label text-dark"><?php echo e(__('Confirm Password')); ?></label>
                                        <input class="form-control <?php $__errorArgs = ['confirm_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="confirm_password" type="password" autocomplete="confirm_password" id="confirm_password" placeholder="<?php echo e(__('Confirm New Password')); ?>">
                                        <?php $__errorArgs = ['confirm_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback text-danger text-xs" role="alert"><?php echo e($message); ?></span>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="col-lg-12 text-right">
                                        <input type="submit" value="<?php echo e(__('Change Password')); ?>" class="btn-create badge-blue">
                                    </div>
                                </div>
                                <?php echo e(Form::close()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/user/profile.blade.php ENDPATH**/ ?>