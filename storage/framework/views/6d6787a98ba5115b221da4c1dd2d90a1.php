<?php $__env->startSection('title', 'Nouvelle Sous-Traitance'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.sous-traitances.index')); ?>" class="text-decoration-none">Sous-Traitances</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouvelle Sous-Traitance</h1>
                <p class="cp-page-subtitle">Ajoutez une intervention de sous-traitance à un projet</p>
            </div>
            <a href="<?php echo e(route('super-admin.sous-traitances.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Sous-Traitances
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-building me-2"></i>Informations de la sous-traitance</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.sous-traitances.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Projet <span class="text-danger">*</span></label>
                            <select name="projet_id" class="form-select <?php $__errorArgs = ['projet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Sélectionner un projet</option>
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
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom de l'entreprise <span class="text-danger">*</span></label>
                            <input type="text" name="nom_entreprise" class="form-control <?php $__errorArgs = ['nom_entreprise'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nom_entreprise')); ?>" required>
                            <?php $__errorArgs = ['nom_entreprise'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre d'employés <span class="text-danger">*</span></label>
                            <input type="number" name="nombre_employes" class="form-control <?php $__errorArgs = ['nombre_employes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nombre_employes', 1)); ?>" min="1" required>
                            <?php $__errorArgs = ['nombre_employes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description de la tâche</label>
                            <textarea name="description_tache" class="form-control" rows="3"><?php echo e(old('description_tache')); ?></textarea>
                        </div>

                        <div class="col-md-12 mt-4"><h6 class="fw-semibold mb-3">Contact</h6></div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nom du contact</label>
                            <input type="text" name="contact_nom" class="form-control" value="<?php echo e(old('contact_nom')); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prénom du contact</label>
                            <input type="text" name="contact_prenom" class="form-control" value="<?php echo e(old('contact_prenom')); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="contact_telephone" class="form-control" value="<?php echo e(old('contact_telephone')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="contact_email" class="form-control" value="<?php echo e(old('contact_email')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date début</label>
                            <input type="date" name="date_debut" class="form-control" value="<?php echo e(old('date_debut')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date fin</label>
                            <input type="date" name="date_fin" class="form-control" value="<?php echo e(old('date_fin')); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="en_attente" <?php echo e(old('statut', 'en_attente') == 'en_attente' ? 'selected' : ''); ?>>En attente</option>
                                <option value="en_cours" <?php echo e(old('statut') == 'en_cours' ? 'selected' : ''); ?>>En cours</option>
                                <option value="terminee" <?php echo e(old('statut') == 'terminee' ? 'selected' : ''); ?>>Terminée</option>
                                <option value="annule" <?php echo e(old('statut') == 'annule' ? 'selected' : ''); ?>>Annulé</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes')); ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="<?php echo e(route('super-admin.sous-traitances.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/sous-traitances/create.blade.php ENDPATH**/ ?>