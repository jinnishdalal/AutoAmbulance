<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Lead Source')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create lead source')): ?>
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="<?php echo e(route('leadsources.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create Lead Source')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?> </a>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped dataTable">
                            <thead>
                            <tr>
                                <th><?php echo e(__('Source')); ?></th>
                                <th width="250px"><?php echo e(__('Action')); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $leadsources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $leadsource): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <a><?php echo e($leadsource->name); ?></a>
                                    </td>
                                    <td class="Action">
                                            <span>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit lead source')): ?>
                                                    <a href="#" class="edit-icon" data-url="<?php echo e(route('leadsources.edit',$leadsource->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Lead Source')); ?>" class="table-action" ><i class="fas fa-pencil-alt"></i></a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete lead source')): ?>
                                                    <a href="#" class="delete-icon"  data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($leadsource->id); ?>').submit();"><i class="fas fa-trash"></i></a>
                                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['leadsources.destroy', $leadsource->id],'id'=>'delete-form-'.$leadsource->id]); ?>

                                                    <?php echo Form::close(); ?>

                                                <?php endif; ?>
                                            </span>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/leadsources/index.blade.php ENDPATH**/ ?>