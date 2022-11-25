<div class="card bg-none card-box">
    <?php echo e(Form::open(array('url' => 'invoices'))); ?>

    <div class="row">
        <div class="form-group col-md-6">
            <?php echo e(Form::label('project_id', __('Project'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::select('project_id', $projects,null, array('class' => 'form-control select2'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('issue_date', __('Issue Date'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('issue_date', '', array('class' => 'form-control datepicker','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('due_date', __('Due Date'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('due_date', '', array('class' => 'form-control datepicker','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('tax_id', __('Tax %'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::select('tax_id', $taxes,null, array('class' => 'form-control select2'))); ?>

        </div>
        <div class="form-group col-md-12">
            <?php echo e(Form::label('terms', __('Terms'),['class'=>'form-control-label'])); ?>

            <?php echo Form::textarea('terms', null, ['class'=>'form-control','rows'=>'2']); ?>

        </div>
        <div class="col-12 text-right">
            <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/invoices/create.blade.php ENDPATH**/ ?>