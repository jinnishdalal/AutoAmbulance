<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Records')); ?>

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
                                <th><?php echo e(__('Order Id')); ?></th>
                                <th><?php echo e(__('Name')); ?></th>
                                <th><?php echo e(__('Plan Name')); ?></th>
                                <th><?php echo e(__('Price')); ?></th>
                                <th><?php echo e(__('Status')); ?></th>
                                <th><?php echo e(__('Payment Type')); ?></th>
                                <th><?php echo e(__('Date')); ?></th>
                                <th><?php echo e(__('Coupon')); ?></th>
                                <th><?php echo e(__('Invoice')); ?></th>
                            </tr>
                            </thead>

                            <tbody>
                            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($order->order_id); ?></td>
                                    <td><?php echo e($order->user_name); ?></td>
                                    <td><?php echo e($order->plan_name); ?></td>
                                    <td>$<?php echo e(number_format($order->price)); ?></td>
                                    <td>
                                        <?php if($order->payment_status == 'succeeded'): ?>
                                            <i class="mdi mdi-circle text-success"></i> <?php echo e(ucfirst($order->payment_status)); ?>

                                        <?php else: ?>
                                            <i class="mdi mdi-circle text-danger"></i> <?php echo e(ucfirst($order->payment_status)); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($order->payment_type); ?></td>
                                    <td><?php echo e($order->created_at->format('d M Y')); ?></td>
                                    <td><?php echo e(!empty($order->use_coupon)?$order->use_coupon->coupon_detail->name:''); ?></td>
                                    <td class="Id">
                                        <?php if(empty($order->receipt)): ?>
                                            <p><?php echo e(__('Manually plan upgraded by super admin')); ?></p>
                                        <?php elseif($order->receipt =='free coupon'): ?>
                                            <p><?php echo e(__('Used 100 % discount coupon code.')); ?></p>
                                        <?php else: ?>
                                            <a href="<?php echo e($order->receipt); ?>" title="Invoice" target="_blank" class=""><i class="fas fa-file-invoice"></i> <?php echo e(__('Invoice')); ?></a>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/dvjxg9j1xkts/public_html/dashboard.drjitendrakodilkar.com/resources/views/order/index.blade.php ENDPATH**/ ?>