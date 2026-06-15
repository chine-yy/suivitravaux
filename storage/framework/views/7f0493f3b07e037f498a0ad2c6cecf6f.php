<?php $__env->startSection('title', 'Nouveau Stock'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.stocks.index')); ?>" class="text-decoration-none">Stocks</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouveau Stock</h1>
                <p class="cp-page-subtitle">Ajoutez un nouvel élément au stock</p>
            </div>
            <a href="<?php echo e(route('super-admin.stocks.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Stocks
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-box-seam me-2"></i>Détails du stock</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.stocks.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nom')); ?>" required>
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
                            <label class="form-label fw-semibold">Référence</label>
                            <input type="text" name="reference" class="form-control" value="<?php echo e(old('reference')); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie</label>
                            <input type="text" name="categorie" class="form-control" value="<?php echo e(old('categorie')); ?>" placeholder="Ex: Outillage, Matériaux...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fournisseur</label>
                            <select name="fournisseur_id" class="form-select">
                                <option value="">-- Sélectionner --</option>
                                <?php $__currentLoopData = $fournisseurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fournisseur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($fournisseur->id); ?>" <?php echo e(old('fournisseur_id') == $fournisseur->id ? 'selected' : ''); ?>><?php echo e($fournisseur->nom); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quantité <span class="text-danger">*</span></label>
                            <input type="number" name="quantite" class="form-control <?php $__errorArgs = ['quantite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('quantite', 0)); ?>" required>
                            <?php $__errorArgs = ['quantite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prix unitaire</label>
                            <input type="number" name="prix_unitaire" class="form-control" value="<?php echo e(old('prix_unitaire', 0)); ?>" step="0.01" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Emplacement</label>
                            <input type="text" name="emplacement" class="form-control" value="<?php echo e(old('emplacement')); ?>" placeholder="Ex: Entrepôt A, Rayon 3...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="disponible" <?php echo e(old('statut', 'disponible') == 'disponible' ? 'selected' : ''); ?>>Disponible</option>
                                <option value="epuise" <?php echo e(old('statut') == 'epuise' ? 'selected' : ''); ?>>Épuisé</option>
                                <option value="en_reapprovisionnement" <?php echo e(old('statut') == 'en_reapprovisionnement' ? 'selected' : ''); ?>>En réapprovisionnement</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo e(old('description')); ?></textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="<?php echo e(route('super-admin.stocks.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/stocks/create.blade.php ENDPATH**/ ?>