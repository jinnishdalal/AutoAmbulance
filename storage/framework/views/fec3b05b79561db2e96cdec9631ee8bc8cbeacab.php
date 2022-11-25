<div class="card bg-none card-box">
    <?php echo e(Form::model($bugStatus, array('route' => array('bugstatus.update', $bugStatus->id), 'method' => 'PUT'))); ?>

    <div class="row">
        <div class="form-group col-12">
            <?php echo e(Form::label('title', __('Bug Status Title'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('title',null, array('class' => 'form-control','required'=>'required'))); ?>

        </div>
        <div class="col-12 text-right">
            <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/bugstatus/edit.blade.php ENDPATH**/ ?>