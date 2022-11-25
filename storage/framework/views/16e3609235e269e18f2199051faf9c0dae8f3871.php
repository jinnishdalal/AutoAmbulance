<div class="card bg-none card-box">
    <?php echo e(Form::open(array('url' => 'leads'))); ?>

    <div class="row">
        <div class="form-group col-md-6 ">
            <?php echo e(Form::label('name', __('Name'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('name', '', array('class' => 'form-control','required'=>'required'))); ?>

        </div>

        <div class="form-group  col-md-6">
            <?php echo e(Form::label('price', __('Price'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('price', '', array('class' => 'form-control','required'=>'required'))); ?>

        </div>
        <div class="form-group  col-md-6">
            <?php echo e(Form::label('stage', __('Stage'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::select('stage', $stages,null, array('class' => 'form-control select2','required'=>'required'))); ?>

        </div>
        <?php if(\Auth::user()->type=='company'): ?>
            <div class="form-group  col-md-6">
                <?php echo e(Form::label('owner', __('Lead User'),['class'=>'form-control-label'])); ?>

                <?php echo Form::select('owner', $owners, null,array('class' => 'form-control select2','required'=>'required')); ?>

            </div>
        <?php endif; ?>
        <div class="form-group  col-md-6">
            <?php echo e(Form::label('client', __('Client'),['class'=>'form-control-label'])); ?>

            <?php echo Form::select('client', $clients, null,array('class' => 'form-control select2','required'=>'required')); ?>

        </div>
        <div class="form-group  col-md-6">
            <?php echo e(Form::label('source', __('Source'),['class'=>'form-control-label'])); ?>

            <?php echo Form::select('source', $sources, null,array('class' => 'form-control select2','required'=>'required')); ?>

        </div>
        <div class="form-group  col-md-12">
            <?php echo e(Form::label('notes', __('Notes'),['class'=>'form-control-label'])); ?>

            <?php echo Form::textarea('notes', '',array('class' => 'form-control','rows'=>'3')); ?>

        </div>
        <div class="col-12 text-right">
            <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/leads/create.blade.php ENDPATH**/ ?>