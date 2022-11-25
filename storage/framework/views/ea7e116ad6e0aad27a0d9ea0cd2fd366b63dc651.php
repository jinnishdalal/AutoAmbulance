<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Client')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <div class="all-button-box row d-flex justify-content-end">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create client')): ?>
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="<?php echo e(route('clients.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Client')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto">
                    <i class="fa fa-plus"></i> <?php echo e(__('Add New')); ?>

                </a>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-3 col-sm-6 col-md-6">
                <div class="card profile-card">
                    <?php if(Gate::check('edit user') || Gate::check('delete user')): ?>
                        <div class="edit-profile user-text">
                            <?php if($client->is_active == 1): ?>
                                <?php if((Gate::check('edit user') || Gate::check('delete user'))): ?>
                                    <div class="dropdown action-item">
                                        <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit user')): ?>
                                                <a href="#" class="dropdown-item" data-url="<?php echo e(route('clients.edit',$client->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Client')); ?>"><?php echo e(__('Edit')); ?></a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete user')): ?>
                                                <a class="dropdown-item" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($client['id']); ?>').submit();">
                                                    <?php if($client->delete_status == 1): ?><?php echo e(__('Delete')); ?> <?php else: ?> <?php echo e(__('Restore')); ?><?php endif; ?>
                                                </a>
                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['clients.destroy', $client['id']],'id'=>'delete-form-'.$client['id']]); ?>

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
                        <img src="<?php echo e((!empty($client->avatar))? asset(Storage::url("avatar/".$client->avatar)): asset(Storage::url("avatar/avatar.png"))); ?>" class="avatar rounded-circle avatar-xl">
                    </div>
                    <h4 class="h4 mb-0 mt-2"><?php echo e($client->name); ?></h4>
                    <h5 class="office-time mb-0"><?php echo e($client->email); ?></h5>
                    <div class="sal-right-card">
                        <span class="badge badge-pill badge-blue"><?php echo e(ucfirst($client->type)); ?></span>
                    </div>
                    <div class="row text-center">
                        <div class="col-md-4 col-sm-4 col-4">
                            <div class="profile-number"><i class="fas fa-briefcase"></i><?php echo e($client->client_project()); ?></div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-4">
                            <div class="profile-number"><i class="fas fa-file-invoice-dollar"></i><?php echo e(\Auth::user()->priceFormat($client ->client_project_budget())); ?></div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-4">
                            <div class="profile-number border-none"><i class="fas fa-tasks"></i><?php echo e($client->client_lead()); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/client/index.blade.php ENDPATH**/ ?>