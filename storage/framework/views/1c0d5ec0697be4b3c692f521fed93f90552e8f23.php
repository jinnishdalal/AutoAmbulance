<div class="card bg-none card-box">
    <?php echo e(Form::model($user,array('route' => array('users.update', $user->id), 'method' => 'PUT'))); ?>

    <div class="row">
        <div class="col-md-6 form-group">
            <?php echo e(Form::label('name',__('Name'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control ','placeholder'=>__('Enter User Name')))); ?>

        </div>
        <div class="col-md-6 form-group">
            <?php echo e(Form::label('email',__('Email'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter User Email')))); ?>

        </div>
        <?php if(\Auth::user()->type != 'super admin'): ?>
            <div class="form-group col-md-12">
                <?php echo e(Form::label('role', __('User Role'),['class'=>'form-control-label'])); ?>

                <?php echo Form::select('role', $roles, $user->roles,array('class' => 'form-control select2','required'=>'required')); ?>

            </div>
        <?php endif; ?>
        <div class="form-group col-12 text-right">
            <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home/dvjxg9j1xkts/public_html/dashboard.drjitendrakodilkar.com/resources/views/user/edit.blade.php ENDPATH**/ ?>