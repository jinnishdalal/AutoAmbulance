<?php $__env->startSection('page-title'); ?>
    <?php echo e($emailTemplate->name); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css-page'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/libs/summernote/summernote-bs4.css')); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script-page'); ?>
    <script src="<?php echo e(asset('assets/libs/summernote/summernote-bs4.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('action-button'); ?>
    <div class="all-button-box row d-flex justify-content-end">
        <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 col-6">
            <a href="<?php echo e(route('email_template.index')); ?>" class="btn btn-xs btn-white btn-icon-only width-auto"><i class="fas fa-arrow-left"></i> <?php echo e(__('Back')); ?> </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <?php echo e(Form::model($emailTemplate, array('route' => array('email_template.update', $emailTemplate->id), 'method' => 'PUT'))); ?>

                    <div class="row">
                        <div class="form-group col-md-12">
                            <?php echo e(Form::label('name',__('Name'),['class'=>'form-control-label text-dark'])); ?>

                            <?php echo e(Form::text('name',null,array('class'=>'form-control ','disabled'=>'disabled'))); ?>

                        </div>
                        <div class="form-group col-md-12">
                            <?php echo e(Form::label('from',__('From'),['class'=>'form-control-label text-dark'])); ?>

                            <?php echo e(Form::text('from',null,array('class'=>'form-control ','required'=>'required'))); ?>

                        </div>
                        <?php echo e(Form::hidden('lang',$currEmailTempLang->lang,array('class'=>''))); ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit email template')): ?>
                            <div class="col-12 text-right">
                                <input type="submit" value="<?php echo e(__('Save')); ?>" class="btn-create badge-blue">
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php echo e(Form::close()); ?>

                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <div class="row text-xs">
                        <div class="col-6">
                            <h6 class="font-weight-bold"><?php echo e(__('Project')); ?></h6>
                            <p class="mb-1"><?php echo e(__('Project Name')); ?> : <span class="pull-right text-primary">{project_name}</span></p>
                            <p class="mb-1"><?php echo e(__('Project Label')); ?> : <span class="pull-right text-primary">{project_label}</span></p>
                            <p class="mb-1"><?php echo e(__('Project Status')); ?> : <span class="pull-right text-primary">{project_status}</span></p>
                        </div>
                        <div class="col-6">
                            <h6 class="font-weight-bold"><?php echo e(__('Task')); ?></h6>
                            <p class="mb-1"><?php echo e(__('Task Name')); ?> : <span class="pull-right text-primary">{task_name}</span></p>
                            <p class="mb-1"><?php echo e(__('Task Priority')); ?> : <span class="pull-right text-primary">{task_priority}</span></p>
                            <p class="mb-1"><?php echo e(__('Task Status')); ?> : <span class="pull-right text-primary">{task_status}</span></p>
                            <p class="mb-1"><?php echo e(__('Task Old Stage')); ?> : <span class="pull-right text-primary">{task_old_stage}</span></p>
                            <p class="mb-1"><?php echo e(__('Task New Stage')); ?> : <span class="pull-right text-primary">{task_new_stage}</span></p>
                        </div>
                        <?php if($emailTemplate->name == 'Assign Estimation'): ?>
                            <div class="col-6">
                                <h6 class="font-weight-bold"><?php echo e(__('Estimation')); ?></h6>
                                <p class="mb-1"><?php echo e(__('Estimation Id')); ?> : <span class="pull-right text-primary">{estimation_name}</span></p>
                                <p class="mb-1"><?php echo e(__('Estimation Client')); ?> : <span class="pull-right text-primary">{estimation_client}</span></p>
                                <p class="mb-1"><?php echo e(__('Estimation Status')); ?> : <span class="pull-right text-primary">{estimation_status}</span></p>
                            </div>
                        <?php endif; ?>
                        <div class="col-6">
                            <h6 class="font-weight-bold"><?php echo e(__('Other')); ?></h6>
                            <p class="mb-1"><?php echo e(__('App Name')); ?> : <span class="pull-right text-primary">{app_name}</span></p>
                            <p class="mb-1"><?php echo e(__('Company Name')); ?> : <span class="pull-right text-primary">{company_name}</span></p>
                            <p class="mb-1"><?php echo e(__('App Url')); ?> : <span class="pull-right text-primary">{app_url}</span></p>
                            <p class="mb-1"><?php echo e(__('Email')); ?> : <span class="pull-right text-primary">{email}</span></p>
                            <p class="mb-1"><?php echo e(__('Password')); ?> : <span class="pull-right text-primary">{password}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="language-wrap">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-12 language-list-wrap">
                                <div class="language-list">
                                    <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                                        <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="text-sm font-weight-bold">
                                                <a href="<?php echo e(route('manage.email.language',[$emailTemplate->id,$lang])); ?>" class="nav-link <?php echo e(($currEmailTempLang->lang == $lang)?'active':''); ?>"><?php echo e(Str::upper($lang)); ?></a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-9 col-md-9 col-sm-12 language-form-wrap">
                                <?php echo e(Form::model($currEmailTempLang, array('route' => array('store.email.language',$currEmailTempLang->parent_id), 'method' => 'POST'))); ?>

                                <div class="row">
                                    <div class="form-group col-12">
                                        <?php echo e(Form::label('subject',__('Subject'),['class'=>'form-control-label text-dark'])); ?>

                                        <?php echo e(Form::text('subject',null,array('class'=>'form-control ','required'=>'required'))); ?>

                                    </div>
                                    <div class="form-group col-12">
                                        <?php echo e(Form::label('content',__('Email Message'),['class'=>'form-control-label text-dark'])); ?>

                                        <?php echo e(Form::textarea('content',$currEmailTempLang->content,array('class'=>'summernote-simple','required'=>'required'))); ?>

                                    </div>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit email template lang')): ?>
                                        <div class="col-md-12 text-right">
                                            <?php echo e(Form::hidden('lang',null)); ?>

                                            <input type="submit" value="<?php echo e(__('Save')); ?>" class="btn-create badge-blue">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php echo e(Form::close()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/email_templates/show.blade.php ENDPATH**/ ?>