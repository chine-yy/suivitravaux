<?php $__env->startSection('title', 'Gestion des Budgets - Super Admin'); ?>

<?php $__env->startSection('breadcrumb'); ?>
<span class="text-muted">Budgets</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="cp-budget">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">Gestion des Budgets</h1>
                <p class="cp-page-subtitle">Suivi détaillé des finances et alertes</p>
            </div>
            <div class="d-flex gap-2">
                <?php if($annualBudget): ?>
                <a href="<?php echo e(route('super-admin.budget.edit', $annualBudget->id)); ?>" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-2"></i>Modifier Budget (<?php echo e($currentYear); ?>)
                </a>
                <?php endif; ?>
                <a href="<?php echo e(route('super-admin.historique.index')); ?>" class="btn btn-outline-info">
                    <i class="bi bi-clock-history me-2"></i>Historique des Budgets
                </a>

            </div>
        </div>


        <?php if(!$annualBudget): ?>
        <div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-exclamation-triangle fs-4"></i>
            <div>
                <strong>Attention !</strong> Le budget manuel pour <?php echo e($currentYear); ?> n'a pas encore été défini. Les
                accès aux autres fonctionnalités de cette année sont actuellement restreints.
            </div>
        </div>
        <?php endif; ?>

        <!-- Financial Alerts -->
        <?php if(!empty($alertes)): ?>
        <div class="alert-container mb-4">
            <?php $__currentLoopData = $alertes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="alert alert-<?php echo e($alerte['type']); ?> d-flex align-items-start gap-3">
                <i class="bi <?php echo e($alerte['icon']); ?> fs-4"></i>
                <div>
                    <strong><?php echo e($alerte['titre']); ?></strong>
                    <p class="mb-0"><?php echo e($alerte['message']); ?></p>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <!-- Global Budget Stats -->
        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-cash-stack"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(number_format($budgetTotalGlobal ?? 0, 0, ',', ' ')); ?> FCF</div>
                    <div class="cp-stat-label">Budget Total</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-diagram-3"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(number_format($budgetAlloueGlobal ?? 0, 0, ',', ' ')); ?> FCF</div>
                    <div class="cp-stat-label">Total Alloué</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-wallet2"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e(number_format($budgetRestantGlobal ?? 0, 0, ',', ' ')); ?> FCF</div>
                    <div class="cp-stat-label">Restant à Allouer</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div
                    class="cp-stat-icon <?php echo e(($budgetTotalGlobal > 0 && ($budgetAlloueGlobal / $budgetTotalGlobal) > 0.8) ? 'cp-bg-danger' : 'cp-bg-info'); ?>">
                    <i class="bi bi-pie-chart"></i>
                </div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value"><?php echo e($budgetTotalGlobal > 0 ? number_format(($budgetAlloueGlobal /
                        $budgetTotalGlobal) * 100, 2, ',', ' ') : 0); ?>%</div>
                    <div class="cp-stat-label">Taux d'allocation</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4">
            <!-- Budget Distribution Chart -->
            <div class="col-12">
                <div class="cp-chart-card" style="min-height: 50vh;">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title">Répartition du Budget (<?php echo e($currentYear); ?>)</h6>
                    </div>
                    <div class="cp-chart-body" style="height: 45vh;">
                        <canvas id="budgetDistributionChart" data-total="<?php echo e($budgetTotalGlobal ?? 0); ?>"
                            data-consomme="<?php echo e($budgetAlloueGlobal ?? 0); ?>"
                            data-restant="<?php echo e($budgetRestantGlobal ?? 0); ?>"></canvas>
                    </div>
                </div>
            </div>

            <!-- Budget by Year -->
            <div class="col-12">
                <div class="cp-chart-card" style="min-height: 50vh;">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title">Budget par Année</h6>
                    </div>
                    <div class="cp-chart-body" style="height: 45vh;">
                        <canvas id="budgetYearChart" data-budgets='<?php echo json_encode($budgetParAnnee, 15, 512) ?>'></canvas>
                    </div>
                </div>
            </div>
        </div>

<div class="modal fade" id="modalAssignBudget" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Allouer un Budget au Projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('super-admin.budget.assign')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="projet_id" id="modal_projet_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sélectionner le Projet</label>
                        <select name="projet_id" class="form-select" id="select_projet"
                            onchange="updateAmountField(this)">
                            <option value="">-- Choisir un projet --</option>
                            <?php $__currentLoopData = $projets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($p->id); ?>" data-budget="<?php echo e($p->dynamic_budget ?? $p->budget ?? 0); ?>"><?php echo e($p->nom); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant Alloué (FCF)</label>
                        <input type="number" step="0.01" name="montant_alloue" id="modal_montant_alloue"
                            class="form-control" required min="0" max="<?php echo e(max(0, ($budgetRestantGlobal ?? 0))); ?>" inputmode="decimal">
                        <div class="form-text" id="modal_montant_alloue_hint">Ce montant sera déduit du budget annuel global.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Assign Budget ST -->
