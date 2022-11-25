<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage User')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <div class="all-button-box row d-flex justify-content-end">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create user')): ?>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-ajax-popup="true" data-title="<?php echo e(__('Create User')); ?>" data-url="<?php echo e(route('users.create')); ?>">
                    <i class="fas fa-plus"></i> <?php echo e(__('Add New')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-3 col-sm-6 col-md-6">
                <div class="card profile-card">
                    <?php if(Gate::check('edit user') || Gate::check('delete user')): ?>
                        <div class="edit-profile user-text">
                            <?php if($user->is_active == 1): ?>
                                <?php if((Gate::check('edit user') || Gate::check('delete user'))): ?>
                                    <div class="dropdown action-item">
                                        <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit user')): ?>
                                                <a href="#" class="dropdown-item" data-url="<?php echo e(route('users.edit',$user->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit User')); ?>"><?php echo e(__('Edit')); ?></a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete user')): ?>
                                                <a class="dropdown-item" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($user['id']); ?>').submit();">
                                                    <?php if($user->delete_status == 1): ?><?php echo e(__('Delete')); ?> <?php else: ?> <?php echo e(__('Restore')); ?><?php endif; ?>
                                                </a>
                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user['id']],'id'=>'delete-form-'.$user['id']]); ?>

                                                <?php echo Form::close(); ?>

                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="#" class="action-item"><i class="fas fa-lock"></i></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="avatar-parent-child">
                        <img src="<?php echo e((!empty($user->avatar))? asset(Storage::url("avatar/".$user->avatar)): asset(Storage::url("avatar/avatar.png"))); ?>" class="avatar rounded-circle avatar-xl">
                    </div>
                    <h4 class="h4 mb-0 mt-2"><?php echo e($user->name); ?></h4>
                    <h5 class="office-time mb-0"><?php echo e($user->email); ?></h5>
                    <?php if($user->delete_status == 0): ?>
                        <h5 class="office-time mb-0"><?php echo e(__('Deleted')); ?></h5>
                    <?php endif; ?>
                    <div class="sal-right-card">
                        <span class="badge badge-pill badge-blue"><?php echo e(ucfirst($user->type)); ?></span>
                    </div>
                    <div class="row text-center">
                        <?php if(\Auth::user()->type=='super admin'): ?>
                            <div class="col-6 text-center">
                                <span class="d-block font-weight-bold mb-0 mt-2 text-sm"><?php echo e(!empty($user->getPlan)?$user->getPlan->name : ''); ?></span>
                            </div>
                            <div class="col-6 text-center Id">
                                <a href="#" class="btn-sm" data-url="<?php echo e(route('plan.upgrade',$user->id)); ?>" data-size="lg" data-ajax-popup="true" data-title="<?php echo e(__('Upgrade Plan')); ?>"><?php echo e(__('Upgrade Plan')); ?></a>
                            </div>
                            <div class="col-12 text-center pt-3">
                                <span class="text-dark text-xs"><?php echo e(__('Plan Expired : ')); ?> <?php echo e(!empty($user->plan_expire_date) ? \Auth::user()->dateFormat($user->plan_expire_date): __('Unlimited')); ?></span>
                            </div>
                            <div class="col-12">
                                <hr class="my-3">
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number"><i class="fas fa-users"></i><?php echo e($user->total_company_user($user->id)); ?></div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number"><i class="fas fa-file-invoice-dollar"></i><?php echo e($user->total_company_project($user->id)); ?></div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number border-none"><i class="fas fa-tasks"></i><?php echo e($user->total_company_client($user->id)); ?></div>
                            </div>
                        <?php else: ?>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number"><i class="fas fa-briefcase"></i><?php echo e($user->user_project()); ?></div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number"><i class="fas fa-file-invoice-dollar"></i><?php echo e(\Auth::user()->priceFormat($user ->user_expense())); ?></div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="profile-number border-none"><i class="fas fa-tasks"></i><?php echo e($user->user_assign_task()); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/dvjxg9j1xkts/public_html/dashboard.drjitendrakodilkar.com/resources/views/user/index.blade.php ENDPATH**/ ?>