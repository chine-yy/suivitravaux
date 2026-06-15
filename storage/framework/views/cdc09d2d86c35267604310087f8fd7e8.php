<?php $__env->startSection('title', 'Nouveau Rendez-vous'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.rendezvous.index')); ?>" class="text-decoration-none">Rendez-vous</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouveau Rendez-vous</h1>
                <p class="cp-page-subtitle">Planifiez un nouveau rendez-vous</p>
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
                <form action="<?php echo e(route('super-admin.rendezvous.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
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
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('titre')); ?>" required>
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
                                <option value="reunion" <?php echo e(old('type') == 'reunion' ? 'selected' : ''); ?>>Réunion</option>
                                <option value="visite" <?php echo e(old('type') == 'visite' ? 'selected' : ''); ?>>Visite</option>
                                <option value="appel" <?php echo e(old('type') == 'appel' ? 'selected' : ''); ?>>Appel</option>
                                <option value="autre" <?php echo e(old('type') == 'autre' ? 'selected' : ''); ?>>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="type_autre_container" style="<?php echo e(old('type') == 'autre' ? '' : 'display: none;'); ?>">
                            <label class="form-label fw-semibold">Précisez le type <span class="text-danger">*</span></label>
                            <input type="text" name="type_autre" class="form-control <?php $__errorArgs = ['type_autre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('type_autre')); ?>" placeholder="Ex: Déjeuner d'affaires, etc.">
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
                            <input type="datetime-local" name="date_heure" class="form-control <?php $__errorArgs = ['date_heure'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('date_heure')); ?>" required>
                            <?php $__errorArgs = ['date_heure'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Durée (minutes)</label>
                            <input type="number" name="duree_minutes" class="form-control" value="<?php echo e(old('duree_minutes', 60)); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lieu</label>
                            <input type="text" name="lieu" class="form-control" value="<?php echo e(old('lieu')); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Partenaire</label>
                            <select name="partenaire_id" id="partenaire_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                <?php $__currentLoopData = $partenaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partenaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($partenaire->id); ?>" <?php echo e(old('partenaire_id') == $partenaire->id ? 'selected' : ''); ?>><?php echo e($partenaire->name); ?> <?php echo e($partenaire->prenom ?? ''); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet</label>
                            <select name="projet_id" id="projet_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $partenaireIds = collect();
                                        if ($projet->partenaire_id) {
                                            $partenaireIds->push($projet->partenaire_id);
                                        }
                                        foreach ($projet->partenaires as $partenaire) {
                                            $partenaireIds->push($partenaire->id);
                                        }
                                        $partenaireIdsJson = json_encode($partenaireIds->unique()->values()->toArray());
                                    ?>
                                <option value="<?php echo e($projet->id); ?>"
                                    data-partenaire-ids="<?php echo e($partenaireIdsJson); ?>"
                                    <?php echo e(old('projet_id') == $projet->id ? 'selected' : ''); ?>><?php echo e($projet->nom); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="planifie" <?php echo e(old('statut', 'planifie') == 'planifie' ? 'selected' : ''); ?>>Planifié</option>
                                <option value="confirme" <?php echo e(old('statut') == 'confirme' ? 'selected' : ''); ?>>Confirmé</option>
                                <option value="termine" <?php echo e(old('statut') == 'termine' ? 'selected' : ''); ?>>Terminé</option>
                                <option value="annule" <?php echo e(old('statut') == 'annule' ? 'selected' : ''); ?>>Annulé</option>
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
    function filterProjetsByPartenaire(autoSelect) {
        const partenaireId = document.getElementById('partenaire_id').value;
        const projetSelect = document.getElementById('projet_id');
        const options = projetSelect.querySelectorAll('option');
        let firstVisible = null;
        options.forEach(opt => {
            if (opt.value === '') return;
            let match = false;
            if (!partenaireId) {
                match = true;
            } else {
                try {
                    const ids = JSON.parse(opt.dataset.partenaireIds || '[]');
                    match = ids.includes(parseInt(partenaireId));
                } catch (e) {
                    match = false;
                }
            }
            opt.style.display = match ? '' : 'none';
            if (match && !firstVisible) firstVisible = opt;
        });
        if (autoSelect && firstVisible) {
            projetSelect.value = firstVisible.value;
        }
    }

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

        document.getElementById('partenaire_id').addEventListener('change', function () {
            filterProjetsByPartenaire(true);
        });
        filterProjetsByPartenaire(false);
    });
</script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/rendezvous/create.blade.php ENDPATH**/ ?>