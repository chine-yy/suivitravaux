@php
    $routePrefix = $routePrefix ?? 'super-admin';
    $canManageDepenses = $canManageDepenses ?? true;
    $canDeleteDepenses = $canDeleteDepenses ?? $canManageDepenses;
    $updateTemplate = route($routePrefix . '.budget.depenses.update', ['depense' => '__DEPENSE_ID__']);
@endphp

@include('partials.alerts')

<div class="cp-card mb-4" id="depenses">
    <div class="cp-card-header d-flex justify-content-between align-items-center">
        <h5 class="cp-card-title mb-0">
            <i class="bi bi-receipt me-2"></i>Gestion des Dépenses : choisir facture pour que le projet et montant soient affecté 
        </h5>
        @if($canManageDepenses)
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddDepense">
                <i class="bi bi-plus-lg me-1"></i>Nouvelle dépense
            </button>
        @endif
    </div>

    <div class="p-3 border-bottom d-flex flex-wrap gap-3 align-items-center" style="background:#e8f5e9;">
        <div>
            <span class="text-muted small">Budget total :</span>
            <strong class="ms-1">{{ number_format($budgetTotalGlobal ?? 0, 0, ',', ' ') }} FCF</strong>
        </div>
        <div>
            <span class="text-muted small">Consommé :</span>
            <strong class="ms-1 text-danger">{{ number_format($budgetConsommeGlobal ?? 0, 0, ',', ' ') }} FCF</strong>
        </div>
        <div>
            <span class="text-muted small">Restant :</span>
            <strong class="ms-1 {{ ($budgetRestantGlobal ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
                {{ number_format($budgetRestantGlobal ?? 0, 0, ',', ' ') }} FCF
            </strong>
        </div>
    </div>

    <div class="cp-card-body p-0">
        @if($depensesRecentes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Projet</th>
                            <th>Catégorie</th>
                            <th>Montant</th>
                            <th>Paiement</th>
                            <th>Statut</th>
                            <th>Référence</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($depensesRecentes as $depense)
                            <tr>
                                <td>{{ $depense->date_depense ? $depense->date_depense->format('d/m/Y') : '-' }}</td>
                                <td>{{ $depense->projet->nom ?? 'N/A' }}</td>
                                <td><span class="badge bg-secondary">{{ $depense->getCategorieLabel() }}</span></td>
                                <td class="text-danger fw-bold">- {{ number_format($depense->montant, 0, ',', ' ') }} FCF</td>
                                <td>{{ $depense->getTypePaiementLabel() }}</td>
                                <td>
                                    @php
                                        $statutClass = match($depense->statut) {
                                            'validee' => 'success',
                                            'rejetee' => 'danger',
                                            default => 'warning',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statutClass }}">{{ $depense->getStatutLabel() }}</span>
                                </td>
                                <td><small class="text-muted">{{ $depense->reference ?? '-' }}</small></td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button class="btn btn-sm btn-outline-info"
                                            onclick="openViewDepense({{ $depense->id }}, '{{ addslashes($depense->projet->nom ?? 'N/A') }}', {{ $depense->montant }}, '{{ addslashes(preg_replace('/|/', ' ', $depense->description ?? '')) }}', '{{ $depense->getCategorieLabel() }}', '{{ $depense->date_depense?->format('d/m/Y') }}', '{{ $depense->getTypePaiementLabel() }}', '{{ $depense->reference }}', '{{ $depense->getStatutLabel() }}', '{{ $statutClass }}')"
                                            title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($canManageDepenses)
                                            <button class="btn btn-sm btn-outline-primary"
                                                onclick="openEditDepense({{ $depense->id }}, {{ $depense->projet_id }}, {{ $depense->montant }}, '{{ addslashes($depense->description) }}', '{{ $depense->categorie }}', '{{ $depense->date_depense?->format('Y-m-d') }}', '{{ $depense->type_paiement }}', '{{ $depense->reference }}', '{{ $depense->statut }}')"
                                                title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @endif
                                        @if($canDeleteDepenses)
                                            <form action="{{ route($routePrefix . '.budget.depenses.destroy', $depense) }}" method="POST"
                                                onsubmit="return confirm('Supprimer cette dépense ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $depensesRecentes->links() }}
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-receipt display-4 opacity-25"></i>
                <p class="mt-3">Aucune dépense enregistrée</p>
            </div>
        @endif
    </div>
</div>

