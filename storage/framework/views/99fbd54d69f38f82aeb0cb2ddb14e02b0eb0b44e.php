<div class="card bg-none card-box">
    <?php echo e(Form::open(array('url' => 'expenses','enctype' => "multipart/form-data"))); ?>

    <div class="row">
        <div class="form-group col-md-6">
            <?php echo e(Form::label('category_id', __('Category'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::select('category_id', $category,null, array('class' => 'form-control select2','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('amount', __('Amount'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('amount', '', array('class' => 'form-control','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('date', __('Date'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('date', '', array('class' => 'form-control datepicker','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('project_id', __('Project'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::select('project_id', $projects,null, array('class' => 'form-control select2','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('user_id', __('User'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::select('user_id', $users,null, array('class' => 'form-control select2','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('attachment', __('Attachment'),['class'=>'form-control-label'])); ?>

            <div class="choose-file form-group">
                <label for="attachment" class="form-control-label">
                    <div><?php echo e(__('Choose file here')); ?></div>
                    <input type="file" class="form-control" name="attachment" id="attachment" data-filename="attachment_update" accept=".jpeg,.jpg,.png,.doc,.pdf">
                </label>
                <p class="attachment_update"></p>
            </div>
        </div>
        <div class="form-group  col-md-12">
            <?php echo e(Form::label('description', __('Description'),['class'=>'form-control-label'])); ?>

            <?php echo Form::textarea('description', null, ['class'=>'form-control','rows'=>'2']); ?>

        </div>
        <div class="col-12 text-right">
            <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/expenses/create.blade.php ENDPATH**/ ?>