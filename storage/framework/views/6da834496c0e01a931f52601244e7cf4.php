<?php $__env->startSection('title', 'Nouveau Document'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.documents.index')); ?>" class="text-decoration-none">Documents</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouveau Document</h1>
                <p class="cp-page-subtitle">Ajoutez un nouveau document au système</p>
            </div>
            <a href="<?php echo e(route('super-admin.documents.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Documents
            </a>
        </div>

        <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-file-earmark me-2"></i>Détails du document</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.documents.store')); ?>" method="POST" enctype="multipart/form-data">
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
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="contrat" <?php echo e(old('type') == 'contrat' ? 'selected' : ''); ?>>Contrat</option>
                                <option value="facture" <?php echo e(old('type') == 'facture' ? 'selected' : ''); ?>>Facture</option>
                                <option value="rapport" <?php echo e(old('type') == 'rapport' ? 'selected' : ''); ?>>Rapport</option>
                                <option value="photo" <?php echo e(old('type') == 'photo' ? 'selected' : ''); ?>>Photo</option>
                                <option value="plan" <?php echo e(old('type') == 'plan' ? 'selected' : ''); ?>>Plan</option>
                                <option value="autre" <?php echo e(old('type') == 'autre' ? 'selected' : ''); ?>>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="div_type_personnalise" style="display: <?php echo e(old('type') == 'autre' ? 'block' : 'none'); ?>;">
                            <label class="form-label fw-semibold text-green">Précisez le type <span class="text-danger">*</span></label>
                            <input type="text" name="type_personnalise" id="input_type_personnalise" class="form-control <?php $__errorArgs = ['type_personnalise'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('type_personnalise')); ?>">
                            <?php $__errorArgs = ['type_personnalise'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet</label>
                            <select name="projet_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($projet->id); ?>" <?php echo e(old('projet_id') == $projet->id ? 'selected' : ''); ?>><?php echo e($projet->nom); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie</label>
                            <input type="text" name="categorie" class="form-control" value="<?php echo e(old('categorie')); ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Fichier</label>
                            <input type="file" name="fichier" class="form-control <?php $__errorArgs = ['fichier'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <?php $__errorArgs = ['fichier'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-1"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="actif" <?php echo e(old('statut', 'actif') == 'actif' ? 'selected' : ''); ?>>Actif</option>
                                <option value="archive" <?php echo e(old('statut') == 'archive' ? 'selected' : ''); ?>>Archivé</option>
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
                        <a href="<?php echo e(route('super-admin.documents.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.querySelector('select[name="type"]');
        const customTypeDiv = document.getElementById('div_type_personnalise');
        const customTypeInput = document.getElementById('input_type_personnalise');

        function toggleCustomType() {
            if (typeSelect.value === 'autre') {
                customTypeDiv.style.display = 'block';
                customTypeInput.setAttribute('required', 'required');
            } else {
                customTypeDiv.style.display = 'none';
                customTypeInput.removeAttribute('required');
                customTypeInput.value = '';
            }
        }

        typeSelect.addEventListener('change', toggleCustomType);
        toggleCustomType(); // Initial check on page load
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/documents/create.blade.php ENDPATH**/ ?>