<?php
    $dir= asset(Storage::url('plan'));
?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Note')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create lead source')): ?>
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="<?php echo e(route('notes.create')); ?>" data-size="lg" data-ajax-popup="true" data-title="<?php echo e(__('Create Note')); ?>"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?> </a>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="staff-wrap">
                <div class="row">
                    <?php if($notes->count() > 0): ?>
                        <?php $__currentLoopData = $notes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card profile-card pt-0">
                                    <div class="row">
                                        <div class="col-10">
                                            <div class="custom-control custom-radio mb-3 <?php echo e($note->color); ?> font-weight-bold">
                                                <label class="custom-control-label "></label>
                                                <?php echo e($note->title); ?>

                                            </div>
                                        </div>
                                        <div class="col-2 text-right">
                                            <div class="dropdown action-item pt-0">
                                                <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(-166px, 35px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit note')): ?>
                                                        <a href="#" class="dropdown-item" data-url="<?php echo e(route('notes.edit',$note->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Note')); ?>"><?php echo e(__('Edit')); ?></a>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete note')): ?>
                                                        <a class="dropdown-item" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($note->id); ?>').submit();"><?php echo e(__('Delete')); ?></a>
                                                        <?php echo Form::open(['method' => 'DELETE', 'route' => ['notes.destroy', $note->id],'id'=>'delete-form-'.$note->id]); ?>

                                                        <?php echo Form::close(); ?>

                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 text-justify text-sm">
                                            <?php echo e($note->text); ?>

                                            <br><br>
                                            <b><?php echo e(\Auth::user()->dateFormat($note->created_at)); ?></b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="card text-center py-3 font-weight-bold">
                                <p><?php echo e(__("No Notes Found.!")); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/notes/index.blade.php ENDPATH**/ ?>