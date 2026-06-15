<?php $__env->startSection('title', 'Nouvelle Équipe'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <span class="text-muted">Nouvelle Équipe</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-people me-2"></i>Nouvelle Équipe</h1>
                <p class="cp-page-subtitle">Créer une équipe</p>
            </div>
            <a href="<?php echo e(route('super-admin.equipes.index')); ?>" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-people me-2"></i>Détails de l'équipe</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo e(route('super-admin.equipes.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nom de l'équipe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="nom" value="<?php echo e(old('nom')); ?>" placeholder="Ex: Équipe Alpha..." required>
                            <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Projet <span class="text-danger">*</span></label>
                            <select class="form-select <?php $__errorArgs = ['projet_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="projet_id" required>
                                <option value="">-- Sélectionner --</option>
                                <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($projet->id); ?>" <?php echo e(old('projet_id') == $projet->id ? 'selected' : ''); ?>><?php echo e($projet->nom); ?></option>
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

                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="description" rows="3"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Choisir le chef d'équipe <span class="text-danger">*</span></label>
                            <select name="chef_equipe_id" class="form-select <?php $__errorArgs = ['chef_equipe_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required id="chef_equipe_select">
                                <option value="">Sélectionner un utilisateur...</option>
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>" <?php echo e(old('chef_equipe_id') == $user->id ? 'selected' : ''); ?>>
                                        <?php echo e($user->name); ?> (<?php echo e($user->role->nom ?? 'Sans rôle'); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="form-text text-muted small">Le chef d'équipe sera automatiquement inclus comme membre.</div>
                            <?php $__errorArgs = ['chef_equipe_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Membres</label>
                            <div class="card bg-light border-0">
                                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                                    <div class="row">
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="users[]" value="<?php echo e($user->id); ?>" id="user_<?php echo e($user->id); ?>" <?php echo e(is_array(old('users')) && in_array($user->id, old('users')) ? 'checked' : ''); ?>>
                                                <label class="form-check-label" for="user_<?php echo e($user->id); ?>">
                                                    <?php echo e($user->name); ?> <span class="text-muted small">(<?php echo e($user->role->nom ?? 'Sans rôle'); ?>)</span>
                                                </label>
                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <?php $__errorArgs = ['users'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small mt-2"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="<?php echo e(route('super-admin.equipes.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Créer l'Équipe
                        </button>
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
        const chefSelect = document.getElementById('chef_equipe_select');
        const userCheckboxes = document.querySelectorAll('input[name="users[]"]');

        function updateCheckboxes() {
            const selectedChefId = chefSelect.value;

            userCheckboxes.forEach(checkbox => {
                if (checkbox.value === selectedChefId && selectedChefId !== '') {
                    checkbox.checked = true;
                    // On ne le désactive pas pour qu'il soit bien envoyé dans le POST (ou on utilise readonly)
                    // Pour le visuel sans perturber le POST, on peut empêcher le clic :
                    checkbox.addEventListener('click', preventUncheck);
                    checkbox.parentElement.style.opacity = '0.6';
                    checkbox.parentElement.title = "Le chef d'équipe est membre de facto";
                } else {
                    checkbox.removeEventListener('click', preventUncheck);
                    checkbox.parentElement.style.opacity = '1';
                    checkbox.parentElement.title = "";
                }
            });
        }

        function preventUncheck(e) {
            e.preventDefault();
        }

        chefSelect.addEventListener('change', updateCheckboxes);
        updateCheckboxes();
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/equipes/create.blade.php ENDPATH**/ ?>