@if($canManageDepenses)
<div class="modal fade" id="modalAddDepense" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Nouvelle dépense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route($routePrefix . '.budget.depenses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold fw-bold">Projet <span class="text-danger">*</span></label>
                            <select name="projet_id" class="form-select" required id="add_projet_id" onchange="updateBudgetRestant(this)">
                                <option value="">-- Choisir un projet --</option>
                                @foreach($projets as $p)
                                    <option value="{{ $p->id }}" data-budget="{{ $p->dynamic_budget ?? $p->budget ?? 0 }}" data-consomme="{{ $p->dynamic_consomme ?? 0 }}">
                                        {{ $p->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text" id="restant_info"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fw-bold">Montant (FCF) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="montant" id="add_montant" class="form-control" required min="0.01" placeholder="Ex: 50000" inputmode="decimal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fw-bold">Catégorie <span class="text-danger">*</span></label>
                            <select name="categorie" class="form-select" required>
                                <option value="materiaux">Matériaux</option>
                                <option value="main_oeuvre">Main d'œuvre</option>
                                <option value="equipement">Équipement</option>
                                <option value="transport">Transport</option>
                                <option value="sous_traitance">Sous-traitance</option>
                                <option value="services">Services</option>
                                <option value="autres" selected>Autres</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fw-bold">Date de dépense <span class="text-danger">*</span></label>
                            <input type="date" name="date_depense" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fw-bold">Mode de paiement <span class="text-danger">*</span></label>
                            <select name="type_paiement" class="form-select" required>
                                <option value="virement" selected>Virement bancaire</option>
                                <option value="especes">Espèces</option>
                                <option value="cheque">Chèque</option>
                                <option value="carte_bancaire">Carte bancaire</option>
                                <option value="autres">Autres</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fw-bold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="en_attente" selected>En attente</option>
                                <option value="validee">Validée</option>
                                <option value="rejetee">Rejetée</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fw-bold">N°Facture</label>
                            <select name="reference" class="form-select" onchange="autoFillMontant(this)">
                                <option value="">-- Sélectionner une facture (Optionnel) --</option>
                                @foreach($factures as $f)
                                    <option value="{{ $f->numero_facture }}" data-montant-ttc="{{ $f->montant_ttc }}" data-projet-id="{{ $f->projet_id }}">{{ $f->numero_facture }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Détails de la dépense..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditDepense" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Modifier la dépense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editDepenseForm" method="POST" data-update-template="{{ $updateTemplate }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Projet <span class="text-danger">*</span></label>
                            <select name="projet_id" id="edit_projet_id" class="form-select" required onchange="updateEditBudgetRestant(this)">
                                <option value="">-- Choisir un projet --</option>
                                @foreach($projets as $p)
                                    <option value="{{ $p->id }}" data-budget="{{ $p->dynamic_budget ?? $p->budget ?? 0 }}" data-consomme="{{ $p->dynamic_consomme ?? 0 }}">
                                        {{ $p->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text" id="edit_restant_info"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Montant (FCF) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="montant" id="edit_montant" class="form-control" required min="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Catégorie <span class="text-danger">*</span></label>
                            <select name="categorie" id="edit_categorie" class="form-select" required>
                                <option value="materiaux">Matériaux</option>
                                <option value="main_oeuvre">Main d'œuvre</option>
                                <option value="equipement">Équipement</option>
                                <option value="transport">Transport</option>
                                <option value="sous_traitance">Sous-traitance</option>
                                <option value="services">Services</option>
                                <option value="autres">Autres</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de dépense <span class="text-danger">*</span></label>
                            <input type="date" name="date_depense" id="edit_date_depense" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mode de paiement <span class="text-danger">*</span></label>
                            <select name="type_paiement" id="edit_type_paiement" class="form-select" required>
                                <option value="virement">Virement bancaire</option>
                                <option value="especes">Espèces</option>
                                <option value="cheque">Chèque</option>
                                <option value="carte_bancaire">Carte bancaire</option>
                                <option value="autres">Autres</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Statut</label>
                            <select name="statut" id="edit_statut" class="form-select">
                                <option value="en_attente">En attente</option>
                                <option value="validee">Validée</option>
                                <option value="rejetee">Rejetée</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">N°Facture</label>
                            <select name="reference" id="edit_reference" class="form-select" onchange="autoFillMontant(this)">
                                <option value="">-- Sélectionner une facture (Optionnel) --</option>
                                @foreach($factures as $f)
                                    <option value="{{ $f->numero_facture }}" data-montant-ttc="{{ $f->montant_ttc }}" data-projet-id="{{ $f->projet_id }}">{{ $f->numero_facture }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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

@push('scripts')
<script>
    function autoFillMontant(select) {
        const opt = select.options[select.selectedIndex];
        const montantTtc = parseFloat(opt.dataset.montantTtc) || 0;
        const projetId = opt.dataset.projetId || '';
        const form = select.closest('form');
        const montantInput = form ? form.querySelector('[name="montant"]') : null;
        const projetSelect = form ? form.querySelector('[name="projet_id"]') : null;
        if (montantInput && montantTtc > 0) {
            montantInput.value = montantTtc;
            montantInput.readOnly = true;
        } else if (montantInput) {
            montantInput.readOnly = false;
        }
        if (projetSelect && projetId) {
            projetSelect.value = projetId;
            projetSelect.disabled = true;
            let hidden = form.querySelector('input[name="projet_id"][type="hidden"]');
            if (!hidden) {
                hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'projet_id';
                projetSelect.parentNode.appendChild(hidden);
            }
            hidden.value = projetId;
            if (typeof updateBudgetRestant === 'function') updateBudgetRestant(projetSelect);
            if (typeof updateEditBudgetRestant === 'function') updateEditBudgetRestant(projetSelect);
        } else if (projetSelect) {
            projetSelect.disabled = false;
            const hidden = form.querySelector('input[name="projet_id"][type="hidden"]');
            if (hidden) hidden.remove();
        }
    }

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
        if (!form) return;

        const template = form.dataset.updateTemplate;
        form.action = template.replace('__DEPENSE_ID__', id);

        document.getElementById('edit_projet_id').value = projetId;
        document.getElementById('edit_montant').value = montant;
        document.getElementById('edit_description').value = description || '';
        document.getElementById('edit_categorie').value = categorie;
        document.getElementById('edit_date_depense').value = date;
        document.getElementById('edit_type_paiement').value = typePaiement;
        document.getElementById('edit_reference').value = reference || '';
        document.getElementById('edit_statut').value = statut;

        const montantInput = document.getElementById('edit_montant');
        const projetSelect = document.getElementById('edit_projet_id');
        if (reference) {
            montantInput.readOnly = true;
            projetSelect.disabled = true;
            let hidden = form.querySelector('input[name="projet_id"][type="hidden"]');
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
            const hidden = form.querySelector('input[name="projet_id"][type="hidden"]');
            if (hidden) hidden.remove();
        }

        updateEditBudgetRestant(document.getElementById('edit_projet_id'));
        new bootstrap.Modal(document.getElementById('modalEditDepense')).show();
    }
</script>
@endpush
