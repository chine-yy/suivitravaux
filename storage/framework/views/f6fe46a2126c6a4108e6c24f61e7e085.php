<?php $__env->startSection('title', 'Mon Profil'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="cp-breadcrumb-item">Mon Profil</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="cp-dashboard">
        <div class="cp-content">
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title"><i class="bi bi-person me-2"></i>Mon Profil</h1>
                    <p class="cp-page-subtitle">Gérez vos informations et vos préférences</p>
                </div>
            </div>


            <div class="row g-4">
                <div class="col-lg-7 col-xl-8 order-2 order-lg-1">
                    <div class="cp-chart-card">
                        <div class="cp-chart-header">
                            <h6 class="cp-chart-title"><i class="bi bi-person-badge me-2"></i>Informations personnelles</h6>
                        </div>
                        <div class="p-4">
                            <form method="POST" action="<?php echo e(route('super-admin.parametres.update')); ?>"
                                enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nom</label>
                                        <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Prénom</label>
                                        <input type="text" class="form-control" name="prenom"
                                            value="<?php echo e(old('prenom', $user->prenom)); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Téléphone</label>
                                        <input type="text" class="form-control" name="telephone"
                                            value="<?php echo e(old('telephone', $user->telephone)); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Photo de profil</label>
                                        <input type="file" class="form-control" name="photo" accept="image/*">
                                        <small class="text-muted">Formats: JPEG, PNG, JPG, GIF. Max: 2 Mo.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Statut</label>
                                        <select class="form-select" name="is_active">
                                            <option value="1" <?php echo e((string) old('is_active', (int) ($user->is_active ?? true)) === '1' ? 'selected' : ''); ?>>
                                                Actif
                                            </option>
                                            <option value="0" <?php echo e((string) old('is_active', (int) ($user->is_active ?? true)) === '0' ? 'selected' : ''); ?>>
                                                Inactif
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 pt-4 border-top">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-check2 me-2"></i>Enregistrer
                                    </button>
                                    <a href="<?php echo e(route('super-admin.dashboard')); ?>"
                                        class="btn btn-outline-secondary px-4">Annuler</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    
                    <div class="cp-chart-card mt-4">
                        <div class="cp-chart-header">
                            <h6 class="cp-chart-title"><i class="bi bi-shield-lock me-2"></i>Sécurité — Changer le mot de
                                passe</h6>
                        </div>
                        <div class="p-4">
                            <form method="POST" action="<?php echo e(route('super-admin.parametres.update')); ?>"
                                enctype="multipart/form-data">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <input type="hidden" name="name" value="<?php echo e($user->name); ?>">
                                <input type="hidden" name="email" value="<?php echo e($user->email); ?>">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nouveau mot de passe</label>
                                        <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                            name="password" placeholder="Laisser vide pour ne pas changer">
                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                                        <input type="password" class="form-control" name="password_confirmation"
                                            placeholder="Répétez le nouveau mot de passe">
                                    </div>
                                </div>

                                <div class="d-flex gap-2 pt-4 border-top mt-4">
                                    <button type="submit" class="btn btn-warning px-4">
                                        <i class="bi bi-lock me-2"></i>Mettre à jour le mot de passe
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-xl-4 order-1 order-lg-2">
                    <div class="cp-chart-card mb-4">
                        <div class="cp-chart-header">
                            <h6 class="cp-chart-title"><i class="bi bi-person me-2"></i>Aperçu du profil</h6>
                        </div>
                        <div class="p-4 text-center">
                            <?php if($user->photo_url): ?>
                                <img src="<?php echo e($user->photo_url); ?>" class="rounded-circle mb-3"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Avatar">
                            <?php else: ?>
                                <?php
                                    $previewInitials = strtoupper(substr($user->prenom ?? '', 0, 1) . substr($user->name ?? '', 0, 1));
                                ?>
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                    style="width: 100px; height: 100px; background: linear-gradient(135deg, #009A44, #d97706); color: #fff; font-size: 2.5rem; font-weight: 600;">
                                    <?php echo e($previewInitials); ?>

                                </div>
                            <?php endif; ?>

                            <h5 class="fw-bold mb-1"><?php echo e($user->name); ?></h5>
                            <p class="text-muted small mb-2">Super Admin</p>
                            <span class="badge mb-4"
                                style="background-color: #009A44; color: #fff;"><?php echo e(($user->is_active ?? true) ? 'Actif' : 'Inactif'); ?></span>

                            <div class="text-start mt-4">
                                <p class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i> <?php echo e($user->email); ?></p>
                                <p class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i>
                                    <?php echo e($user->telephone ?? 'Non renseigné'); ?></p>
                                <p class="mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i> Inscrit depuis
                                    <?php echo e($user->created_at->format('d/m/Y')); ?>

                                </p>
                            </div>
                            <div class="mt-2">
                                <form method="POST" action="<?php echo e(route('super-admin.parametres.photo.destroy')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm px-3"
                                        style="background-color: #009A44; color: #fff; border: none;"
                                        onclick="return confirm('Confirmer la suppression de la photo ?');">
                                        <i class="bi bi-trash me-1"></i>Supprimer photo
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/profil/index.blade.php ENDPATH**/ ?>