<?php $__env->startSection('page-title'); ?>
    <?php echo e(__('Manage Email Templates')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped dataTable">
                        <thead>
                        <tr>
                            <th width="92%"><?php echo e(__('Name')); ?></th>
                            <th><?php echo e(__('Action')); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $EmailTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $EmailTemplate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($EmailTemplate->name); ?></td>
                                <td class="Action">
                    <span>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit email template lang')): ?>
                            <a href="<?php echo e(route('manage.email.language',[$EmailTemplate->id,\Auth::user()->currentLanguage()])); ?>" class="edit-icon">
                            <i class="fas fa-eye"></i>
                        </a>
                        <?php endif; ?>
                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/dvjxg9j1xkts/public_html/dashboard.drjitendrakodilkar.com/resources/views/email_templates/index.blade.php ENDPATH**/ ?>