<div class="modal fade" id="modalAssignBudgetST" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSTTitle">Allouer un Budget à la Sous-Traitance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('super-admin.budget.assign-st')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="sous_traitance_id" id="modal_st_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sous-Traitance <span class="text-danger">*</span></label>
                        <select name="sous_traitance_id" class="form-select" id="select_st" onchange="updateSTAmountField(this)">
                            <option value="">-- Choisir une sous-traitance --</option>
                            <?php $__currentLoopData = $sousTraitances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st->id); ?>" data-budget="<?php echo e($st->montant_contrat); ?>"><?php echo e($st->nom_entreprise); ?> (<?php echo e($st->projet?->nom ?? 'N/A'); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant Alloué (FCF)</label>
                        <input type="number" step="0.01" name="montant_contrat" id="modal_st_montant"
                            class="form-control" required min="0" max="<?php echo e(max(0, ($budgetRestantGlobal ?? 0))); ?>" inputmode="decimal">
                        <div class="form-text" id="modal_st_montant_hint">Ce montant sera déduit du budget annuel global et une notification sera
                            envoyée au partenaire.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Voir une dépense -->
<div class="modal fade" id="modalViewDepense" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Détails de la dépense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Projet</span>
                        <span class="fw-bold" id="view_projet"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Montant</span>
                        <span class="fw-bold text-danger" id="view_montant"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Catégorie</span>
                        <span class="badge bg-secondary" id="view_categorie"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Date</span>
                        <span id="view_date"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Paiement</span>
                        <span id="view_paiement"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">Statut</span>
                        <span class="badge" id="view_statut"></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between">
                        <span class="text-muted">N°Facture</span>
                        <span id="view_reference"></span>
                    </div>
                    <div class="list-group-item">
                        <span class="text-muted d-block mb-1">Description</span>
                        <p class="mb-0 text-break" id="view_description"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Historique des Budgets -->
