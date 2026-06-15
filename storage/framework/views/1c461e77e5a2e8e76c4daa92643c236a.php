<?php $__env->startSection('title', 'Nouveau Rapport'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="text-muted"><a href="<?php echo e(route('role-dynamique.rapports.index')); ?>">Rapports</a></span> / 
    <span class="text-muted">Nouveau Rapport</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-content">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">Envoyer un Rapport</h1>
            <p class="cp-page-subtitle">Rédigez et soumettez un nouveau rapport pour vos projets.</p>
        </div>
        <div>
            <a href="<?php echo e(route('role-dynamique.rapports.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="cp-card">
        <div class="cp-card-header">
            <h5 class="mb-0">Formulaire de rapport</h5>
        </div>
        <div class="cp-card-body">
            <form action="<?php echo e(route('role-dynamique.rapports.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="projet_id" class="form-label">Projet concerné <span class="text-danger">*</span></label>
                        <select name="projet_id" id="projet_id" class="form-select <?php $__errorArgs = ['projet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Sélectionnez un projet...</option>
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
                        <label for="type" class="form-label">Type de Rapport <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="journalier" <?php echo e(old('type') == 'journalier' ? 'selected' : ''); ?>>Journalier</option>
                            <option value="hebdomadaire" <?php echo e(old('type') == 'hebdomadaire' ? 'selected' : ''); ?>>Hebdomadaire</option>
                            <option value="mensuel" <?php echo e(old('type') == 'mensuel' ? 'selected' : ''); ?>>Mensuel</option>
                            <option value="incident" <?php echo e(old('type') == 'incident' ? 'selected' : ''); ?>>Incident</option>
                            <option value="fin_tache" <?php echo e(old('type') == 'fin_tache' ? 'selected' : ''); ?>>Fin de Tâche</option>
                            <option value="sous_tache" <?php echo e(old('type') == 'sous_tache' ? 'selected' : ''); ?>>Sous Tâche</option>
                        </select>
                        <?php $__errorArgs = ['type'];
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
                        <label for="titre" class="form-label">Titre du Rapport <span class="text-danger">*</span></label>
                        <input type="text" name="titre" id="titre" class="form-control <?php $__errorArgs = ['titre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('titre')); ?>" required placeholder="Ex: Avancement de la phase X">
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
                        <label for="contenu" class="form-label">Contenu Détaillé</label>
                        <textarea name="contenu" id="contenu" rows="6" class="form-control <?php $__errorArgs = ['contenu'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Décrivez l'activité, les problèmes rencontrés, etc."><?php echo e(old('contenu')); ?></textarea>
                        <?php $__errorArgs = ['contenu'];
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

                    <div class="col-12 d-flex justify-content-end align-items-center gap-2 mt-4">
                        <!-- Pour les rôles dynamiques classiques, le statut de création initial est généralement "soumis" pour attente de validation -->
                        <input type="hidden" name="statut" value="soumis">
                        
                        <button class="btn btn-outline-secondary" type="submit" name="statut" value="brouillon" onclick="this.form.statut.value='brouillon'">
                            <i class="bi bi-save me-2"></i>Brouillon
                        </button>
                        <button type="submit" class="btn btn-green">
                            <i class="bi bi-send-fill me-2"></i>Envoyer le rapport
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.role-dynamique', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/role-dynamique/rapports/create.blade.php ENDPATH**/ ?>