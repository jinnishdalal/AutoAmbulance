<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Project')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('action-button'); ?>
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create project')): ?>
        <div class="all-button-box row d-flex justify-content-end">
            <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
                <a href="#" data-url="<?php echo e(route('projects.create')); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Create New Project')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-plus"></i> <?php echo e(__('Create')); ?> </a>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $permissions=$project->client_project_permission();
                $perArr=(!empty($permissions)? explode(',',$permissions->permissions):[]);

                $project_last_stage = ($project->project_last_stage($project->id)? $project->project_last_stage($project->id)->id:'');

                $total_task = $project->project_total_task($project->id);
                $completed_task=$project->project_complete_task($project->id,$project_last_stage);
                $percentage=0;
                if($total_task!=0){
                    $percentage = intval(($completed_task / $total_task) * 100);
                }

                $label='';
                if($percentage<=15){
                    $label='bg-danger';
                }else if ($percentage > 15 && $percentage <= 33) {
                    $label='bg-warning';
                } else if ($percentage > 33 && $percentage <= 70) {
                    $label='bg-primary';
                } else {
                    $label='bg-success';
                }

            ?>

            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-6 project-card">
                <div class="card">
                    <div class="edit-profile user-text">
                        <?php if($project->is_active == 1): ?>
                            <?php if((Gate::check('edit project') || Gate::check('delete project'))): ?>
                                <div class="dropdown action-item">
                                    <a href="#" class="action-item" role="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-h"></i></a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit project')): ?>
                                            <a href="#" class="dropdown-item" data-url="<?php echo e(route('projects.edit',$project->id)); ?>" data-ajax-popup="true" data-title="<?php echo e(__('Edit Project')); ?>"><?php echo e(__('Edit')); ?></a>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete project')): ?>
                                            <a href="#" class="dropdown-item" data-confirm="<?php echo e(__('Are You Sure?').'|'.__('This action can not be undone. Do you want to continue?')); ?>" data-confirm-yes="document.getElementById('delete-form-<?php echo e($project->id); ?>').submit();"><?php echo e(__('Delete')); ?></a>
                                            <?php echo Form::open(['method' => 'DELETE', 'route' => ['projects.destroy', $project->id],'id'=>'delete-form-'.$project->id]); ?>

                                            <?php echo Form::close(); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="#" class="action-item"><i class="fas fa-lock"></i></a>
                        <?php endif; ?>
                    </div>
                    <div class="project-title">
                        <?php if($project->is_active==1): ?>
                            <h3 class="h3 font-weight-400 mb-0"><a href="<?php echo e(route('projects.show',$project->id)); ?>"><?php echo e($project->name); ?></a></h3>
                        <?php else: ?>
                            <h3 class="h3 font-weight-400 mb-0"><?php echo e($project->name); ?></h3>
                        <?php endif; ?>
                        <?php $__currentLoopData = $project_status; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($key== $project->status): ?>
                                <?php if($status=='Completed'): ?>
                                    <?php $status_color ='badge-success' ?>
                                <?php elseif($status=='On Going'): ?>
                                    <?php $status_color ='badge-primary' ?>
                                <?php else: ?>
                                    <?php $status_color ='badge-warning' ?>
                                <?php endif; ?>
                                <span class="badge badge-pill <?php echo e($status_color); ?>"><?php echo e(__($status)); ?></span>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <div class="project-detail">
                        <div class="row">
                            <div class="col-lg-4 col-xl-4 mb-2 col-md-4 col-sm-4 col-4">
                                <div class="start-date">
                                    <label class="m-0"><?php echo e(__('Start Date')); ?></label>
                                    <div class="date-box"><?php echo e(\Auth::user()->dateFormat($project->start_date)); ?></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 mb-2 col-md-4 col-sm-4 col-4">
                                <div class="start-date">
                                    <label class="m-0"><?php echo e(__('Due Date')); ?></label>
                                    <div class="date-box light-red"><?php echo e(\Auth::user()->dateFormat($project->due_date)); ?></div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 mb-2 col-md-4 col-sm-4 col-4">
                                <div class="start-date full-width">
                                    <label class="m-0"><?php echo e(__('Progress')); ?></label>
                                    <div class="progress">
                                        <div class="progress-bar yellow-bg" style="width:<?php echo e($percentage); ?>%"><?php echo e($percentage); ?>%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-xl-4 mb-2 col-md-4 col-sm-4 col-4">
                                <div class="start-date">
                                    <label class="m-0"><?php echo e(__('Budget')); ?><span><?php echo e(\Auth::user()->priceFormat($project->price)); ?></span></label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 mb-2 col-md-4 col-sm-4 col-4">
                                <div class="start-date">
                                    <label class="m-0"><?php echo e(__('Expense')); ?><span><?php echo e(\Auth::user()->priceFormat($project->project_expenses())); ?></span></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-xl-4 col-md-4 col-sm-4 col-4">
                                <?php
                                    $client=(!empty($project->client())?$project->client()->avatar:'')
                                ?>
                                <div class="start-date">
                                    <label class="m-0"><?php echo e(__('Client')); ?></label>
                                    <ul class="project-img">
                                        <li><img src="<?php echo e((!empty($project->client()->avatar)? asset(Storage::url('avatar/'.$client)) : asset(Storage::url('avatar/avatar.png')))); ?>" data-toggle="tooltip" data-original-title="<?php echo e($project->client()->name); ?>"></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-4 col-md-4 col-sm-4 col-4">
                                <div class="start-date">
                                    <label class="m-0"><?php echo e(__('Members')); ?></label>
                                    <ul class="project-img">
                                        <?php $__currentLoopData = $project->project_user(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project_user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><img src="<?php echo e((!empty($project_user->avatar)? asset(Storage::url('avatar/'.$project_user->avatar)) : asset(Storage::url('avatar/avatar.png')))); ?>" data-toggle="tooltip" data-original-title="<?php echo e($project_user->name); ?>"></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="project-detail">
                        <div class="row">
                            <div class="col-lg-3 col-xl-3 col-md-4 col-sm-3 col-3">
                                <div class="project-number">
                                    <?php if($project->is_active==1): ?>
                                        <a href="<?php echo e(route('project.taskboard',$project->id)); ?>">
                                            <i class="fas fa-tasks"></i> <?php echo e($project->countTask()); ?> <?php echo e(__('Tasks')); ?>

                                        </a>
                                    <?php else: ?>
                                        <i class="fas fa-tasks"></i> <?php echo e($project->countTask()); ?> <?php echo e(__('Tasks')); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-5 col-md-4 col-sm-5 col-5">
                                <div class="project-number">
                                    <?php if($project->is_active==1): ?>
                                        <a href="<?php echo e(route('project.taskboard',$project->id)); ?>">
                                            <i class="fas fa-comments"></i> <?php echo e($project->countTaskComments()); ?> <?php echo e(__('Comments')); ?>

                                        </a>
                                    <?php else: ?>
                                        <i class="fas fa-comments"></i> <?php echo e($project->countTaskComments()); ?> <?php echo e(__('Comments')); ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if($project->is_active==1): ?>
                                <div class="col-lg-5 col-xl-4 col-md-4 col-sm-4 col-4">
                                    <a href="<?php echo e(route('projects.show',$project->id)); ?>" class="btn btn-sm btn-white btn-icon-only width-auto">
                                        <?php echo e(__('Detail')); ?>

                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/projects/index.blade.php ENDPATH**/ ?>