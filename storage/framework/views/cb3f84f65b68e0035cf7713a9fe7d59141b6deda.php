<?php $__env->startPush('script-page'); ?>
    <script src="<?php echo e(asset('assets/libs/dragula/dist/dragula.min.js')); ?>"></script>
    <script>
        !function (a) {
            "use strict";
            var t = function () {
                this.$body = a("body")
            };
            t.prototype.init = function () {
                a('[data-plugin="dragula"]').each(function () {
                    var t = a(this).data("containers"), n = [];
                    if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
                    var r = a(this).data("handleclass");
                    r ? dragula(n, {
                        moves: function (a, t, n) {
                            return n.classList.contains(r)
                        }
                    }) : dragula(n).on('drop', function (el, target, source, sibling) {

                        var order = [];
                        $("#" + target.id + " > div").each(function () {
                            order[$(this).index()] = $(this).attr('data-id');
                        });

                        var id = $(el).attr('data-id');
                        var stage_id = $(target).attr('data-id');

                        $("#" + source.id).parent().find('.count').text($("#" + source.id + " > div").length);
                        $("#" + target.id).parent().find('.count').text($("#" + target.id + " > div").length);

                        $.ajax({
                            url: '<?php echo e(route('leads.order')); ?>',
                            type: 'POST',
                            data: {lead_id: id, stage_id: stage_id, order: order, "_token": $('meta[name="csrf-token"]').attr('content')},
                            success: function (data) {
                            },
                            error: function (data) {
                                data = data.responseJSON;
                                show_toastr('<?php echo e(__("Error")); ?>', data.error, 'error')
                            }
                        });
                    });
                })
            }, a.Dragula = new t, a.Dragula.Constructor = t
        }(window.jQuery), function (a) {
            "use strict";

            a.Dragula.init()

        }(window.jQuery);
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Lead')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create lead')): ?>
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="<?php echo e(route('leads.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Lead')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?> </a>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <?php
                $json = [];
                foreach ($stages as $stage){
                    $json[] = 'lead-list-'.$stage->id;
                }
            ?>
            <div class="board" data-plugin="dragula" data-containers='<?php echo json_encode($json); ?>'>
                <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(\Auth::user()->type == 'company'): ?>
                        <?php ($leads = $stage->leads); ?>
                    <?php else: ?>
                        <?php ($leads = $stage->user_leads()); ?>
                    <?php endif; ?>
                    <div class="tasks">
                        <h5 class="mt-0 mb-0 task-header"><?php echo e($stage->name); ?> (<span class="count"><?php echo e(count($leads)); ?></span>)</h5>
                        <div id="lead-list-<?php echo e($stage->id); ?>" data-id="<?php echo e($stage->id); ?>" class="task-list-items for-leads mb-2">
                            <?php $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="card mb-2 mt-0 pb-1" data-id="<?php echo e($lead->id); ?>">
                                    <div class="card-body p-0">
                                        <?php if(Gate::check('edit lead') || Gate::check('delete lead')): ?>
                                            <div class="float-right">
                                                <?php if(!$lead->is_active): ?>
                                                    <div class="dropdown global-icon lead-dropdown pr-1">
                                                        <a href="#" class="action-item" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit lead')): ?>
                                                                <a class="dropdown-item" data-url="<?php echo e(URL::to('leads/'.$lead->id.'/edit')); ?>" data-size="lg" data-ajax-popup="true" data-title="<?php echo e(__('Edit Lead')); ?>" href="#"><?php echo e(__('Edit')); ?></a>
                                                            <?php endif; ?>
                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete lead')): ?>
                                                                <a class="dropdown-item" href="#" data-title="<?php echo e(__('Delete Lead')); ?>" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($lead->id); ?>').submit();"><?php echo e(__('Delete')); ?></a>
                                                                <?php echo Form::open(['method' => 'DELETE', 'route' => ['leads.destroy', $lead->id],'id'=>'delete-form-'.$lead->id]); ?>

                                                                <?php echo Form::close(); ?>

                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="pl-2 pt-0 pr-2 pb-2">
                                            <h5 class="my-2">
                                                <a href="#" class="text-body"><?php echo e($lead->name); ?></a>
                                            </h5>
                                            <p class="mb-0">
                                                <span class="text-nowrap mb-2 d-inline-block text-xs"><?php echo e($lead->notes); ?></span>
                                            </p>
                                            <div class="row">
                                                <div class="col-6 text-xs">
                                                    <i class="far fa-clock"></i>
                                                    <span><?php echo e(\Auth::user()->dateFormat($lead->created_at)); ?></span>
                                                </div>
                                                <div class="col-6 text-right text-xs font-weight-bold">
                                                    <span><?php echo e(\Auth::user()->priceFormat($lead->price)); ?></span>
                                                </div>
                                                <div class="col-12 pt-2">
                                                    <p class="mb-0">
                                                        <?php if(\Auth::user()->type=='company'): ?>
                                                            <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                                <img alt="image" data-toggle="tooltip" data-original-title="<?php echo e((!empty($lead->user())?$lead->user()->name:'')); ?>" src="<?php echo e((!empty($lead->user()->avatar))? asset(Storage::url('avatar/'.$lead->user()->avatar)) : asset(Storage::url("avatar/avatar.png"))); ?>" class="rounded-circle " width="25" height="25">
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="#" class="btn btn-sm mr-1 p-0 rounded-circle">
                                                                <img alt="image" data-toggle="tooltip" data-original-title="<?php echo e((!empty($lead->client())?$lead->client()->name:'')); ?>" src="<?php echo e((!empty($lead->user()->avatar))? asset(Storage::url('avatar/'.$lead->user()->avatar)) : asset(Storage::url("avatar/avatar.png"))); ?>" class="rounded-circle " width="25" height="25">
                                                            </a>
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/leads/index.blade.php ENDPATH**/ ?>