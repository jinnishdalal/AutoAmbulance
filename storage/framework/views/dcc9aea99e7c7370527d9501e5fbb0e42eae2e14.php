<div class="card bg-none card-box">
    <?php echo e(Form::model($project, array('route' => array('projects.update', $project->id), 'method' => 'PUT'))); ?>

    <div class="row">
        <div class="form-group col-md-6">
            <?php echo e(Form::label('name', __('Projects Name'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('name', null, array('class' => 'form-control ','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('price', __('Projects Price'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('price', null, array('class' => 'form-control','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('date', __('Due Date'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('date', $project->due_date, array('class' => 'form-control datepicker','required'=>'required'))); ?>

        </div>

        <div class="form-group col-md-6">
            <?php echo e(Form::label('client', __('Client'),['class'=>'form-control-label'])); ?>

            <?php echo Form::select('client', $clients, null,array('class' => 'form-control select2','required'=>'required')); ?>

        </div>

        <div class="form-group col-md-12">
            <?php echo e(Form::label('lead', __('Lead'),['class'=>'form-control-label'])); ?>

            <?php echo Form::select('lead', $leads, null,array('class' => 'form-control select2','required'=>'required')); ?>

        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('label', __('Label'),['class'=>'form-control-label'])); ?>

            <div class="bg-color-label">
                <?php $__currentLoopData = $labels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="custom-control custom-radio <?php echo e($label->color); ?> mb-3">
                        <input class="custom-control-input" name="label" id="customCheck_<?php echo e($k); ?>" type="radio" value="<?php echo e($label->id); ?>" <?php echo e(($label->id==$project->label)?'checked':''); ?>>
                        <label class="custom-control-label" for="customCheck_<?php echo e($k); ?>"></label>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('description', __('Description'),['class'=>'form-control-label'])); ?>

            <?php echo Form::textarea('description', null, ['class'=>'form-control ','rows'=>'2']); ?>

        </div>
        <div class="col-12 text-right">
            <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/projects/edit.blade.php ENDPATH**/ ?>