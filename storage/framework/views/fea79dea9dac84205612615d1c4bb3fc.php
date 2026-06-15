<?php $__env->startSection('title', 'Gestion des Permissions'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <span class="app-breadcrumb-item">Permissions</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-card">
    <div class="admin-card-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="admin-card-title"><i class="bi bi-shield-check me-2"></i>Gestion des Permissions</h3>
            <p class="text-muted mb-0 small"><?php echo e($totalPermissions); ?> permissions réparties sur <?php echo e($totalRoles); ?> rôles</p>
        </div>
    </div>

    <div class="admin-card-body p-0">
        <?php $__empty_1 = true; $__currentLoopData = $groupedPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $groupPermissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $groupTotal = collect($groupPermissions)->sum(fn ($module) => count($module['permissions']));
            ?>
            <div class="permission-module-section">
                <div class="permission-module-header d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-folder text-green me-2"></i>
                        <?php echo e($groupName); ?>

                        <span class="badge bg-secondary-soft text-secondary ms-2"><?php echo e($groupTotal); ?></span>
                    </h6>
                    <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#group-<?php echo e(Str::slug($groupName)); ?>">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>

                <div class="collapse show" id="group-<?php echo e(Str::slug($groupName)); ?>">
                    <?php $__currentLoopData = $groupPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $moduleName => $moduleData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-3 border-bottom">
                            <h6 class="mb-2 fw-semibold">
                                <i class="bi bi-<?php echo e($moduleData['icon'] ?? 'circle'); ?> me-2 text-primary"></i>
                                <?php echo e($moduleData['nom']); ?>

                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php $__currentLoopData = $moduleData['permissions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-<?php echo e($permission->color ?? 'secondary'); ?> me-1">
                                            <?php echo e(\App\Models\Permission::$actionLabels[$permission->action] ?? ucfirst($permission->action)); ?>

                                        </span>
                                        <span class="text-muted small"><?php echo e($permission->nom); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shield-slash display-1 opacity-25"></i>
                <p class="mt-3">Aucune permission définie</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.permission-module-section {
    border-bottom: 1px solid #dee2e6;
}
.permission-module-section:last-child {
    border-bottom: none;
}
.permission-module-header {
    background-color: #f8f9fa !important;
}
.bg-secondary-soft {
    background-color: #f8f9fa !important;
}
.text-green {
    color: #009A44 !important;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/permissions/index.blade.php ENDPATH**/ ?>