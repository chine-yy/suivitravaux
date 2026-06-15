<?php $__env->startSection('title', 'Créer un Utilisateur'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('role-dynamique.users.index')); ?>" class="text-decoration-none">Utilisateurs</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-person-plus me-2"></i>Créer un Utilisateur</h1>
                <p class="cp-page-subtitle">Renseignez les informations du nouvel utilisateur. Le mot de passe sera généré automatiquement.</p>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-lg-7">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-person-badge me-2"></i>Informations de l'Utilisateur</h6>
                    </div>
                    <div class="p-4">
                        <form action="<?php echo e(route('role-dynamique.users.store')); ?>" method="POST" id="createUserForm">
                            <?php echo csrf_field(); ?>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('name')); ?>" placeholder="Nom de famille" required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" name="prenom" id="prenom" class="form-control <?php $__errorArgs = ['prenom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('prenom')); ?>" placeholder="Prénom" required>
                                    <?php $__errorArgs = ['prenom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>

                            <div class="mb-3 mt-3">
                                <label for="email" class="form-label fw-semibold">Adresse Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('email')); ?>" placeholder="utilisateur@exemple.com" required>
                                </div>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-3">
                                <label for="telephone" class="form-label fw-semibold">Numéro de Téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="telephone" id="telephone" class="form-control <?php $__errorArgs = ['telephone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        value="<?php echo e(old('telephone')); ?>" placeholder="+221 77 000 00 00">
                                </div>
                                <?php $__errorArgs = ['telephone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-4">
                                <label for="role_id" class="form-label fw-semibold">Rôle assigné <span class="text-danger">*</span></label>
                                <?php if($roles->isEmpty()): ?>
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        Aucun rôle disponible. <a href="<?php echo e(route('role-dynamique.roles.create')); ?>">Créer un rôle d'abord.</a>
                                    </div>
                                <?php else: ?>
                                <select name="role_id" id="role_id" class="form-select <?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Sélectionnez un rôle</option>
                                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php echo e(old('role_id') == $role->id ? 'selected' : ''); ?>>
                                        <?php echo e($role->nom); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php endif; ?>
                            </div>

                            <div class="alert alert-info border-0" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                                    <div>
                                        <strong>Mot de passe automatique</strong><br>
                                        <small>Un mot de passe sécurisé sera généré automatiquement et affiché à la création du compte.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary px-4" <?php echo e($roles->isEmpty() ? 'disabled' : ''); ?>>
                                    <i class="bi bi-person-plus me-2"></i>Créer l'Utilisateur
                                </button>
                                <a href="<?php echo e(route('role-dynamique.users.index')); ?>" class="btn btn-outline-secondary px-4">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="cp-chart-card mb-3">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-shield-check me-2"></i>Rôles disponibles</h6>
                    </div>
                    <div class="p-4">
                        <?php if($roles->isEmpty()): ?>
                            <p class="text-muted text-center">Aucun rôle. <a href="<?php echo e(route('role-dynamique.roles.create')); ?>">Créer un rôle</a></p>
                        <?php else: ?>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <span class="badge rounded-pill" style="background:linear-gradient(135deg,#009A44,#007a35);"><?php echo e($role->nom); ?></span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-green" data-bs-toggle="modal" data-bs-target="#modalRole<?php echo e($role->id); ?>">
                                    <i class="bi bi-eye me-1"></i>Voir
                                </button>
                                <?php if((int) ($role->entreprise_id ?? 0) === (int) (auth()->user()->entreprise_id ?? 0)): ?>
                                <a href="<?php echo e(route('role-dynamique.roles.edit', $role)); ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil me-1"></i>Modifier
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Modal Permissions du rôle -->
                        <div class="modal fade" id="modalRole<?php echo e($role->id); ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header py-2">
                                        <h6 class="modal-title"><i class="bi bi-shield-check me-2 text-green"></i><?php echo e($role->nom); ?></h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-3" style="max-height:60vh;overflow-y:auto;">
                                        <?php
                                            $rolePermIds = $role->permissions->pluck('id')->toArray();
                                            $grouped = \App\Models\Permission::getGroupedPermissions();
                                        ?>
                                        <?php if(count($rolePermIds) > 0): ?>
                                            <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $modules): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $roleModules = [];
                                                    foreach ($modules as $modSlug => $modData) {
                                                        $activePerms = collect($modData['permissions'])->filter(fn($p) => in_array($p->id, $rolePermIds));
                                                        if ($activePerms->count() > 0) {
                                                            $roleModules[$modSlug] = ['nom' => $modData['nom'], 'icon' => $modData['icon'], 'perms' => $activePerms];
                                                        }
                                                    }
                                                ?>
                                                <?php if(count($roleModules) > 0): ?>
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bi bi-folder2-open text-green me-2"></i>
                                                        <strong class="small"><?php echo e($groupName); ?></strong>
                                                    </div>
                                                    <?php $__currentLoopData = $roleModules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="d-flex align-items-center mb-1 ms-3">
                                                        <i class="bi bi-<?php echo e($mod['icon']); ?> text-muted me-2 small"></i>
                                                        <span class="text-muted small me-2" style="min-width:120px;"><?php echo e($mod['nom']); ?></span>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            <?php $__currentLoopData = $mod['perms']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <span class="badge bg-green-soft text-green small"><?php echo e(\App\Models\Permission::$actionLabels[$p->action] ?? ucfirst($p->action)); ?></span>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                            <div class="text-center py-3 text-muted">
                                                <i class="bi bi-shield-slash"></i>
                                                <p class="mt-1 mb-0 small">Aucune permission</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-green { color: #009A44 !important; }
.bg-green-soft { background-color: rgba(0, 154, 68, 0.1) !important; }
.btn-outline-green { color: #009A44; border-color: #009A44; }
.btn-outline-green:hover { background: #009A44; color: #fff; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.role-dynamique', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/role-dynamique/users/create.blade.php ENDPATH**/ ?>