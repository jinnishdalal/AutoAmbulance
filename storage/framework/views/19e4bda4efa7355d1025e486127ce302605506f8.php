<div class="card bg-none card-box">
    <?php echo e(Form::model($payment, array('route' => array('payments.update', $payment->id), 'method' => 'PUT','enctype' => "multipart/form-data"))); ?>

    <div class="row">
        <div class="form-group col-12">
            <?php echo e(Form::label('name', __('Payment Name'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('name', null, array('class' => 'form-control ','required'=>'required'))); ?>

        </div>
        <div class="col-12 text-right">
            <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/payments/edit.blade.php ENDPATH**/ ?>