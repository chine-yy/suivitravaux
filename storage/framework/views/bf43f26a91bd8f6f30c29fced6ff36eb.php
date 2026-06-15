<?php $__env->startSection('title', 'Modifier Fournisseur'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('role-dynamique.fournisseurs.index')); ?>" class="text-decoration-none">Fournisseurs</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Modifier</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier Fournisseur</h1>
                <p class="cp-page-subtitle">Mettez à jour les informations du fournisseur</p>
            </div>
            <a href="<?php echo e(route('role-dynamique.fournisseurs.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Fournisseurs
            </a>
        </div>


        <form action="<?php echo e(route('role-dynamique.fournisseurs.update', $fournisseur->id)); ?>" method="POST">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <!-- Card 1: Infos de l'entreprise -->
            <div class="cp-chart-card mb-4 shadow-sm border-0">
                <div class="cp-chart-header border-bottom py-3">
                    <h6 class="cp-chart-title mb-0"><i class="bi bi-building me-2"></i>Infos de l'entreprise</h6>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nom', $fournisseur->nom)); ?>" required>
                            <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $fournisseur->email)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="<?php echo e(old('telephone', $fournisseur->telephone)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie</label>
                            <input type="text" name="categorie" class="form-control" value="<?php echo e(old('categorie', $fournisseur->categorie)); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Adresse</label>
                            <textarea name="adresse" class="form-control" rows="2"><?php echo e(old('adresse', $fournisseur->adresse)); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Site Web</label>
                            <input type="url" name="site_web" class="form-control" value="<?php echo e(old('site_web', $fournisseur->site_web)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="actif" <?php echo e($fournisseur->statut == 'actif' ? 'selected' : ''); ?>>Actif</option>
                                <option value="inactif" <?php echo e($fournisseur->statut == 'inactif' ? 'selected' : ''); ?>>Inactif</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Personne de contact -->
            <div class="cp-chart-card mb-4 shadow-sm border-0">
                <div class="cp-chart-header border-bottom py-3">
                    <h6 class="cp-chart-title mb-0"><i class="bi bi-person me-2"></i>Infos personne à contacter</h6>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nom</label>
                            <input type="text" name="contact_nom" class="form-control" value="<?php echo e(old('contact_nom', $fournisseur->contact_nom)); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prénom</label>
                            <input type="text" name="contact_prenom" class="form-control" value="<?php echo e(old('contact_prenom', $fournisseur->contact_prenom)); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="contact_telephone" class="form-control" value="<?php echo e(old('contact_telephone', $fournisseur->contact_telephone)); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"><?php echo e(old('notes', $fournisseur->notes)); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 pt-4">
                <button type="submit" class="btn btn-primary px-5 py-2 fw-medium shadow-sm transition-all hover-translate-y">
                    <i class="bi bi-check2 me-2"></i>Enregistrer
                </button>
                <a href="<?php echo e(route('role-dynamique.fournisseurs.index')); ?>" class="btn btn-outline-secondary px-5 py-2 fw-medium">Annuler</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.role-dynamique', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/role-dynamique/fournisseurs/edit.blade.php ENDPATH**/ ?>