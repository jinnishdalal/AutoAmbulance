<div class="card bg-none card-box">
    <?php echo e(Form::open(array('url'=>'clients','method'=>'post'))); ?>

    <div class="row">
        <div class="col-md-6 form-group">
            <?php echo e(Form::label('name',__('Name'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Client Name'),'required'=>'required'))); ?>

        </div>
        <div class="col-md-6 form-group">
            <?php echo e(Form::label('email',__('Email'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Client Email'),'required'=>'required'))); ?>

        </div>
        <div class="col-md-12 form-group">
            <?php echo e(Form::label('password',__('Password'),['class'=>'form-control-label'])); ?>

            <?php echo e(Form::password('password',array('class'=>'form-control','placeholder'=>__('Enter Client Password'),'minlength'=>"6",'required'=>'required'))); ?>

        </div>
        <div class="form-group col-12 text-right">
            <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn-create badge-blue">
            <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn-create bg-gray" data-dismiss="modal">
        </div>
    </div>
    <?php echo e(Form::close()); ?>

</div>
<?php /**PATH /home1/xgjtsdo/work.xalop.com/resources/views/client/create.blade.php ENDPATH**/ ?>