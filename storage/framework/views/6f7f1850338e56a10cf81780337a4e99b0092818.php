<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Invoice')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create invoice')): ?>
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="<?php echo e(route('invoices.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create Invoice')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?> </a>
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
                                <th> <?php echo e(__('Invoice')); ?></th>
                                <th> <?php echo e(__('Project')); ?></th>
                                <th> <?php echo e(__('Issue Date')); ?></th>
                                <th> <?php echo e(__('Due Date')); ?></th>
                                <th> <?php echo e(__('Value')); ?></th>
                                <th> <?php echo e(__('Status')); ?></th>
                                <?php if(Gate::check('edit invoice') || Gate::check('delete invoice')): ?>
                                    <th> <?php echo e(__('Action')); ?></th>
                                <?php endif; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="Id">
                                        <a href="<?php echo e(route('invoices.show',$invoice->id)); ?>"><?php echo e(Utility::invoiceNumberFormat($invoice->id)); ?></a>
                                    </td>
                                    <td><?php echo e((isset($invoice->project) && !empty($invoice->project)) ? $invoice->project->name : '-'); ?></td>
                                    <td><?php echo e(Auth::user()->dateFormat($invoice->issue_date)); ?></td>
                                    <td><?php echo e(Auth::user()->dateFormat($invoice->due_date)); ?></td>
                                    <td><?php echo e(Auth::user()->priceFormat($invoice->getTotal())); ?></td>
                                    <td>
                                        <?php if($invoice->status == 0): ?>
                                            <span class="label label-soft-primary"><?php echo e(__(\App\Invoice::$statues[$invoice->status])); ?></span>
                                        <?php elseif($invoice->status == 1): ?>
                                            <span class="label label-soft-danger"><?php echo e(__(\App\Invoice::$statues[$invoice->status])); ?></span>
                                        <?php elseif($invoice->status == 2): ?>
                                            <span class="label label-soft-warning"><?php echo e(__(\App\Invoice::$statues[$invoice->status])); ?></span>
                                        <?php elseif($invoice->status == 3): ?>
                                            <span class="label label-soft-success"><?php echo e(__(\App\Invoice::$statues[$invoice->status])); ?></span>
                                        <?php elseif($invoice->status == 4): ?>
                                            <span class="label label-soft-info"><?php echo e(__(\App\Invoice::$statues[$invoice->status])); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if(Gate::check('edit invoice') || Gate::check('delete invoice')): ?>
                                        <td class="Action">
                                            <span>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('show invoice')): ?>
                                                    <a href="<?php echo e(route('invoices.show',$invoice->id)); ?>" class="edit-icon bg-warning" data-toggle="tooltip" data-original-title="<?php echo e(__('Detail')); ?>">
                                                    <i class="far fa-eye"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit invoice')): ?>
                                                    <a href="#" data-url="<?php echo e(route('invoices.edit',$invoice->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Invoice')); ?>" class="edit-icon" >
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <?php endif; ?>
                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete invoice')): ?>
                                                    <a href="#" class="delete-icon"  data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($invoice->id); ?>').submit();">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                    <?php echo Form::open(['method' => 'DELETE', 'route' => ['invoices.destroy', $invoice->id],'id'=>'delete-form-'.$invoice->id]); ?>

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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/invoices/index.blade.php ENDPATH**/ ?>