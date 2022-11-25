<div class="card bg-none card-box">
    <?php echo e(Form::model($estimation, array('route' => array('estimations.update', $estimation->id), 'method' => 'PUT'))); ?>

    <div class="row">
        <div class="form-group col-6">
            <?php echo e(Form::label('client_id', __('Client'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::select('client_id', $client,null, array('class' => 'form-control select2','required'=>'required'))); ?>

        </div>
        <div class="form-group col-6">
            <?php echo e(Form::label('status', __('Status'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::select('status', \App\Estimation::$statues,null, array('class' => 'form-control select2','required'=>'required'))); ?>

        </div>
        <div class="form-group col-6">
            <?php echo e(Form::label('issue_date', __('Issue Date'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('issue_date',null, array('class' => 'form-control datepicker','required'=>'required'))); ?>

        </div>
        <div class="form-group col-6">
            <?php echo e(Form::label('discount', __('Discount'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('discount',null, array('class' => 'form-control','required'=>'required','min'=>"0"))); ?>

        </div>
        <div class="form-group col-6">
            <?php echo e(Form::label('tax_id', __('Tax %'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::select('tax_id', $taxes,null, array('class' => 'form-control select2','required'=>'required'))); ?>

        </div>
        <div class="form-group col-12">
            <?php echo e(Form::label('terms', __('Terms'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::textarea('terms',null, array('class' => 'form-control'))); ?>

        </div>
        <div class="col-12 text-right">
            <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/estimations/edit.blade.php ENDPATH**/ ?>