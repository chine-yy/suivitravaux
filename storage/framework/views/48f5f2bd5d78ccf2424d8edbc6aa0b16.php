<?php $__env->startSection('title', 'Nouvelle Facture'); ?>

<?php $__env->startPush('styles'); ?>
<style>
#projet_id option { color: #000 !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <a href="<?php echo e(route('super-admin.factures.index')); ?>" class="text-decoration-none">Factures</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouvelle Facture</h1>
                <p class="cp-page-subtitle">Créez une nouvelle facture</p>
            </div>
            <a href="<?php echo e(route('super-admin.factures.index')); ?>" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Factures
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-receipt me-2"></i>Détails de la facture</h6>
            </div>
            <div class="p-4">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($err); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('super-admin.factures.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">N° Facture</label>
                            <input type="text" name="numero_facture" class="form-control <?php $__errorArgs = ['numero_facture'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('numero_facture')); ?>" placeholder="Laisser vide pour une génération automatique (ex: 0001)">
                            <?php $__errorArgs = ['numero_facture'];
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
                            <select name="type" class="form-select" required>
                                <option value="facture" <?php echo e(old('type') == 'facture' ? 'selected' : ''); ?>>Facture</option>
                                <option value="avoir" <?php echo e(old('type') == 'avoir' ? 'selected' : ''); ?>>Avoir</option>
                                <option value="proforma" <?php echo e(old('type') == 'proforma' ? 'selected' : ''); ?>>Proforma</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="activer_partenaire" onchange="togglePartenaire()">
                                <label class="form-check-label" for="activer_partenaire">Rattacher un partenaire</label>
                            </div>
                            <label class="form-label fw-semibold">Partenaire</label>
                            <select name="partenaire_id" id="partenaire_id" class="form-select" onchange="filterProjetsByPartenaire()" disabled>
                                <option value="">-- Aucun --</option>
                                <?php $__currentLoopData = $partenaires; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partenaire): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($partenaire->id); ?>" <?php echo e(old('partenaire_id') == $partenaire->id ? 'selected' : ''); ?>>
                                    <?php echo e(trim(($partenaire->prenom ?? '') . ' ' . ($partenaire->name ?? '')) ?: ('Partenaire #' . $partenaire->id)); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet</label>
                            <select name="projet_id" id="projet_id" class="form-select" onchange="updateBudgetRestantFacture(this)">
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
                                    data-budget="<?php echo e($projet->dynamic_budget ?? 0); ?>"
                                    data-consomme="<?php echo e($projet->dynamic_consomme ?? 0); ?>"
                                    <?php echo e(old('projet_id') == $projet->id ? 'selected' : ''); ?>><?php echo e($projet->nom); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="form-text" id="facture_budget_info"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Montant HT (FCFA)</label>
                            <input type="number" step="0.01" name="montant_ht" class="form-control" value="<?php echo e(old('montant_ht')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">TVA (FCFA)</label>
                            <input type="number" step="0.01" name="montant_tva" class="form-control" value="<?php echo e(old('montant_tva')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Montant TTC (FCFA)</label>
                            <input type="number" step="0.01" name="montant_ttc" id="montant_ttc"
                                class="form-control <?php $__errorArgs = ['montant_ttc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                value="<?php echo e(old('montant_ttc')); ?>" min="0" inputmode="decimal">
                            <?php $__errorArgs = ['montant_ttc'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date Émission</label>
                            <input type="date" name="date_emission" class="form-control" value="<?php echo e(old('date_emission')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Date Échéance</label>
                            <input type="date" name="date_echeance" class="form-control" value="<?php echo e(old('date_echeance')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Statut Paiement</label>
                            <select name="statut_paiement" class="form-select">
                                <option value="en_attente" <?php echo e(old('statut_paiement', 'en_attente') == 'en_attente' ? 'selected' : ''); ?>>En attente</option>
                                <option value="paye" <?php echo e(old('statut_paiement') == 'paye' ? 'selected' : ''); ?>>Payé</option>
                                <option value="en_retard" <?php echo e(old('statut_paiement') == 'en_retard' ? 'selected' : ''); ?>>En retard</option>
                                <option value="annule" <?php echo e(old('statut_paiement') == 'annule' ? 'selected' : ''); ?>>Annulé</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Mode Paiement</label>
                            <select name="mode_paiement" class="form-select">
                                <option value="">-- Sélectionner --</option>
                                <option value="virement" <?php echo e(old('mode_paiement') == 'virement' ? 'selected' : ''); ?>>Virement</option>
                                <option value="cheque" <?php echo e(old('mode_paiement') == 'cheque' ? 'selected' : ''); ?>>Chèque</option>
                                <option value="especes" <?php echo e(old('mode_paiement') == 'especes' ? 'selected' : ''); ?>>Espèces</option>
                                <option value="carte" <?php echo e(old('mode_paiement') == 'carte' ? 'selected' : ''); ?>>Carte bancaire</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"><?php echo e(old('notes')); ?></textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="<?php echo e(route('super-admin.factures.index')); ?>" class="btn btn-outline-secondary px-4">Annuler</a>
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
            match = true; // Show all if no partenaire selected
        } else {
            try {
                const partenaireIds = JSON.parse(opt.dataset.partenaireIds || '[]');
                match = partenaireIds.includes(parseInt(partenaireId));
            } catch (e) {
                console.error('Error parsing partenaire IDs:', e);
                match = false;
            }
        }

        opt.style.display = match ? '' : 'none';
        if (match && !firstVisible) firstVisible = opt;
    });

    if (autoSelect && firstVisible) {
        projetSelect.value = firstVisible.value;
    }
    updateBudgetRestantFacture(projetSelect);
}

function updateBudgetRestantFacture(select) {
    const opt = select.options[select.selectedIndex];
    const budget = parseFloat(opt.dataset.budget) || 0;
    const consomme = parseFloat(opt.dataset.consomme) || 0;
    const restant = Math.max(0, budget - consomme);
    const info = document.getElementById('facture_budget_info');
    const montantInput = document.getElementById('montant_ttc');
    if (!info) return;

    if (!opt.value) {
        info.innerHTML = '';
        if (montantInput) montantInput.removeAttribute('max');
        return;
    }

    if (budget > 0) {
        const color = restant <= 0 ? 'text-danger' : 'text-success';
        info.innerHTML = 'Budget alloué : <strong>' + budget.toLocaleString('fr-FR') + ' FCF</strong> — Restant : <strong class="' + color + '">' + restant.toLocaleString('fr-FR') + ' FCF</strong>';
        if (montantInput) {
            montantInput.max = restant;
            const currentValue = parseFloat(montantInput.value || '0');
            if (!isNaN(currentValue) && currentValue > restant) {
                montantInput.value = restant;
            }
        }
    } else {
        info.innerHTML = '<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Veuillez allouer d\'abord une somme pour le projet "' + opt.text + '"</span>';
        if (montantInput) montantInput.removeAttribute('max');
    }
}

function togglePartenaire() {
    const cb = document.getElementById('activer_partenaire');
    const sel = document.getElementById('partenaire_id');
    sel.disabled = !cb.checked;
    if (!cb.checked) {
        sel.value = '';
    }
    filterProjetsByPartenaire();
}

document.getElementById('partenaire_id').addEventListener('change', function () {
    filterProjetsByPartenaire(true);
});

document.addEventListener('DOMContentLoaded', function () {
    filterProjetsByPartenaire(false);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/factures/create.blade.php ENDPATH**/ ?>