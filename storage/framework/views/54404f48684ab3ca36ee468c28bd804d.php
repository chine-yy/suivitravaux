<?php $__env->startSection('title', 'Créer un Rôle'); ?>
<?php $__env->startSection('breadcrumb'); ?>
<a href="<?php echo e(route('super-admin.roles.index')); ?>" class="text-decoration-none">Rôles</a>
<span class="cp-breadcrumb-separator">/</span>
<span class="cp-breadcrumb-item">Nouveau</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Creer un Role</h1>
                <p class="cp-page-subtitle">Definissez un nouveau role avec ses permissions</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('super-admin.configuration.logs')); ?>" class="btn btn-outline-info">
                    <i class="bi bi-file-earmark-text me-2"></i>Voir les logs
                </a>
                <a href="<?php echo e(route('super-admin.roles.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>
        </div>


        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-shield-plus me-2"></i>Informations du Rôle</h6>
                    </div>
                    <div class="p-4">
                        <form action="<?php echo e(route('super-admin.roles.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="mb-4">
                                <label for="nom" class="form-label fw-semibold">Nom du Rôle <span class="text-danger">*</span></label>
                                <input type="text" name="nom" id="nom" class="form-control form-control-lg <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Ex: Chef de Projet, Membre, Observateur..." value="<?php echo e(old('nom')); ?>" autofocus required>
                                <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold"><i class="bi bi-shield-lock me-2"></i>Permissions d'accès</label>
                                <p class="text-muted small mb-3">Cochez les autorisations pour chaque module.</p>

                                <?php
                                $groupedPermissions = \App\Models\Permission::getGroupedPermissions();
                                $checkedIds = old('permissions', []);
                                ?>

                                <div class="permission-matrix bg-white border rounded-3 overflow-hidden">
                                    <?php $__empty_1 = true; $__currentLoopData = $groupedPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $modules): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php echo $__env->make('partials.permission-matrix-body', ['groupedPermissions' => [$groupName => $modules], 'checkedIds' => $checkedIds], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="p-4 text-center text-muted">
                                        <i class="bi bi-exclamation-triangle fs-3 text-warning mb-2"></i>
                                        <p class="mb-0">Aucune permission disponible. Exécutez le seeder.</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check-lg me-2"></i>Créer le Rôle
                                </button>
                                <a href="<?php echo e(route('super-admin.roles.index')); ?>" class="btn btn-outline-secondary px-4">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Règles des permissions</h6>
                    </div>
                    <div class="p-3">
                        <div class="alert alert-info py-2 px-3 mb-3 small">
                            <i class="bi bi-eye me-1"></i>
                            <strong>Voir</strong> est <span class="text-danger fw-bold">obligatoire</span> pour activer toute autre action sur un module.
                        </div>
                        <p class="text-muted small mb-2 fw-semibold">Comportement automatique :</p>
                        <ul class="text-muted small mb-3 ps-3">
                            <li>Cocher <em>Créer / Modifier / Supprimer</em>… → <strong>Voir</strong> s'active seul</li>
                            <li>Décocher <strong>Voir</strong> → toutes les autres actions se désactivent</li>
                        </ul>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <span class="badge bg-primary"><i class="bi bi-eye me-1"></i>Voir *</span>
                            <span class="badge bg-success"><i class="bi bi-plus me-1"></i>Créer</span>
                            <span class="badge bg-warning text-dark"><i class="bi bi-pencil me-1"></i>Modifier</span>
                            <span class="badge bg-danger"><i class="bi bi-trash me-1"></i>Supprimer</span>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-info"><i class="bi bi-person-check me-1"></i>Assigner</span>
                            <span class="badge bg-secondary"><i class="bi bi-download me-1"></i>Exporter</span>
                            <span class="badge bg-dark"><i class="bi bi-key me-1"></i>Réinit.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/permissions-matrix.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/permissions-matrix.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/roles/create.blade.php ENDPATH**/ ?>