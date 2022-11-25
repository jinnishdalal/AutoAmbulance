<div class="card bg-none card-box">
    <?php echo e(Form::model($invoice, array('route' => array('invoices.products.store', $invoice->id), 'method' => 'POST'))); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <input type="text" class="form-control " value="<?php echo e((!empty($invoice->project)?$invoice->project->name:'')); ?>" readonly>
            </div>
        </div>

        <div class="col-12">
            <div class="d-flex radio-check">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="customRadio5" name="type" value="milestone" checked="checked" onclick="hide_show(this)">
                    <label class="custom-control-label text-dark" for="customRadio5"><?php echo e(__('Milestone & Task')); ?></label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" class="custom-control-input" id="customRadio6" name="type" value="other" onclick="hide_show(this)">
                    <label class="custom-control-label text-dark" for="customRadio6"><?php echo e(__('Other')); ?></label>
                </div>
            </div>
        </div>
    </div>
    <div id="milestone">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="milestone_id" class="form-control-label"><?php echo e(__('Milestone')); ?></label>
                    <select class="form-control select2" onchange="getTask(this,<?php echo e($invoice->project_id); ?>)" id="milestone_id" name="milestone_id">
                        <option value="" selected="selected"><?php echo e(__('Select Milestone')); ?></option>
                        <?php $__currentLoopData = $milestones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $milestone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($milestone->id); ?>"><?php echo e($milestone->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="task_id" class="form-control-label"><?php echo e(__('Task')); ?></label>
                    <select class="form-control select2" id="task_id" name="task_id">
                        <option value="" selected="selected"><?php echo e(__('Select Task')); ?></option>
                        <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($task->id); ?>"><?php echo e($task->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div id="other" style="display: none">
        <div id="milestone">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="title" class="form-control-label"><?php echo e(__('Title')); ?></label>
                        <input type="text" class="form-control " name="title">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="price" class="form-control-label"><?php echo e(__('Price')); ?></label>
                <input type="number" class="form-control " name="price" step="0.01" required>
            </div>
        </div>
        <div class="col-12 text-right">
            <?php if(isset($invoice)): ?>
                <input type="submit" value="<?php echo e(__('Add')); ?>" class="btn-create badge-blue">
            <?php else: ?>
                <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn-create badge-blue">
            <?php endif; ?>
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>

        <?php echo e(Form::close()); ?>

    </div>
</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/invoices/product.blade.php ENDPATH**/ ?>