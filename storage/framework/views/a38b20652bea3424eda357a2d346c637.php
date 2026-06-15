<?php $__env->startSection('title', 'Nouvelle Intervention'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.interventions.index')); ?>" class="text-decoration-none">Interventions</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouvelle Intervention</h1>
                <p class="cp-page-subtitle">Planifiez une nouvelle intervention</p>
            </div>
            <a href="<?php echo e(route('super-admin.interventions.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Interventions
            </a>
        </div>

        <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-tools me-2"></i>Détails de l'intervention</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.interventions.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" id="type_select" class="form-select" required onchange="toggleTypeAutre()">
                                <option value="">-- Sélectionner --</option>
                                <option value="installation" <?php echo e(old('type') == 'installation' ? 'selected' : ''); ?>>Installation</option>
                                <option value="maintenance" <?php echo e(old('type') == 'maintenance' ? 'selected' : ''); ?>>Maintenance</option>
                                <option value="reparation" <?php echo e(old('type') == 'reparation' ? 'selected' : ''); ?>>Réparation</option>
                                <option value="inspection" <?php echo e(old('type') == 'inspection' ? 'selected' : ''); ?>>Inspection</option>
                                <option value="autre" <?php echo e(old('type') == 'autre' ? 'selected' : ''); ?>>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="type_autre_container" style="display: <?php echo e(old('type') == 'autre' ? 'block' : 'none'); ?>;">
                            <label class="form-label fw-semibold">Type (Préciser) <span class="text-danger">*</span></label>
                            <input type="text" name="type_autre" class="form-control <?php $__errorArgs = ['type_autre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('type_autre')); ?>" placeholder="Spécifier le type">
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
                            <label class="form-label fw-semibold">Date Intervention <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_intervention" class="form-control <?php $__errorArgs = ['date_intervention'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('date_intervention')); ?>" required>
                            <?php $__errorArgs = ['date_intervention'];
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
                            <label class="form-label fw-semibold">Mission (Type) <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mission_type" id="mission_tache" value="tache" checked onchange="toggleMissionFields()">
                                    <label class="form-check-label" for="mission_tache">Tâche</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mission_type" id="mission_sous_tache" value="sous_tache" onchange="toggleMissionFields()">
                                    <label class="form-check-label" for="mission_sous_tache">Sous-tâche</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" id="mission_display_label">Tâche</label>

                            <div id="tache_container">
                                <select name="tache_id" class="form-select">
                                    <option value="">-- Sélectionner une tâche --</option>
                                    <?php $__currentLoopData = $taches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tache): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($tache->id); ?>"><?php echo e($tache->titre); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <div id="sous_tache_container" style="display: none;">
                                <select name="sous_tache_id" class="form-select">
                                    <option value="">-- Sélectionner une sous-tâche --</option>
                                    <?php $__currentLoopData = $sousTaches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($st->id); ?>"><?php echo e($st->titre); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Personnels</label>
                            <select name="technicien_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                <?php $__currentLoopData = $techniciens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $technicien): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($technicien->id); ?>" <?php echo e(old('technicien_id') == $technicien->id ? 'selected' : ''); ?>>
                                    <?php echo e($technicien->name); ?> <?php echo e($technicien->prenom); ?> - <?php echo e($technicien->role->nom ?? 'Sans rôle'); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="planifie" <?php echo e(old('statut', 'planifie') == 'planifie' ? 'selected' : ''); ?>>Planifié</option>
                                <option value="en_cours" <?php echo e(old('statut') == 'en_cours' ? 'selected' : ''); ?>>En cours</option>
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
                        <a href="<?php echo e(route('super-admin.interventions.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleTypeAutre() {
        var select = document.getElementById('type_select');
        var container = document.getElementById('type_autre_container');
        if (select.value === 'autre') {
            container.style.display = 'block';
            container.querySelector('input').setAttribute('required', 'required');
        } else {
            container.style.display = 'none';
            container.querySelector('input').removeAttribute('required');
        }
    }
    function toggleMissionFields() {
        var isTache = document.getElementById('mission_tache').checked;
        document.getElementById('mission_display_label').innerText = isTache ? 'Tâche' : 'Sous-tâche';
        document.getElementById('tache_container').style.display = isTache ? 'block' : 'none';
        document.getElementById('sous_tache_container').style.display = isTache ? 'none' : 'block';
    }
    window.addEventListener('DOMContentLoaded', function() {
        toggleTypeAutre();
        toggleMissionFields();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/interventions/create.blade.php ENDPATH**/ ?>