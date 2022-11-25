<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Payment')); ?>

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
                                <th> <?php echo e(__('Transaction ID')); ?></th>
                                <th> <?php echo e(__('Invoice')); ?></th>
                                <th> <?php echo e(__('Payment Date')); ?></th>
                                <th> <?php echo e(__('Payment Method')); ?></th>
                                <th> <?php echo e(__('Payment Type')); ?></th>
                                <th> <?php echo e(__('Note')); ?></th>
                                <th> <?php echo e(__('Amount')); ?></th>
                                <?php if(Gate::check('show invoice') || \Auth::user()->type=='client'): ?>
                                    <th><?php echo e(__('Action')); ?></th>
                                <?php endif; ?>
                            </tr>
                            </thead>

                            <tbody>
                            <?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e(sprintf("%05d", $payment->transaction_id)); ?></td>
                                    <td><?php echo e(Utility::invoiceNumberFormat($payment->invoice->invoice_id)); ?></td>
                                    <td><?php echo e(Auth::user()->dateFormat($payment->date)); ?></td>
                                    <td><?php echo e((!empty($payment->payment)?$payment->payment->name:'-')); ?></td>
                                    <td><?php echo e($payment->payment_type); ?></td>
                                    <td><?php echo e($payment->notes); ?></td>
                                    <td><?php echo e(Auth::user()->priceFormat($payment->amount)); ?></td>
                                    <?php if(Gate::check('show invoice') || \Auth::user()->type=='client'): ?>
                                        <td class="Action">
                                            <span>
                                                <a href="<?php echo e(route('invoices.show',$payment->invoice->id)); ?>" class="edit-icon bg-warning" data-toggle="tooltip" data-original-title="<?php echo e(__('Invoice Detail')); ?>">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/invoices/all-payments.blade.php ENDPATH**/ ?>