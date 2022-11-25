<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Coupon Detail')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <div class="all-button-box row d-flex justify-content-end">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="<?php echo e(route('coupons.index')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-arrow-left"></i> <?php echo e(__('Back')); ?> </a>
        </div>
    </div>
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
                                <th> <?php echo e(__('User')); ?></th>
                                <th> <?php echo e(__('Date')); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $__currentLoopData = $userCoupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userCoupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e(!empty($userCoupon->userDetail)?$userCoupon->userDetail->name:''); ?></td>
                                    <td><?php echo e($userCoupon->created_at); ?></td>
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

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/coupon/view.blade.php ENDPATH**/ ?>