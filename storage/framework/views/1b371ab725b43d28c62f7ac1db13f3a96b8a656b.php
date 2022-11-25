<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Plans')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <div class="all-button-box row d-flex justify-content-end">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create plan')): ?>
            <?php if(count($payment_setting)>0): ?>
            <div class="all-button-box row d-flex justify-content-end">
                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                    <a href="#" data-url="<?php echo e(route('plans.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create Plan')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?> </a>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create plan')): ?>
        <div class="row">
            <div class="col-12">
                <?php if(count($payment_setting)==0): ?>
                    <div class="alert alert-warning"><i class="fe fe-info"></i> <?php echo e(__('Please set payment api key & secret key for add new plan')); ?></div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-lg-4 col-xl-3 col-md-6 col-sm-6">
                <div class="plan-3">
                    <h6 class="text-center"><?php echo e($plan->name); ?></h6>
                    <p class="price">
                        <sup><?php echo e((env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : '$')); ?></sup>
                        <?php echo e(number_format($plan->price)); ?>

                        <sub>/ <?php echo e($plan->duration); ?></sub>
                    </p>
                    <ul class="plan-detail">
                        <li><?php echo e(($plan->max_users < 0) ? __('Unlimited'):$plan->max_users); ?> <?php echo e(__('Users')); ?></li>
                        <li><?php echo e(($plan->max_clients < 0) ? __('Unlimited'):$plan->max_clients); ?> <?php echo e(__('Clients')); ?></li>
                        <li><?php echo e(($plan->max_projects < 0) ? __('Unlimited'):$plan->max_projects); ?> <?php echo e(__('Projects')); ?></li>
                    </ul>
                    <?php if($plan->description): ?>
                        <p class="server-plan">
                            <?php echo e($plan->description); ?>

                        </p>
                    <?php endif; ?>
                    <div class="text-center">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit plan')): ?>
                            <a href="#" class="button btn-xs" data-ajax-popup="true" data-size="lg" data-title="<?php echo e(__('Edit Plan')); ?>" data-url="<?php echo e(route('plans.edit',$plan->id)); ?>"><i class="fas fa-pencil-alt"></i></a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('buy plan')): ?>
                            <?php if($plan->id != \Auth::user()->plan): ?>
                                <a href="<?php echo e(route('payment',\Illuminate\Support\Facades\Crypt::encrypt($plan->id))); ?>" class="button btn-xs"><i class="fa fa-cart-plus"></i></a>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if(\Auth::user()->type == 'company' && \Auth::user()->plan == $plan->id): ?>
                            <?php if(empty(\Auth::user()->plan_expire_date)): ?>
                                <p class="server-plan font-weight-bold text-white text-center"><?php echo e(__('Unlimited')); ?></p>
                            <?php else: ?>
                                <p class="server-plan font-weight-bold text-white text-center">
                                    <?php echo e(__('Expire on ')); ?> <?php echo e((date('d M Y',strtotime(\Auth::user()->plan_expire_date)))); ?>

                                </p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/plan/index.blade.php ENDPATH**/ ?>