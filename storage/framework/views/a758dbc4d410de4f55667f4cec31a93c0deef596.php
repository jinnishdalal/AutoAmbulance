<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Expense')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create lead source')): ?>
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="<?php echo e(route('expenses.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create Expense')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?> </a>
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
                                <th><?php echo e(__('Category')); ?></th>
                                <th width="40%"> <?php echo e(__('Description')); ?></th>
                                <th><?php echo e(__('Amount')); ?></th>
                                <th><?php echo e(__('Date')); ?></th>
                                <th><?php echo e(__('Project')); ?></th>
                                <th><?php echo e(__('User')); ?></th>
                                <th><?php echo e(__('Attachment')); ?></th>
                                <?php if(Gate::check('edit expense') || Gate::check('delete expense')): ?>
                                    <th> <?php echo e(__('Action')); ?></th>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e((!empty($expense->category)?$expense->category->name:'')); ?></td>
                                    <td><?php echo e((!empty($expense->description) ? $expense->description : '-')); ?></td>
                                    <td><?php echo e(Auth::user()->priceFormat($expense->amount)); ?> </td>
                                    <td><?php echo e(Auth::user()->dateFormat($expense->date)); ?></td>
                                    <td><?php echo e((!empty($expense->projects)?$expense->projects->name:'')); ?></td>
                                    <td><?php echo e((!empty($expense->user)?$expense->user->name:'')); ?></td>
                                    <td class="Action">
                                        <?php if($expense->attachment): ?>
                                            <span>
                                                <a href="<?php echo e(asset(Storage::url('attachment/'. $expense->attachment))); ?>" download="" class="edit-icon bg-info" data-toggle="tooltip" data-original-title="<?php echo e(__('Download')); ?>">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                        </span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <?php if(Gate::check('edit expense') || Gate::check('delete expense')): ?>
                                        <td class="Action">
                                            <span>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit expense')): ?>
                                                    <a href="#" class="edit-icon" data-url="<?php echo e(route('expenses.edit',$expense->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Expense')); ?>" >
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete expense')): ?>
                                                    <a href="#" class="delete-icon"  data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($expense->id); ?>').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['expenses.destroy', $expense->id],'id'=>'delete-form-'.$expense->id]); ?>

                                                    <?php echo Form::close(); ?>

                                                <?php endif; ?>
                                            </span>
                                        </td>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/expenses/index.blade.php ENDPATH**/ ?>