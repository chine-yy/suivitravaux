<?php $__env->startSection('title', 'Nouvelle Sous-Tâche'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<a href="<?php echo e(route('super-admin.sous-taches.index')); ?>" class="text-decoration-none">Sous-Tâches</a>
<span class="cp-breadcrumb-separator">/</span>
<span class="cp-breadcrumb-item">Nouveau</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouvelle Sous-Tâche</h1>
                <p class="cp-page-subtitle">Créez une nouvelle sous-tâche pour une tâche existante</p>
            </div>
            <a href="<?php echo e(route('super-admin.sous-taches.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Sous-Tâches
            </a>
        </div>

        <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-task me-2"></i>Informations de la Sous-Tâche</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.sous-taches.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="row g-4">
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
                            <label class="form-label fw-semibold">Tâche Parente <span class="text-danger">*</span></label>
                            <select name="tache_id" id="tache_id" class="form-select <?php $__errorArgs = ['tache_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Sélectionner une tâche</option>
                                <?php $__currentLoopData = $taches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!$t->user_id): ?>
                                    <option value="<?php echo e($t->id); ?>" data-projet-id="<?php echo e($t->projet_id); ?>" data-phase-id="<?php echo e($t->phase_id); ?>" <?php echo e(old('tache_id') == $t->id ? 'selected' : ''); ?>>
                                        <?php echo e($t->titre ?? 'Tâche #' . $t->id); ?>

                                    </option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['tache_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Phase de la Tâche</label>
                            <div class="input-group">
                                <select name="phase_id" id="phase_id" class="form-select">
                                    <option value="">-- Sélectionner une phase --</option>
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="btn-new-phase" title="Nouvelle Phase">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date de début</label>
                            <input type="date" name="date_debut" class="form-control <?php $__errorArgs = ['date_debut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('date_debut')); ?>">
                            <?php $__errorArgs = ['date_debut'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date fin prévue</label>
                            <input type="date" name="date_fin_prevue" class="form-control <?php $__errorArgs = ['date_fin_prevue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('date_fin_prevue')); ?>">
                            <?php $__errorArgs = ['date_fin_prevue'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Personne assignée</label>
                            <select name="user_id" class="form-select <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="user_id_select">
                                <option value="">-- Sélectionner une personne --</option>
                                <?php $__currentLoopData = $membres; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $membre): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($membre->id); ?>" <?php echo e(old('user_id') == $membre->id ? 'selected' : ''); ?>>
                                        <?php echo e($membre->name); ?> <?php echo e($membre->prenom ? '(' . $membre->prenom . ')' : ''); ?> - <?php echo e($membre->role->nom ?? 'Sans rôle'); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="5"><?php echo e(old('description')); ?></textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 pt-3 border-top mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="<?php echo e(route('super-admin.sous-taches.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvelle Phase -->
<div class="modal fade" id="modalPhase" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Phase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-new-phase">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="projet_id" id="modal_projet_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom de la Phase <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="btn-save-phase">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tacheSelect = document.getElementById('tache_id');
    const phaseSelect = document.getElementById('phase_id');
    const btnNewPhase = document.getElementById('btn-new-phase');
    const modalPhase = new bootstrap.Modal(document.getElementById('modalPhase'));
    const btnSavePhase = document.getElementById('btn-save-phase');

    const projetsData = <?php echo json_encode($projets, 15, 512) ?>;

    function updatePhases(isInitial = false) {
        const selectedTache = tacheSelect.options[tacheSelect.selectedIndex];
        const projetId = selectedTache ? selectedTache.getAttribute('data-projet-id') : null;
        const currentPhaseId = selectedTache ? selectedTache.getAttribute('data-phase-id') : null;

        phaseSelect.innerHTML = '<option value="">-- Sélectionner une phase --</option>';

        if (projetId) {
            const projet = projetsData.find(p => p.id == projetId);
            if (projet && projet.phases) {
                projet.phases.forEach(phase => {
                    const option = document.createElement('option');
                    option.value = phase.id;
                    option.textContent = phase.nom;
                    if (isInitial && currentPhaseId == phase.id) {
                        option.selected = true;
                    }
                    phaseSelect.appendChild(option);
                });
            }
            btnNewPhase.disabled = false;
        } else {
            btnNewPhase.disabled = true;
        }
    }

    tacheSelect.addEventListener('change', () => updatePhases(true));
    updatePhases(true);

    btnNewPhase.addEventListener('click', function() {
        const selectedTache = tacheSelect.options[tacheSelect.selectedIndex];
        const projetId = selectedTache ? selectedTache.getAttribute('data-projet-id') : null;

        if (!projetId) {
            alert('Veuillez sélectionner une tâche parente d\'abord.');
            return;
        }
        document.getElementById('modal_projet_id').value = projetId;
        modalPhase.show();
    });

    btnSavePhase.addEventListener('click', function() {
        const form = document.getElementById('form-new-phase');
        const formData = new FormData(form);

        fetch("<?php echo e(route('super-admin.phases.store')); ?>", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const projet = projetsData.find(p => p.id == formData.get('projet_id'));
                if (projet) {
                    if (!projet.phases) projet.phases = [];
                    projet.phases.push(data.phase);
                }

                const option = document.createElement('option');
                option.value = data.phase.id;
                option.textContent = data.phase.nom;
                option.selected = true;
                phaseSelect.appendChild(option);

                modalPhase.hide();
                form.reset();
            } else {
                alert('Erreur lors de la création de la phase');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue.');
        });
    });


});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/sous-taches/create.blade.php ENDPATH**/ ?>