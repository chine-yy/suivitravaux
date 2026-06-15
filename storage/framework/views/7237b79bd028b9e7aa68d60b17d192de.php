<?php $__env->startSection('title', 'Modifier l\'Équipe'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.equipes.index')); ?>" class="text-decoration-none">Équipes</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Modifier</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier l'Équipe</h1>
                <p class="cp-page-subtitle">Mettez à jour les informations et les membres de l'équipe</p>
            </div>
            <a href="<?php echo e(route('super-admin.equipes.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-people me-2"></i>Informations de l'Équipe</h6>
                    </div>
                    <div class="p-4">
                        <form action="<?php echo e(route('super-admin.equipes.update', $equipe->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Nom de l'équipe <span class="text-danger">*</span></label>
                                <input type="text" name="nom" class="form-control <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('nom', $equipe->nom)); ?>" required>
                                <?php $__errorArgs = ['nom'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Projet associé <span class="text-danger">*</span></label>
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
                                        <option value="<?php echo e($projet->id); ?>" <?php echo e(old('projet_id', $equipe->projet_id) == $projet->id ? 'selected' : ''); ?>>
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

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('description', $equipe->description)); ?></textarea>
                                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Statut</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="statut" id="statut_active" value="active" <?php echo e(old('statut', $equipe->statut) == 'active' ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="statut_active">Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="statut" id="statut_inactive" value="inactive" <?php echo e(old('statut', $equipe->statut) == 'inactive' ? 'checked' : ''); ?>>
                                        <label class="form-check-label" for="statut_inactive">Inactive</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
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
                                        <option value="<?php echo e($user->id); ?>" <?php echo e(old('chef_equipe_id', $equipe->chef_equipe_id) == $user->id ? 'selected' : ''); ?>>
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

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Membres de l'équipe <span class="text-danger">*</span></label>
                                <div class="border rounded p-3">
                                    <div class="row">
                                        <?php $selectedUsers = old('users', $equipe->users->pluck('id')->toArray()); ?>
                                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="users[]" value="<?php echo e($user->id); ?>" id="user_<?php echo e($user->id); ?>" <?php echo e(in_array($user->id, $selectedUsers) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="user_<?php echo e($user->id); ?>">
                                                        <?php echo e($user->name); ?> <span class="text-muted small">(<?php echo e($user->role->nom ?? 'Sans rôle'); ?>)</span>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

                            <div class="d-flex gap-2 pt-2 border-top">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check2 me-2"></i>Enregistrer
                                </button>
                                <a href="<?php echo e(route('super-admin.equipes.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Détails actuels</h6>
                    </div>
                    <div class="p-4">
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted">Créée le :</td>
                                <td><?php echo e($equipe->created_at->format('d/m/Y')); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Membres :</td>
                                <td><?php echo e($equipe->users->count()); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
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

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/equipes/edit.blade.php ENDPATH**/ ?>