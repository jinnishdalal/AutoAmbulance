<div class="card bg-none card-box">
    <?php echo e(Form::model($coupon, array('route' => array('coupons.update', $coupon->id), 'method' => 'PUT'))); ?>

    <div class="row">
        <div class="form-group col-md-12">
            <?php echo e(Form::label('name',__('Name'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control ','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('discount',__('Discount'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('discount',null,array('class'=>'form-control','required'=>'required','min'=>'1','max'=>'100','step'=>'0.01'))); ?>

            <span class="small"><?php echo e(__('Note: Discount in Percentage')); ?></span>
        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('limit',__('Limit'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('limit',null,array('class'=>'form-control','min'=>'1','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('code',__('Code'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('code',null,array('class'=>'form-control','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-12 text-right">
            <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/coupon/edit.blade.php ENDPATH**/ ?>