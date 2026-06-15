@extends('layouts.super-admin')

@section('title', 'Allocation Budget par Projet - Super Admin')

@section('breadcrumb')
<span class="text-muted">Budget</span> / <span class="text-muted">Allocation Projet</span>
@endsection

@section('content')
<div class="cp-allocation-projet">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">
                    <i class="bi bi-diagram-3 me-2"></i>Allocation du Budget par Projet
                </h1>
                <p class="cp-page-subtitle">Gestion des budgets alloués aux projets - Année {{ $currentYear }}</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('allocationProjetTable', 'Allocation projets', 'allocation_projets_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
                <a href="{{ route('super-admin.budget.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-cash-stack me-2"></i>Gestion Budget
                </a>
            </div>
        </div>

        @include('partials.alerts')

        @if(!$annualBudget)
        <div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-exclamation-triangle fs-4"></i>
            <div>
                <strong>Attention !</strong> Le budget annuel pour {{ $currentYear }} n'a pas encore été défini.
            </div>
        </div>
        @endif

        <!-- Stats Row -->
        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-cash-stack"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ number_format($budgetTotalGlobal ?? 0, 0, ',', ' ') }} FCF</div>
                    <div class="cp-stat-label">Budget Total</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-diagram-3"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ number_format($budgetAlloueGlobal ?? 0, 0, ',', ' ') }} FCF</div>
                    <div class="cp-stat-label">Total Alloué</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-wallet2"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ number_format($budgetRestantGlobal ?? 0, 0, ',', ' ') }} FCF</div>
                    <div class="cp-stat-label">Restant à Allouer</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-briefcase"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $projets->count() }}</div>
                    <div class="cp-stat-label">Projets</div>
                </div>
            </div>
        </div>

        <!-- Allocation by Project -->
        <div class="cp-card mb-4">
            <div class="cp-card-header d-flex justify-content-between align-items-center">
                <h5 class="cp-card-title mb-0">
                    <i class="bi bi-briefcase me-2"></i>Budgets Alloués aux Projets ({{ $currentYear }})
                </h5>
            </div>
            <div class="p-3 bg-light border-bottom">
                <form action="{{ route('super-admin.allocation-projet.index') }}" method="GET" class="row g-2">
                    <div class="col-md-10">
                        <input type="text" name="nom" class="form-control form-control-sm"
                            placeholder="Filtrer par nom de projet..." value="{{ request('nom') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary w-100">Filtrer</button>
                        <a href="{{ route('super-admin.allocation-projet.index') }}" class="btn btn-sm btn-outline-secondary"><i
                                class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </form>
            </div>

            <div class="cp-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="allocationProjetTable">
                        <thead class="table-light">
                            <tr>
                                <th>Projet</th>
                                <th>Budget Alloué</th>
                                <th>Consommé</th>
                                <th>Restant</th>
                                <th>Consommation</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projets as $projet)
                            @php
                            $budget = $projet->dynamic_budget ?? 0;
                            $consomme = $projet->dynamic_consomme ?? 0;
                            $restant = $budget - $consomme;
                            $pourcentage = $budget > 0 ? round(($consomme / $budget) * 100) : 0;
                            $statutBudget = $budget > 0 ? ($consomme > $budget ? 'danger' : ($pourcentage > 80 ?
                            'warning' : 'success')) : 'secondary';
                            @endphp
                            <tr>
                                <td><strong>{{ $projet->nom }}</strong></td>
                                <td>{{ number_format($budget, 0, ',', ' ') }} FCF</td>
                                <td>{{ number_format($consomme, 0, ',', ' ') }} FCF</td>
                                <td class="{{ $restant < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($restant, 0, ',', ' ') }} FCF
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $statutBudget }}"
                                                style="width: {{ min($pourcentage, 100) }}%"></div>
                                        </div>
                                        <span class="small">{{ $pourcentage }}%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('super-admin.projets.show', $projet->id) }}"
                                            class="btn btn-sm btn-outline-info" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="openAssignModal('{{ $projet->id }}', '{{ $projet->nom }}', '{{ $budget }}')"
                                            title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @include('partials.row-export', ['id' => $projet->id, 'prefix' => 'projet', 'title' => 'Projet', 'project_name_only' => true])
                                        <form action="{{ route('super-admin.allocation-projet.destroy', $projet->id) }}" method="POST" onsubmit="return confirm('Réinitialiser le budget de ce projet ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Réinitialiser">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox display-1 text-muted"></i>
                                    <p class="mt-3 text-muted">Aucun projet trouvé</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Assign Budget -->
<div class="modal fade" id="modalAssignBudget" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Allouer un Budget au Projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('super-admin.allocation-projet.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Projet <span class="text-danger">*</span></label>
                        <select name="projet_id" id="modal_projet_id" class="form-select" required onchange="updateAllocationBounds()">
                            <option value="">Sélectionner un projet</option>
                            @foreach($projets as $projet)
                            <option value="{{ $projet->id }}" data-current-budget="{{ $projet->dynamic_budget ?? 0 }}">{{ $projet->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant alloué (FCF) <span class="text-danger">*</span></label>
                        <input type="number" name="montant_alloue" id="modal_montant" class="form-control" min="1" step="any"
                            required inputmode="decimal" @if($annualBudget) max="{{ max(0, ($budgetDisponibleAllocation ?? 0)) }}" @endif>
                        <div class="form-text" id="modal_montant_hint">
                            @if($annualBudget)
                                Montant autorisé: <strong>1</strong> à <strong>{{ number_format(max(0, ($budgetDisponibleAllocation ?? 0)), 0, ',', ' ') }} FCF</strong>.
                            @else
                                Définissez d'abord le budget annuel pour pouvoir allouer un montant.
                            @endif
                        </div>
                    </div>
                    @if($annualBudget)
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Budget disponible: <strong>{{ number_format($budgetDisponibleAllocation ?? 0, 0, ',', ' ') }} FCF</strong>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Allouer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const allocationGlobalDisponible = {{ max(0, (float) ($budgetDisponibleAllocation ?? 0)) }};

function updateAllocationBounds() {
    const select = document.getElementById('modal_projet_id');
    const input = document.getElementById('modal_montant');
    const hint = document.getElementById('modal_montant_hint');
    if (!select || !input) return;

    const option = select.options[select.selectedIndex];
    const currentBudget = parseFloat(option?.dataset?.currentBudget || '0') || 0;
    const allowedMax = Math.max(0, allocationGlobalDisponible + currentBudget);

    input.max = allowedMax;
    if (hint) {
        hint.innerHTML = `Montant autorisé: <strong>1</strong> à <strong>${allowedMax.toLocaleString('fr-FR')} FCF</strong>.`;
    }

    const value = parseFloat(input.value || '0');
    if (!isNaN(value) && (value < 1 || value > allowedMax)) {
        input.value = Math.min(Math.max(value, 1), allowedMax);
    }
}

function openAssignModal(projetId, projetNom, currentBudget) {
    const select = document.getElementById('modal_projet_id');
    const input = document.getElementById('modal_montant');

    select.value = projetId;
    updateAllocationBounds();
    input.value = currentBudget || '';

    document.getElementById('modalTitle').textContent = 'Modifier le Budget pour: ' + projetNom;
    new bootstrap.Modal(document.getElementById('modalAssignBudget')).show();
}

document.addEventListener('DOMContentLoaded', function () {
    updateAllocationBounds();
});
</script>
@endsection
