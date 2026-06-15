<?php $__env->startSection('title', 'Inscrire des Partenaires'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.partenaires.index')); ?>" class="text-decoration-none text-muted">Partenaires</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Inscription Multiple</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-person-plus me-2"></i>Inscrire des Partenaires</h1>
                <p class="cp-page-subtitle">Création de <?php echo e($count); ?> compte(s) partenaire pour un projet</p>
            </div>
            <a href="<?php echo e(route('super-admin.partenaires.index')); ?>" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>


        <form action="<?php echo e(route('super-admin.partenaires.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="row g-4">
                <!-- Project Selection (Shared for all partenaires in this batch) -->
                <div class="col-12">
                    <div class="cp-chart-card mb-4 border-0 shadow-sm">
                        <div class="cp-chart-header py-3 bg-light">
                            <h6 class="cp-chart-title mb-0"><i class="bi bi-kanban me-2"></i>1. Sélection du Projet</h6>
                        </div>
                        <div class="p-4">
                            <label class="form-label fw-bold small">Projet à suivre <span class="text-danger">*</span></label>
                            <select class="form-select <?php $__errorArgs = ['projet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="projet_id" required>
                                <option value="">-- Sélectionner un projet non attribué --</option>
                                <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($projet->id); ?>" <?php echo e(old('projet_id') == $projet->id ? 'selected' : ''); ?>>
                                    <?php echo e($projet->nom); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="form-text text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i> Seuls les projets n'ayant aucun partenaire associé sont affichés.
                            </div>
                            <?php $__errorArgs = ['projet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <!-- Multiple Partenaires Info -->
                <div class="col-12">
                    <div class="cp-chart-card border-0 shadow-sm">
                        <div class="cp-chart-header py-3 bg-light">
                            <h6 class="cp-chart-title mb-0"><i class="bi bi-people me-2"></i>2. Informations des Partenaires (<?php echo e($count); ?>)</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Type de compte</label>
                                    <input type="text" class="form-control" value="Partenaire" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                            <?php for($i = 0; $i < $count; $i++): ?>
                            <div class="partenaire-form-block <?php echo e($i > 0 ? 'mt-5 pt-5 border-top' : ''); ?>">
                                <h6 class="fw-bold text-green mb-4"><i class="bi bi-person-circle me-2"></i>Partenaire #<?php echo e($i + 1); ?></h6>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Nom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?php $__errorArgs = ["nom.$i"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="nom[]" value="<?php echo e(old("nom.$i")); ?>" required placeholder="Nom de famille">
                                        <?php $__errorArgs = ["nom.$i"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?php $__errorArgs = ["prenom.$i"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="prenom[]" value="<?php echo e(old("prenom.$i")); ?>" required placeholder="Prénom">
                                        <?php $__errorArgs = ["prenom.$i"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control <?php $__errorArgs = ["email.$i"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email[]" value="<?php echo e(old("email.$i")); ?>" required placeholder="exemple@email.com">
                                        <?php $__errorArgs = ["email.$i"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Téléphone</label>
                                        <input type="text" class="form-control <?php $__errorArgs = ["telephone.$i"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="telephone[]" value="<?php echo e(old("telephone.$i")); ?>" placeholder="+225 ...">
                                        <?php $__errorArgs = ["telephone.$i"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 d-flex justify-content-end gap-2">
                <a href="<?php echo e(route('super-admin.partenaires.index')); ?>" class="btn btn-outline-secondary px-5 py-2">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary text-white px-5 py-2 fw-bold shadow-sm">
                    <i class="bi bi-check-circle me-2"></i>Inscrire les Partenaires
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux/resources/views/super-admin/partenaires/create.blade.php ENDPATH**/ ?>