<?php $__env->startSection('title', 'Gestion des Dépenses'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Budget</span> / <span class="text-muted">Dépenses</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-depenses">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-receipt me-2"></i>Gestion des Dépenses</h1>
                <p class="cp-page-subtitle">Gestion et suivi des dépenses pour l'année <?php echo e($currentYear); ?> : choisir facture pour que le projet et montant soient affecté </p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('depensesTable', 'Gestion dépenses', 'depenses_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <a href="<?php echo e(route('super-admin.budget.index')); ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-cash-stack me-2"></i>Gestion Budget
                </a>
            </div>
        </div>

        <?php echo $__env->make('partials.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php if(!$annualBudget): ?>
        <div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-exclamation-triangle fs-4"></i>
            <div>
                <strong>Attention !</strong> Le budget annuel pour <?php echo e($currentYear); ?> n'a pas encore été défini.
            </div>
        </div>
        <?php endif; ?>

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-cash-stack"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(number_format($budgetTotalGlobal ?? 0, 0, ',', ' ')); ?> FCF</div>
                    <div class="cp-stat-label">Budget Total</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-credit-card"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(number_format($budgetConsommeGlobal ?? 0, 0, ',', ' ')); ?> FCF</div>
                    <div class="cp-stat-label">Consommé (Dépenses)</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-wallet2"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(number_format($budgetRestantGlobal ?? 0, 0, ',', ' ')); ?> FCF</div>
                    <div class="cp-stat-label">Restant</div>
                </div>
            </div>
        </div>

        <div class="cp-card mb-4">
            <div class="cp-card-header d-flex justify-content-between align-items-center">
                <h5 class="cp-card-title mb-0"><i class="bi bi-list-ul me-2"></i>Dépenses Récentes</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddDepense"> <i class="bi bi-plus-lg me-1"></i>Nouvelle dépense</button>
            </div>
            <div class="cp-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="depensesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Projet</th>
                                <th>Montant</th>
                                <th>Catégorie</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $depensesRecentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($depense->projet->nom ?? 'N/A'); ?></td>
                                <td><?php echo e(number_format($depense->montant, 0, ',', ' ')); ?> FCF</td>
                                <td><?php echo e(ucfirst($depense->categorie)); ?></td>
                                <td><?php echo e($depense->date_depense ? \Carbon\Carbon::parse($depense->date_depense)->format('d/m/Y') : '-'); ?></td>
                                <td><?php echo e(ucfirst($depense->statut)); ?></td>
<td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end align-items-center">
                                        <button class="btn btn-sm btn-outline-info"
                                            onclick="openViewDepense(<?php echo e($depense->id); ?>, '<?php echo e(addslashes($depense->projet->nom ?? 'N/A')); ?>', <?php echo e($depense->montant); ?>, '<?php echo e(addslashes(preg_replace('/|/', ' ', $depense->description ?? ''))); ?>', '<?php echo e($depense->getCategorieLabel()); ?>', '<?php echo e($depense->date_depense?->format('d/m/Y')); ?>', '<?php echo e($depense->getTypePaiementLabel()); ?>', '<?php echo e($depense->reference ?? ''); ?>', '<?php echo e($depense->getStatutLabel()); ?>', 'warning')"
                                            title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="openEditDepense(<?php echo e($depense->id); ?>, <?php echo e($depense->projet_id); ?>, <?php echo e($depense->montant); ?>, '<?php echo e(addslashes($depense->description ?? '')); ?>', '<?php echo e($depense->categorie); ?>', '<?php echo e($depense->date_depense?->format('Y-m-d')); ?>', '<?php echo e($depense->type_paiement); ?>', '<?php echo e($depense->reference ?? ''); ?>', '<?php echo e($depense->statut); ?>')"
                                            title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="<?php echo e(route('super-admin.export.pdf.direct', ['type' => 'depense', 'id' => $depense->id])); ?>"
                                            class="btn btn-sm btn-outline-secondary" title="Télécharger">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form action="<?php echo e(route('super-admin.budget.depenses.destroy', $depense->id)); ?>" method="POST" class="d-inline-flex m-0">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette dépense ?')" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox display-1 text-muted"></i>
                                    <p class="mt-3 text-muted">Aucune dépense trouvée</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="cp-card-footer">
                <?php echo e($depensesRecentes->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<!-- Modal Add Depense -->
<div class="modal fade" id="modalAddDepense" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Dépense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('super-admin.budget.depenses.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet <span class="text-danger">*</span></label>
                            <select name="projet_id" id="add_projet_id" class="form-select" required onchange="updateDepenseBounds(this)">
                                <option value="">Sélectionner un projet</option>
                                <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>" data-budget="<?php echo e($p->dynamic_budget ?? $p->budget ?? 0); ?>" data-consomme="<?php echo e($p->dynamic_consomme ?? 0); ?>">
                                    <?php echo e($p->nom); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Montant (FCF) <span class="text-danger">*</span></label>
                            <input type="number" name="montant" id="add_montant" class="form-control" min="0.01" step="any" inputmode="decimal" required>
                            <div class="form-text" id="add_montant_hint"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie</label>
                            <select name="categorie" class="form-select">
                                <option value="materiaux">Matériaux</option>
                                <option value="main_oeuvre">Main d'oeuvre</option>
                                <option value="equipement">Équipement</option>
                                <option value="transport">Transport</option>
                                <option value="sous_traitance">Sous-traitance</option>
                                <option value="services">Services</option>
                                <option value="autres">Autres</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date Dépense</label>
                            <input type="date" name="date_depense" class="form-control" value="<?php echo e(date('Y-m-d')); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type Paiement</label>
                            <select name="type_paiement" class="form-select">
                                <option value="especes">Espèces</option>
                                <option value="virement">Virement</option>
                                <option value="cheque">Chèque</option>
                                <option value="carte_bancaire">Carte bancaire</option>
                                <option value="autres">Autres</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="en_attente">En attente</option>
                                <option value="validee">Validée</option>
                                <option value="rejetee">Rejetée</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">N°Facture</label>
                            <select name="reference" class="form-select" onchange="autoFillMontant(this)">
                                <option value="">-- Sélectionner une facture (Optionnel) --</option>
                                <?php $__currentLoopData = $factures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($f->numero_facture); ?>" data-montant-ttc="<?php echo e($f->montant_ttc); ?>" data-projet-id="<?php echo e($f->projet_id); ?>"><?php echo e($f->numero_facture); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal View Depense -->
<div class="modal fade" id="modalViewDepense" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Détails de la dépense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Projet</span><span class="fw-bold" id="view_projet"></span></div>
                    <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Montant</span><span class="fw-bold text-danger" id="view_montant"></span></div>
                    <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Catégorie</span><span class="badge bg-secondary" id="view_categorie"></span></div>
                    <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Date</span><span id="view_date"></span></div>
                    <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Paiement</span><span id="view_paiement"></span></div>
                    <div class="list-group-item d-flex justify-content-between"><span class="text-muted">Statut</span><span class="badge" id="view_statut"></span></div>
                    <div class="list-group-item d-flex justify-content-between"><span class="text-muted">N°Facture</span><span id="view_reference"></span></div>
                    <div class="list-group-item"><span class="text-muted d-block mb-1">Description</span><p class="mb-0 text-break" id="view_description"></p></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Depense -->
<div class="modal fade" id="modalEditDepense" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Modifier la dépense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDepenseForm" action="<?php echo e(route('super-admin.budget.depenses.update', ['depense' => '__DEPENSE_ID__'])); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet <span class="text-danger">*</span></label>
                            <select name="projet_id" id="edit_projet_id" class="form-select" required onchange="updateEditBudgetRestant(this)">
                                <option value="">Sélectionner un projet</option>
                                <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>" data-budget="<?php echo e($p->dynamic_budget ?? $p->budget ?? 0); ?>" data-consomme="<?php echo e($p->dynamic_consomme ?? 0); ?>">
                                    <?php echo e($p->nom); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <div class="form-text" id="edit_restant_info"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Montant (FCF) <span class="text-danger">*</span></label>
                            <input type="number" name="montant" id="edit_montant" class="form-control" min="0.01" step="any" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie</label>
                            <select name="categorie" id="edit_categorie" class="form-select">
                                <option value="materiaux">Matériaux</option>
                                <option value="main_oeuvre">Main d'oeuvre</option>
                                <option value="equipement">Équipement</option>
                                <option value="transport">Transport</option>
                                <option value="sous_traitance">Sous-traitance</option>
                                <option value="services">Services</option>
                                <option value="autres">Autres</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date Dépense</label>
                            <input type="date" name="date_depense" id="edit_date_depense" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type Paiement</label>
                            <select name="type_paiement" id="edit_type_paiement" class="form-select">
                                <option value="especes">Espèces</option>
                                <option value="virement">Virement</option>
                                <option value="cheque">Chèque</option>
                                <option value="carte_bancaire">Carte bancaire</option>
                                <option value="autres">Autres</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" id="edit_statut" class="form-select">
                                <option value="en_attente">En attente</option>
                                <option value="validee">Validée</option>
                                <option value="rejetee">Rejetée</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">N°Facture</label>
                            <select name="reference" id="edit_reference" class="form-select" onchange="autoFillMontant(this)">
                                <option value="">-- Sélectionner une facture --</option>
                                <?php $__currentLoopData = $factures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($f->numero_facture); ?>" data-montant-ttc="<?php echo e($f->montant_ttc); ?>" data-projet-id="<?php echo e($f->projet_id); ?>"><?php echo e($f->numero_facture); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function updateDepenseBounds(select) {
    var montantInput = document.getElementById('add_montant');
    var hint = document.getElementById('add_montant_hint');
    if (!select || !montantInput) return;

    var option = select.options[select.selectedIndex];
    var budget = parseFloat(option ? option.getAttribute('data-budget') : '0') || 0;
    var consomme = parseFloat(option ? option.getAttribute('data-consomme') : '0') || 0;
    var restant = Math.max(0, budget - consomme);

    if (budget > 0) {
        montantInput.max = restant;
        if (hint) {
            hint.innerHTML = 'Montant autorisé: <strong>0</strong> à <strong>' + restant.toLocaleString('fr-FR') + ' FCF</strong>.';
        }
    } else {
        montantInput.removeAttribute('max');
        if (hint) {
            hint.innerHTML = '<span class="text-warning">Aucun budget alloué à ce projet.</span>';
        }
    }
}

function updateEditBudgetRestant(select) {
    var opt = select.options[select.selectedIndex];
    var budget = parseFloat(opt ? opt.getAttribute('data-budget') : '0') || 0;
    var consume = parseFloat(opt ? opt.getAttribute('data-consomme') : '0') || 0;
    var editInput = document.getElementById('edit_montant');
    var currentMontant = parseFloat(editInput?.value || '0') || 0;
    var restant = Math.max(0, budget - consume);
    var allowedMax = Math.max(0, restant + currentMontant);
    var info = document.getElementById('edit_restant_info');
    if (!info) return;

    if (budget > 0) {
        var color = restant <= 0 ? 'text-danger' : 'text-success';
        info.innerHTML = 'Budget : <strong>' + budget.toLocaleString('fr-FR') + ' FCF</strong> — Restant : <strong class="' + color + '">' + restant.toLocaleString('fr-FR') + ' FCF</strong>';
        if (editInput) {
            editInput.max = allowedMax;
        }
    } else {
        info.innerHTML = '<span class="text-warning">Aucun budget alloué à ce projet</span>';
    }
}

function openViewDepense(id, projet, montant, description, categorie, date, paiement, reference, statut, statutClass) {
    document.getElementById('view_projet').innerText = projet;
    document.getElementById('view_montant').innerText = montant.toLocaleString('fr-FR') + ' FCF';
    document.getElementById('view_categorie').innerText = categorie;
    document.getElementById('view_date').innerText = date;
    document.getElementById('view_paiement').innerText = paiement;
    document.getElementById('view_description').innerText = description || 'Aucune description';
    document.getElementById('view_reference').innerText = reference || '-';
    document.getElementById('view_statut').innerText = statut;
    document.getElementById('view_statut').className = 'badge bg-' + statutClass;
    new bootstrap.Modal(document.getElementById('modalViewDepense')).show();
}

function autoFillMontant(select) {
    var opt = select.options[select.selectedIndex];
    var montantTtc = parseFloat(opt.getAttribute('data-montant-ttc')) || 0;
    var projetId = opt.getAttribute('data-projet-id') || '';
    var form = select.closest('form');
    var montantInput = form ? form.querySelector('[name="montant"]') : null;
    var projetSelect = form ? form.querySelector('[name="projet_id"]') : null;
    if (montantInput && montantTtc > 0) {
        montantInput.value = montantTtc;
        montantInput.readOnly = true;
    } else if (montantInput) {
        montantInput.readOnly = false;
    }
    if (projetSelect && projetId) {
        projetSelect.value = projetId;
        projetSelect.disabled = true;
        var hidden = form.querySelector('input[name="projet_id"][type="hidden"]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'projet_id';
            projetSelect.parentNode.appendChild(hidden);
        }
        hidden.value = projetId;
        if (typeof updateDepenseBounds === 'function') updateDepenseBounds(projetSelect);
        if (typeof updateEditBudgetRestant === 'function') updateEditBudgetRestant(projetSelect);
    } else if (projetSelect) {
        projetSelect.disabled = false;
        var hidden = form.querySelector('input[name="projet_id"][type="hidden"]');
        if (hidden) hidden.remove();
    }
}

function openEditDepense(id, projetId, montant, description, categorie, date, typePaiement, reference, statut) {
    var form = document.getElementById('editDepenseForm');
    var template = form.getAttribute('action');
    form.action = template.replace('__DEPENSE_ID__', id);

    document.getElementById('edit_projet_id').value = projetId;
    document.getElementById('edit_montant').value = montant;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_categorie').value = categorie;
    document.getElementById('edit_date_depense').value = date;
    document.getElementById('edit_type_paiement').value = typePaiement;
    document.getElementById('edit_reference').value = reference || '';
    document.getElementById('edit_statut').value = statut;

    var montantInput = document.getElementById('edit_montant');
    var projetSelect = document.getElementById('edit_projet_id');
    if (reference) {
        montantInput.readOnly = true;
        projetSelect.disabled = true;
        var hidden = form.querySelector('input[name="projet_id"][type="hidden"]');
        if (!hidden) {
            hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'projet_id';
            projetSelect.parentNode.appendChild(hidden);
        }
        hidden.value = projetId;
    } else {
        montantInput.readOnly = false;
        projetSelect.disabled = false;
        var hidden = form.querySelector('input[name="projet_id"][type="hidden"]');
        if (hidden) hidden.remove();
    }

    updateEditBudgetRestant(document.getElementById('edit_projet_id'));
    new bootstrap.Modal(document.getElementById('modalEditDepense')).show();
}

document.addEventListener('DOMContentLoaded', function () {
    var select = document.getElementById('add_projet_id');
    if (select) {
        updateDepenseBounds(select);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux CNRST/resources/views/super-admin/depenses/index.blade.php ENDPATH**/ ?>