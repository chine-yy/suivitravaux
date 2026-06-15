<?php $__env->startSection('title', 'Signaler un Incident'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.incidents.index')); ?>" class="text-decoration-none">Incidents</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-exclamation-triangle me-2 text-green"></i>Signaler un nouvel Incident</h1>
                <p class="cp-page-subtitle">Renseignez les informations de l'incident. Il sera rattaché au projet sélectionné.</p>
            </div>
        </div>

        <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-pencil-square me-2 text-green"></i>Détails de l'Incident</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.incidents.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="projet_id" class="form-label fw-semibold">Projet concerné <span class="text-danger">*</span></label>
                            <select name="projet_id" id="projet_id" class="form-select <?php $__errorArgs = ['projet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="" selected disabled>Choisir un projet...</option>
                                <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($projet->id); ?>" <?php echo e(old('projet_id') == $projet->id ? 'selected' : ''); ?>>
                                        <?php echo e($projet->nom); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['projet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6">
                            <label for="titre" class="form-label fw-semibold">Titre de l'incident <span class="text-danger">*</span></label>
                            <input type="text" name="titre" id="titre" class="form-control <?php $__errorArgs = ['titre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('titre')); ?>" placeholder="Ex: Retard livraison matériel, Panne machine..." required>
                            <?php $__errorArgs = ['titre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Niveau de gravité <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-4 pt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gravite" id="gravite_faible" value="faible" <?php echo e(old('gravite') == 'faible' ? 'checked' : ''); ?> required>
                                    <label class="form-check-label text-info fw-medium" for="gravite_faible">
                                        <i class="bi bi-info-circle me-1"></i>Faible
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gravite" id="gravite_moyen" value="moyen" <?php echo e(old('gravite') == 'moyen' || !old('gravite') ? 'checked' : ''); ?>>
                                    <label class="form-check-label text-warning fw-medium" for="gravite_moyen">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Moyen
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gravite" id="gravite_critique" value="critique" <?php echo e(old('gravite') == 'critique' ? 'checked' : ''); ?>>
                                    <label class="form-check-label text-danger fw-medium" for="gravite_critique">
                                        <i class="bi bi-fire me-1"></i>Critique
                                    </label>
                                </div>
                            </div>
                            <?php $__errorArgs = ['gravite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Description détaillée <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="5" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                placeholder="Décrivez précisément l'incident rencontré..." required><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="d-flex gap-3 pt-3 border-top mt-4 justify-content-end">
                        <a href="<?php echo e(route('super-admin.incidents.index')); ?>" class="btn btn-outline-secondary px-4 py-2">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-green btn-with-border px-5 py-2 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-2"></i>Enregistrer l'Incident
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .text-green { color: #009A44 !important; }
    .btn-green { background: #009A44; color: white; border: none; transition: all 0.3s ease; }
    .btn-green:hover { background: #007a35; color: white; }
    .btn-with-border { border: 2px solid #009A44 !important; }
    .form-control:focus, .form-select:focus { border-color: #009A44; box-shadow: 0 0 0 0.25rem rgba(0, 154, 68, 0.25); }
    .cp-breadcrumb-separator { margin: 0 0.5rem; color: #6c757d; }
    .cp-breadcrumb-item { color: #009A44; font-weight: 600; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/incidents/create.blade.php ENDPATH**/ ?>