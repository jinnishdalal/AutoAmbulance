<div class="card bg-none card-box">
    <?php
        $project_id= Request::segment(2)
    ?>
    <?php echo e(Form::open(array('route' => array('task.timesheet.store',$project_id)))); ?>

    <div class="row">
        <div class="form-group col-md-6">
            <?php echo e(Form::label('project_id', __('Project'),['class'=>'form-control-label'])); ?>

            <?php echo Form::select('project_id', $projects, null,array('class' => 'form-control select2','required'=>'required')); ?>

            <?php if(count($projects) < 2): ?>
                <small><?php echo e(__('Please Create Project')); ?> <a href="<?php echo e(route('projects.index')); ?>"><?php echo e(__('here')); ?></a></small>
            <?php endif; ?>
        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('task_id', __('Task'),['class'=>'form-control-label'])); ?>

            <select name="task_id" id="task_id" class="form-control select2" required>
                <option value=""><?php echo e(__('Select Task')); ?></option>
            </select>
        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('date', __('Task Date'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('date', '', array('class' => 'form-control datepicker','required'=>'required'))); ?>

        </div>
        <div class="form-group col-md-6">
            <?php echo e(Form::label('hours', __('Task Hours'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::number('hours', '', array('class' => 'form-control','required'=>'required','min'=>0))); ?>

        </div>
        <div class="form-group  col-md-12">
            <?php echo e(Form::label('remark', __('Remark'),['class'=>'form-control-label'])); ?>

            <?php echo Form::textarea('remark', null, ['class'=>'form-control','rows'=>'2']); ?>

        </div>
        <div class="col-12 text-right">
            <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>

<script>
    $(document).on("change", "#commonModal select[name=project_id]", function () {
        $.ajax({
            url: '<?php echo e(route('timesheet.project.task')); ?>',
            data: {project_id: $(this).val(), _token: $('meta[name="csrf-token"]').attr('content')},
            type: 'POST',
            success: function (data) {
                $('#task_id').empty();
                $("#task_id").html('<option value="" selected="selected"><?php echo e(__('Select Task')); ?></option>');
                $.each(data, function (key, data) {
                    $("#task_id").append('<option value="' + key + '">' + data + '</option>');
                });
                $("#task_id").select2();
            }
        });
    });
</script>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/projects/timesheetCreate.blade.php ENDPATH**/ ?>