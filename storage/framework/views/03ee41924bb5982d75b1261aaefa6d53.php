<?php $__env->startSection('title', 'Modifier Rendez-vous'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.rendezvous.index')); ?>" class="text-decoration-none">Rendez-vous</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Modifier</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier Rendez-vous</h1>
                <p class="cp-page-subtitle">Mettez à jour les informations du rendez-vous</p>
            </div>
            <a href="<?php echo e(route('super-admin.rendezvous.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Rendez-vous
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-calendar-event me-2"></i>Détails du rendez-vous</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.rendezvous.update', $rendezvous->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                            <input type="text" name="titre" class="form-control <?php $__errorArgs = ['titre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('titre', $rendezvous->titre)); ?>" required>
                            <?php $__errorArgs = ['titre'];
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
                            <select name="type" id="type_select" class="form-select" required>
                                <option value="reunion" <?php echo e($rendezvous->type == 'reunion' ? 'selected' : ''); ?>>Réunion</option>
                                <option value="visite" <?php echo e($rendezvous->type == 'visite' ? 'selected' : ''); ?>>Visite</option>
                                <option value="appel" <?php echo e($rendezvous->type == 'appel' ? 'selected' : ''); ?>>Appel</option>
                                <option value="autre" <?php echo e($rendezvous->type == 'autre' ? 'selected' : ''); ?>>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="type_autre_container" style="<?php echo e($rendezvous->type == 'autre' ? '' : 'display: none;'); ?>">
                            <label class="form-label fw-semibold">Précisez le type <span class="text-danger">*</span></label>
                            <input type="text" name="type_autre" class="form-control <?php $__errorArgs = ['type_autre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('type_autre', $rendezvous->type_autre)); ?>" placeholder="Ex: Déjeuner d'affaires, etc.">
                            <?php $__errorArgs = ['type_autre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date & Heure <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_heure" class="form-control" value="<?php echo e($rendezvous->date_heure ? date('Y-m-d\TH:i', strtotime($rendezvous->date_heure)) : ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Durée (minutes)</label>
                            <input type="number" name="duree_minutes" class="form-control" value="<?php echo e(old('duree_minutes', $rendezvous->duree_minutes)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lieu</label>
                            <input type="text" name="lieu" class="form-control" value="<?php echo e(old('lieu', $rendezvous->lieu)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet</label>
                            <select name="projet_id" id="projet_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($projet->id); ?>" <?php echo e($rendezvous->projet_id == $projet->id ? 'selected' : ''); ?>><?php echo e($projet->nom); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="planifie" <?php echo e($rendezvous->statut == 'planifie' ? 'selected' : ''); ?>>Planifié</option>
                                <option value="confirme" <?php echo e($rendezvous->statut == 'confirme' ? 'selected' : ''); ?>>Confirmé</option>
                                <option value="termine" <?php echo e($rendezvous->statut == 'termine' ? 'selected' : ''); ?>>Terminé</option>
                                <option value="annule" <?php echo e($rendezvous->statut == 'annule' ? 'selected' : ''); ?>>Annulé</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo e($rendezvous->description); ?></textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="<?php echo e(route('super-admin.rendezvous.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
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
        const typeSelect = document.getElementById('type_select');
        const typeAutreContainer = document.getElementById('type_autre_container');
        const typeAutreInput = typeAutreContainer.querySelector('input');

        function toggleTypeAutre() {
            if (typeSelect.value === 'autre') {
                typeAutreContainer.style.display = 'block';
                typeAutreInput.setAttribute('required', 'required');
            } else {
                typeAutreContainer.style.display = 'none';
                typeAutreInput.removeAttribute('required');
            }
        }

        typeSelect.addEventListener('change', toggleTypeAutre);
        toggleTypeAutre();
    });
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/rendezvous/edit.blade.php ENDPATH**/ ?>