<div class="modal fade" id="modalHistoryBudget" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Historique des Budgets Annuels</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Année</th>
                                <th>Budget Total</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $budgetParAnnee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="ps-4 fw-bold">
                                    <?php echo e($b->annee); ?>

                                    <?php if($b->annee == date('Y')): ?>
                                    <span class="badge bg-primary ms-2">Année en cours</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(number_format($b->total, 0, ',', ' ')); ?> FCF</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="<?php echo e(route('super-admin.budget.index', ['annee' => $b->annee])); ?>"
                                            class="btn btn-sm <?php echo e($currentYear == $b->annee ? 'btn-secondary disabled' : 'btn-outline-primary'); ?>">
                                            <?php if($currentYear == $b->annee): ?>
                                            <i class="bi bi-eye-slash"></i> Actuel
                                            <?php else: ?>
                                            <i class="bi bi-eye"></i> Voir
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    Aucun historique de budget disponible.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
    // ============= Depense CRUD =============
    function updateBudgetRestant(select) {
        const opt = select.options[select.selectedIndex];
        const budget = parseFloat(opt.dataset.budget) || 0;
        const consomme = parseFloat(opt.dataset.consomme) || 0;
        const restant = Math.max(0, budget - consomme);
        const info = document.getElementById('restant_info');
        const montantInput = document.getElementById('add_montant');
        if (!info) return;

        if (budget > 0) {
            const color = restant <= 0 ? 'text-danger' : 'text-success';
            info.innerHTML = `Budget alloué : <strong>${budget.toLocaleString('fr-FR')} FCF</strong> — Restant : <strong class="${color}">${restant.toLocaleString('fr-FR')} FCF</strong> — Montant autorisé : <strong>0</strong> à <strong>${restant.toLocaleString('fr-FR')} FCF</strong>`;
            if (montantInput) {
                montantInput.max = restant;
                const currentValue = parseFloat(montantInput.value || '0');
                if (!isNaN(currentValue) && currentValue > restant) {
                    montantInput.value = restant;
                }
            }
        } else {
            info.innerHTML = '<span class="text-warning">Aucun budget alloué à ce projet</span>';
            montantInput?.removeAttribute('max');
        }
    }

    function updateEditBudgetRestant(select) {
        const opt = select.options[select.selectedIndex];
        const budget = parseFloat(opt.dataset.budget) || 0;
        const consomme = parseFloat(opt.dataset.consomme) || 0;
        const editInput = document.getElementById('edit_montant');
        const currentMontant = parseFloat(editInput?.value || '0') || 0;
        const restant = Math.max(0, budget - consomme);
        const allowedMax = Math.max(0, restant + currentMontant);
        const info = document.getElementById('edit_restant_info');
        if (!info) return;

        if (budget > 0) {
            const color = restant <= 0 ? 'text-danger' : 'text-success';
            info.innerHTML = `Budget alloué : <strong>${budget.toLocaleString('fr-FR')} FCF</strong> — Restant : <strong class="${color}">${restant.toLocaleString('fr-FR')} FCF</strong> — Montant autorisé : <strong>0</strong> à <strong>${allowedMax.toLocaleString('fr-FR')} FCF</strong>`;
            if (editInput) {
                editInput.max = allowedMax;
                const currentValue = parseFloat(editInput.value || '0');
                if (!isNaN(currentValue) && currentValue > allowedMax) {
                    editInput.value = allowedMax;
                }
            }
        } else {
            info.innerHTML = '<span class="text-warning">Aucun budget alloué à ce projet</span>';
            editInput?.removeAttribute('max');
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

        const statutBadge = document.getElementById('view_statut');
        statutBadge.innerText = statut;
        statutBadge.className = 'badge bg-' + statutClass;

        new bootstrap.Modal(document.getElementById('modalViewDepense')).show();
    }

    function openEditDepense(id, projetId, montant, description, categorie, date, typePaiement, reference, statut) {
        const form = document.getElementById('editDepenseForm');
        form.action = `/super-admin/budget/depenses/${id}`;

        document.getElementById('edit_projet_id').value = projetId;
        document.getElementById('edit_montant').value = montant;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_categorie').value = categorie;
        document.getElementById('edit_date_depense').value = date;
        document.getElementById('edit_type_paiement').value = typePaiement;
        document.getElementById('edit_reference').value = reference;
        document.getElementById('edit_statut').value = statut;

        // Trigger restant update for the selected project
        updateEditBudgetRestant(document.getElementById('edit_projet_id'));

        new bootstrap.Modal(document.getElementById('modalEditDepense')).show();
    }
    // ========================================

    function openAssignModal(id, nom, currentBudget) {
        document.getElementById('modalTitle').innerText = 'Modifier le budget : ' + nom;
        document.getElementById('modal_projet_id').value = id;
        document.getElementById('modal_montant_alloue').value = currentBudget;
        document.getElementById('select_projet').value = id;
        updateAmountField(document.getElementById('select_projet'));
        document.getElementById('select_projet').disabled = true;

        var modal = new bootstrap.Modal(document.getElementById('modalAssignBudget'));
        modal.show();
    }

    function openAssignSTModal(id, nom, currentBudget) {
        document.getElementById('modalSTTitle').innerText = 'Modifier le budget : ' + nom;
        document.getElementById('modal_st_id').value = id;
        document.getElementById('modal_st_montant').value = currentBudget;
        document.getElementById('select_st').value = id;
        updateSTAmountField(document.getElementById('select_st'));
        document.getElementById('select_st').disabled = true;

        var modal = new bootstrap.Modal(document.getElementById('modalAssignBudgetST'));
        modal.show();
    }

    // Reset form when clicking "Allouer à un projet" button
    document.querySelector('[data-bs-target="#modalAssignBudget"]').addEventListener('click', function () {
        document.getElementById('modalTitle').innerText = 'Allouer un Budget au Projet';
        document.getElementById('modal_projet_id').value = '';
        document.getElementById('modal_montant_alloue').value = '';
        document.getElementById('select_projet').value = '';
        document.getElementById('select_projet').disabled = false;
        updateAmountField(document.getElementById('select_projet'));
    });

    // Reset form when clicking "Allouer à une sous-traitance" button
    document.querySelector('[data-bs-target="#modalAssignBudgetST"]').addEventListener('click', function () {
        document.getElementById('modalSTTitle').innerText = 'Allouer un Budget à la Sous-Traitance';
        document.getElementById('modal_st_id').value = '';
        document.getElementById('modal_st_montant').value = '';
        document.getElementById('select_st').value = '';
        document.getElementById('select_st').disabled = false;
        updateSTAmountField(document.getElementById('select_st'));
    });

    function updateAmountField(select) {
        const input = document.getElementById('modal_montant_alloue');
        const hint = document.getElementById('modal_montant_alloue_hint');
        const globalMax = <?php echo e(max(0, ($budgetRestantGlobal ?? 0))); ?>;
        if (select.value) {
            const currentAllocation = parseFloat(select.options[select.selectedIndex].getAttribute('data-budget')) || 0;
            const allowedMax = Math.max(0, globalMax + currentAllocation);
            input.max = allowedMax;
            if (!input.value) input.value = allowedMax;
            const value = parseFloat(input.value || '0');
            if (!isNaN(value) && value > allowedMax) input.value = allowedMax;
            document.getElementById('modal_projet_id').value = select.value;
            if (hint) hint.innerHTML = `Montant autorisé: <strong>0</strong> à <strong>${allowedMax.toLocaleString('fr-FR')} FCF</strong>.`;
        } else {
            input.max = globalMax;
            input.value = '';
            if (hint) hint.innerHTML = `Montant autorisé: <strong>0</strong> à <strong>${globalMax.toLocaleString('fr-FR')} FCF</strong>.`;
        }
    }

    function updateSTAmountField(select) {
        const input = document.getElementById('modal_st_montant');
        const hint = document.getElementById('modal_st_montant_hint');
        const globalMax = <?php echo e(max(0, ($budgetRestantGlobal ?? 0))); ?>;
        if (select.value) {
            const currentAllocation = parseFloat(select.options[select.selectedIndex].getAttribute('data-budget')) || 0;
            const allowedMax = Math.max(0, globalMax + currentAllocation);
            input.max = allowedMax;
            if (!input.value) input.value = allowedMax;
            const value = parseFloat(input.value || '0');
            if (!isNaN(value) && value > allowedMax) input.value = allowedMax;
            document.getElementById('modal_st_id').value = select.value;
            if (hint) hint.innerHTML = `Montant autorisé: <strong>0</strong> à <strong>${allowedMax.toLocaleString('fr-FR')} FCF</strong>.`;
        } else {
            input.max = globalMax;
            input.value = '';
            if (hint) hint.innerHTML = `Montant autorisé: <strong>0</strong> à <strong>${globalMax.toLocaleString('fr-FR')} FCF</strong>.`;
        }
    }

    // Add search filter to select (keep this for reference)
    document.getElementById('select_st').addEventListener('change', function() {
        updateSTAmountField(this);
    });

    // Tolère la saisie clavier libre puis borne à la soumission (évite le message "valeurs les plus proches")
    document.addEventListener('DOMContentLoaded', function () {
        const budgetForm = document.querySelector('#modalAssignBudget form');
        const stForm = document.querySelector('#modalAssignBudgetST form');
        const budgetInput = document.getElementById('modal_montant_alloue');
        const stInput = document.getElementById('modal_st_montant');

        if (budgetInput) {
            budgetInput.removeAttribute('step');
            budgetInput.setAttribute('step', 'any');
        }
        if (stInput) {
            stInput.removeAttribute('step');
            stInput.setAttribute('step', 'any');
        }

        function clampInput(input) {
            if (!input) return true;
            const value = parseFloat(input.value || '0');
            const min = parseFloat(input.min || '0');
            const max = parseFloat(input.max || '0');
            if (isNaN(value)) return false;
            if (value < min) {
                input.value = min;
                return true;
            }
            if (value > max) {
                input.value = max;
                return true;
            }
            return true;
        }

        budgetForm?.addEventListener('submit', function (e) {
            if (!clampInput(budgetInput)) e.preventDefault();
        });

        stForm?.addEventListener('submit', function (e) {
            if (!clampInput(stInput)) e.preventDefault();
        });
    });
</script>

</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Budget Distribution Chart
        const distCanvas = document.getElementById('budgetDistributionChart');
        if (distCanvas) {
            const total = parseFloat(distCanvas.dataset.total) || 0;
            const consomme = parseFloat(distCanvas.dataset.consomme) || 0;
            const restant = parseFloat(distCanvas.dataset.restant) || 0;

            if (total > 0) {
                new Chart(distCanvas, {
                    type: 'doughnut',
                    data: {
                        labels: ['Consommé', 'Restant'],
                        datasets: [{
                            data: [consomme, Math.max(restant, 0)],
                            backgroundColor: ['#22c55e', '#a3e635'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            } else {
                distCanvas.parentElement.innerHTML = '<div class="text-center text-muted py-5">Aucun budget enregistré</div>';
            }
        }

        // Budget by Year Chart
        const yearCanvas = document.getElementById('budgetYearChart');
        if (yearCanvas) {
            const budgets = JSON.parse(yearCanvas.dataset.budgets || '[]');
            if (budgets.length) {
                new Chart(yearCanvas, {
                    type: 'bar',
                    data: {
                        labels: budgets.map(b => b.annee),
                        datasets: [{
                            label: 'Budget',
                            data: budgets.map(b => b.total),
                            backgroundColor: '#22c55e',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            } else {
                yearCanvas.parentElement.innerHTML = '<div class="text-center text-muted py-5">Aucune donnée disponible</div>';
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.super-admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/dydy/Documents/base/laravel/suivitravaux/resources/views/super-admin/budget/index.blade.php ENDPATH**/ ?>