<?php $__env->startSection('title', 'Modifier Contrat'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        #projet_id option {
            color: #000 !important;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.contrats.index')); ?>" class="text-decoration-none">Contrats</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Modifier</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier Contrat</h1>
                <p class="cp-page-subtitle">Mettez à jour les informations du contrat</p>
            </div>
            <a href="<?php echo e(route('super-admin.contrats.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Contrats
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-file-earmark-text me-2"></i>Détails du contrat</h6>
            </div>
            <div class="p-4">
                <form action="<?php echo e(route('super-admin.contrats.update', $contrat->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">N° Contrat <span class="text-danger">*</span></label>
                            <input type="text" name="numero_contrat" class="form-control <?php $__errorArgs = ['numero_contrat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('numero_contrat', $contrat->numero_contrat)); ?>" required>
                            <?php $__errorArgs = ['numero_contrat'];
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
                            <select name="type" class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="prestation" <?php echo e($contrat->type == 'prestation' ? 'selected' : ''); ?>>Prestation</option>
                                <option value="marche" <?php echo e($contrat->type == 'marche' ? 'selected' : ''); ?>>Marché</option>
                                <option value="sous_traitance" <?php echo e($contrat->type == 'sous_traitance' ? 'selected' : ''); ?>>Sous-traitance</option>
                                <option value="autre" <?php echo e($contrat->type == 'autre' ? 'selected' : ''); ?>>Autre</option>
                            </select>
                            <?php $__errorArgs = ['type'];
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
                            <select name="projet_id" id="projet_id" class="form-select" onchange="updateBudgetRestantContrat(this)">
                                <option value="">-- Aucun --</option>
                                <?php $__currentLoopData = $projets ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                    data-budget="<?php echo e($projet->dynamic_budget ?? 0); ?>"
                                    data-consomme="<?php echo e($projet->dynamic_consomme ?? 0); ?>"
                                    <?php echo e($contrat->projet_id == $projet->id ? 'selected' : ''); ?>><?php echo e($projet->nom); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="form-text" id="contrat_budget_info"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Montant (FCFA)</label>
                            <input type="number" step="0.01" name="montant" id="montant" class="form-control" value="<?php echo e(old('montant', $contrat->montant)); ?>">
                            <div id="montant_error" class="text-danger small mt-1" style="display:none;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="brouillon" <?php echo e($contrat->statut == 'brouillon' ? 'selected' : ''); ?>>Brouillon</option>
                                <option value="signe" <?php echo e($contrat->statut == 'signe' ? 'selected' : ''); ?>>Signé</option>
                                <option value="en_cours" <?php echo e($contrat->statut == 'en_cours' ? 'selected' : ''); ?>>En cours</option>
                                <option value="termine" <?php echo e($contrat->statut == 'termine' ? 'selected' : ''); ?>>Terminé</option>
                                <option value="annule" <?php echo e($contrat->statut == 'annule' ? 'selected' : ''); ?>>Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date Début</label>
                            <input type="date" name="date_debut" class="form-control" value="<?php echo e($contrat->date_debut ? date('Y-m-d', strtotime($contrat->date_debut)) : ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date Fin</label>
                            <input type="date" name="date_fin" class="form-control" value="<?php echo e($contrat->date_fin ? date('Y-m-d', strtotime($contrat->date_fin)) : ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Objet</label>
                            <textarea name="objet" class="form-control" rows="3"><?php echo e($contrat->objet); ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Conditions</label>
                            <textarea name="conditions" class="form-control" rows="3"><?php echo e($contrat->conditions); ?></textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="<?php echo e(route('super-admin.contrats.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        let montantRestant = 0;

        function checkMontant() {
            const montantInput = document.getElementById('montant');
            const errorDiv = document.getElementById('montant_error');
            if (!montantInput || !errorDiv) return;
            const valeur = parseFloat(montantInput.value);
            if (valeur > montantRestant && montantRestant >= 0) {
                errorDiv.textContent = 'Le montant ne peut pas dépasser le restant disponible de ' + montantRestant.toLocaleString('fr-FR') + ' FCFA.';
                errorDiv.style.display = 'block';
            } else {
                errorDiv.style.display = 'none';
            }
        }

        function updateBudgetRestantContrat(select) {
            const opt = select.options[select.selectedIndex];
            const budget = parseFloat(opt.dataset.budget) || 0;
            const consomme = parseFloat(opt.dataset.consomme) || 0;
            montantRestant = Math.max(0, budget - consomme);
            const info = document.getElementById('contrat_budget_info');
            const montantInput = document.getElementById('montant');
            if (!info) return;

            if (!opt.value) {
                info.innerHTML = '';
                if (montantInput) montantInput.removeAttribute('max');
                return;
            }

            if (budget > 0) {
                const color = montantRestant <= 0 ? 'text-danger' : 'text-success';
                info.innerHTML = 'Budget alloué : <strong>' + budget.toLocaleString('fr-FR') + ' FCF</strong> — Restant : <strong class="' + color + '">' + montantRestant.toLocaleString('fr-FR') + ' FCF</strong>';
                if (montantInput) {
                    montantInput.max = montantRestant;
                }
            } else {
                info.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Veuillez allouer d\'abord une somme pour le projet "' + opt.text + '"</span>';
                if (montantInput) montantInput.removeAttribute('max');
            }
            checkMontant();
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('montant')?.addEventListener('input', checkMontant);
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/contrats/edit.blade.php ENDPATH**/ ?>