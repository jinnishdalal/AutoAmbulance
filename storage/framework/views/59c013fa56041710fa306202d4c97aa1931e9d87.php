<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Dashboard')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>
    <script>
        var SalesChart = (function () {
            var $chart = $('#chart-sales');

            function init($this) {
                var salesChart = new Chart($this, {
                    type: 'line',
                    options: {
                        scales: {
                            yAxes: [{
                                gridLines: {
                                    color: Charts.colors.gray[200],
                                    zeroLineColor: Charts.colors.gray[200]
                                },
                                ticks: {}
                            }]
                        }
                    },
                    data: {
                        labels:<?php echo json_encode($chartData['label']); ?>,
                        datasets: [{
                            label: '<?php echo e(__("Order")); ?>',
                            data:<?php echo json_encode($chartData['data']); ?>

                        }]
                    }
                });
                $this.data('chart', salesChart);
            };
            if ($chart.length) {
                init($chart);
            }
        })();
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-box height-95">
                <div class="icon-box blue-bg"><?php echo e($user['total_user']); ?></div>
                <div class="number-icon">
                    <div class="card-right-title pt-2">
                        <h4 class="float-left"><?php echo e(__('Total Users')); ?></h4>
                        <h5 class="float-right"><?php echo e(__('Paid Users')); ?> : <span class="text-dark"><?php echo e(number_format($user['total_paid_user'])); ?></span></h5>
                    </div>
                </div>
                <img src="<?php echo e(asset('assets/img/dot-icon.png')); ?>" alt="" class="dotted-icon">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-box height-95">
                <div class="icon-box green-bg"><?php echo e($user['total_orders']); ?></div>
                <div class="number-icon">
                    <div class="card-right-title pt-2">
                        <h4 class="float-left"><?php echo e(__('Total Orders')); ?></h4>
                        <h5 class="float-right"><?php echo e(__('Total Order Amount')); ?> : <span class="text-dark"><?php echo e(number_format($user['total_orders_price'])); ?></span></h5>
                    </div>
                </div>
                <img src="<?php echo e(asset('assets/img/dot-icon.png')); ?>" alt="" class="dotted-icon">
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <div class="card card-box height-95">
                <div class="icon-box red-bg"><?php echo e($user['total_plan']); ?></div>
                <div class="number-icon">
                    <div class="card-right-title pt-2">
                        <h4 class="float-left"><?php echo e(__('Total Plans')); ?></h4>
                        <h5 class="float-right"><?php echo e(__('Total Purchase Plan')); ?> : <span class="text-dark"><?php echo e(number_format($user['most_purchese_plan'])); ?></span></h5>
                    </div>
                </div>
                <img src="<?php echo e(asset('assets/img/dot-icon.png')); ?>" alt="" class="dotted-icon">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <h4 class="h4 font-weight-400 float-left"><?php echo e(__('Recent Order')); ?></h4>
                <h6 class="last-day-text"><?php echo e(__('Last 7 Days')); ?></h6>
            </div>
            <div class="card bg-none">
                <canvas id="chart-sales" class="chart-canvas" height="300"></canvas>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/dashboard/super_admin.blade.php ENDPATH**/ ?>