<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Bug Status')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('script-page'); ?>
    <script src="<?php echo e(asset('assets/libs/jquery-ui/jquery-ui.js')); ?>"></script>
    <script>
        $(function () {
            $(".sortable").sortable();
            $(".sortable").disableSelection();
            $(".sortable").sortable({
                stop: function () {
                    var order = [];
                    $(this).find('li').each(function (index, data) {
                        order[index] = $(data).attr('data-id');
                    });
                    $.ajax({
                        url: "<?php echo e(route('bugstatus.order')); ?>",
                        data: {order: order, _token: $('meta[name="csrf-token"]').attr('content')},
                        type: 'POST',
                        success: function (data) {
                        },
                        error: function (data) {
                            data = data.responseJSON;
                            show_toastr('<?php echo e(__("Error")); ?>', data.error, 'error')
                        }
                    })
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create bug status')): ?>
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" class="btn btn-xs btn-white btn-icon-only width-auto" data-url="<?php echo e(route('bugstatus.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create Bug Status')); ?>"><i class="fas fa-plus"></i><?php echo e(__('Create')); ?></a>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <ul class="list-group sortable">
                        <?php $__currentLoopData = $bugStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="list-group-item" data-id="<?php echo e($bug->id); ?>">
                                <div class="row">
                                    <div class="col-6 text-xs text-dark"><?php echo e($bug->title); ?></div>
                                    <div class="col-4 text-xs text-dark"><?php echo e($bug->created_at); ?></div>
                                    <div class="col-2">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit bug status')): ?>
                                            <a href="#" data-url="<?php echo e(URL::to('bugstatus/'.$bug->id.'/edit')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Bug Status')); ?>" class="edit-icon">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete bug status')): ?>
                                            <a href="#" class="delete-icon" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($bug->id); ?>').submit();"><i class="fas fa-trash"></i></a>
                                            <?php echo Form::open(['method' => 'DELETE', 'route' => ['bugstatus.destroy', $bug->id],'id'=>'delete-form-'.$bug->id]); ?>

                                            <?php echo Form::close(); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/bugstatus/index.blade.php ENDPATH**/ ?>