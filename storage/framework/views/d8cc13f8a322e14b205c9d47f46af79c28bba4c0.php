<div class="card bg-none card-box">
    <?php echo e(Form::open(array('url' => 'taxes'))); ?>

    <div class="row">
        <div class="form-group col-md-6">
            <?php echo e(Form::label('name', __('Tax Rate Name'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('name', '', array('class' => 'form-control','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('rate', __('Tax Rate %'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('rate', '', array('class' => 'form-control','required'=>'required','step'=>'0.01'))); ?>

        </div>
        <div class="col-12 text-right">
            <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/taxes/create.blade.php ENDPATH**/ ?>