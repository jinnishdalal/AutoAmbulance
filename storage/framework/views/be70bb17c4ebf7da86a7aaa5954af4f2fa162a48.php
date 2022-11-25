<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage TimeSheet')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage timesheet')): ?>
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="<?php echo e(route('task.timesheet')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create Time Sheet')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?> </a>
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
                                <th> <?php echo e(__('Task')); ?></th>
                                <?php if(\Auth::user()->type == 'company'): ?>
                                    <th> <?php echo e(__('User')); ?></th>
                                <?php endif; ?>
                                <th> <?php echo e(__('Project')); ?></th>
                                <th> <?php echo e(__('Date')); ?></th>
                                <th> <?php echo e(__('Hours')); ?></th>
                                <th> <?php echo e(__('Remark')); ?></th>
                                <?php if(\Auth::user()->type!='client'): ?>
                                    <th> <?php echo e(__('Action')); ?></th>
                                <?php else: ?>
                                    <th><?php echo e(__('User')); ?></th>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $timeSheets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timeSheet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class=""><?php echo e(!empty($timeSheet->task())? $timeSheet->task()->title : ''); ?></td>
                                    <?php if(\Auth::user()->type == 'company'): ?>
                                        <td class=""><?php echo e(!empty($timeSheet->user())? $timeSheet->user()->name : ''); ?></td>
                                    <?php endif; ?>
                                    <td class=""><?php echo e(!empty($timeSheet->project)? $timeSheet->project->name : ''); ?></td>
                                    <td><?php echo e(Auth::user()->dateFormat($timeSheet->date)); ?></td>
                                    <td><?php echo e($timeSheet->hours); ?></td>
                                    <td class=""><?php echo e($timeSheet->remark); ?></td>
                                    <?php if(\Auth::user()->type!='client'): ?>
                                        <td class="Action">
                                            <a href="#" class="edit-icon" data-url="<?php echo e(route('task.timesheet.edit',[$timeSheet->id])); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Time Sheet')); ?>" >
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <a href="#" class="delete-icon"  data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($timeSheet->id); ?>').submit();">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php echo Form::open(['method' => 'DELETE', 'route' => ['task.timesheet.destroy', $timeSheet->id],'id'=>'delete-form-'.$timeSheet->id]); ?>

                                            <?php echo Form::close(); ?>

                                        </td>
                                    <?php else: ?>
                                        <td><?php echo e(!empty($timeSheet->user())?$timeSheet->user()->name:''); ?></td>
                                    <?php endif; ?>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/projects/timeSheet.blade.php ENDPATH**/ ?>