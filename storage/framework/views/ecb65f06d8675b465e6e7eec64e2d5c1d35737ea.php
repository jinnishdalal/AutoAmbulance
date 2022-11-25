<div class="card bg-none card-box">
    <?php echo e(Form::model($note, array('route' => array('notes.update', $note->id), 'method' => 'PUT'))); ?>

    <div class="row">
        <div class="form-group col-md-12">
            <?php echo e(Form::label('title',__('Title'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('title',null,array('class'=>'form-control ','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('text', __('Description'),['class'=>'form-control-label'])); ?>

            <?php echo Form::textarea('text', null, ['class'=>'form-control','rows'=>'4','required'=>'required']); ?>

        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('name', __('Color'),['class'=>'form-control-label'])); ?>

            <div class="bg-color-label">
                <?php $__currentLoopData = $colors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$color): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="custom-control custom-radio  mb-3 <?php echo e($color); ?>">
                        <input class="custom-control-input" name="color" id="customCheck_<?php echo e($k); ?>" type="radio" value="<?php echo e($color); ?>" <?php if($note->color == $color): ?> checked <?php endif; ?> >
                        <label class="custom-control-label " for="customCheck_<?php echo e($k); ?>"></label>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="col-12 text-right">
            <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/notes/edit.blade.php ENDPATH**/ ?>