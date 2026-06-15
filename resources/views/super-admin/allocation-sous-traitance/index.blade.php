@extends('layouts.super-admin')

@section('title', 'Allocation Budget Sous-Traitance - Super Admin')

@section('breadcrumb')
<span class="text-muted">Budget</span> / <span class="text-muted">Allocation Sous-Traitance</span>
@endsection

@section('content')
<div class="cp-allocation-st">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">
                    <i class="bi bi-people me-2"></i>Allocation du Budget par Sous-Traitance
                </h1>
                <p class="cp-page-subtitle">Gestion des budgets alloués aux sous-traitances - Année {{ $currentYear }}</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('allocationSTTable', 'Allocation sous-traitances', 'allocation_sous_traitances_export')">
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
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-people"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $sousTraitances->count() }}</div>
                    <div class="cp-stat-label">Sous-Traitances</div>
                </div>
            </div>
        </div>

        <!-- Allocation by Sous-Traitance -->
        <div class="cp-card mb-4">
            <div class="cp-card-header d-flex justify-content-between align-items-center">
                <h5 class="cp-card-title mb-0">
                    <i class="bi bi-people me-2"></i>Budgets Alloués aux Sous-Traitances ({{ $currentYear }})
                </h5>
            </div>
            <div class="p-3 bg-light border-bottom">
                <form action="{{ route('super-admin.allocation-sous-traitance.index') }}" method="GET" class="row g-2">
                    <div class="col-md-10">
                        <input type="text" name="projet_st" class="form-control form-control-sm"
                            placeholder="Filtrer par projet..." value="{{ request('projet_st') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary w-100">Filtrer</button>
                        <a href="{{ route('super-admin.allocation-sous-traitance.index') }}" class="btn btn-sm btn-outline-secondary"><i
                                class="bi bi-arrow-counterclockwise"></i></a>
                    </div>
                </form>
            </div>

            <div class="cp-card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="allocationSTTable">
                        <thead class="table-light">
                            <tr>
                                <th>Entreprise</th>
                                <th>Projet</th>
                                <th>Montant Alloué</th>
                                <th>Contact</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sousTraitances as $st)
                            <tr>
                                <td><strong>{{ $st->nom_entreprise }}</strong></td>
                                <td>{{ $st->projet?->nom ?? 'N/A' }}</td>
                                <td class="text-success fw-bold">{{ number_format($st->montant_contrat, 0, ',', ' ') }} FCF</td>
                                <td><small><i class="bi bi-envelope me-1"></i>{{ $st->contact_email ?? 'N/A' }}</small></td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('super-admin.sous-traitances.show', $st->id) }}"
                                            class="btn btn-sm btn-outline-info" title="Voir">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="openSTModal('{{ $st->id }}', '{{ $st->nom_entreprise }}', '{{ $st->projet?->nom ?? 'N/A' }}', '{{ $st->montant_contrat }}')"
                                            title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="{{ route('super-admin.export.pdf.direct', ['type' => 'soustraitance', 'id' => $st->id]) }}"
                                            class="btn btn-sm btn-outline-secondary" title="Télécharger">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <form action="{{ route('super-admin.allocation-sous-traitance.destroy', $st->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Réinitialiser le budget de cette sous-traitance ?')"
                                                title="Réinitialiser">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-inbox display-1 text-muted"></i>
                                    <p class="mt-3 text-muted">Aucune sous-traitance trouvée</p>
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

<!-- Modal MAJ Budget Sous-Traitance -->
<div class="modal fade" id="modalUpdateST" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSTTitle">Mettre à jour le Budget</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('super-admin.allocation-sous-traitance.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sous-Traitance</label>
                        <input type="text" id="st_entreprise" class="form-control" readonly>
                        <input type="hidden" name="sous_traitance_id" id="modal_st_id_input">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Montant alloué (FCF) <span class="text-danger">*</span></label>
                        <input type="number" name="montant_contrat" id="modal_st_montant" class="form-control" min="1" step="any"
                            required inputmode="decimal" max="{{ max(0, ($budgetDisponibleAllocation ?? 0)) }}">
                        <div class="form-text" id="modal_st_montant_hint">
                            Montant autorisé: <strong>1</strong> à <strong>{{ number_format(max(0, ($budgetDisponibleAllocation ?? 0)), 0, ',', ' ') }} FCF</strong>.
                        </div>
                    </div>
                    @if($annualBudget)
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Budget restant pour allocation: <strong>{{ number_format($budgetDisponibleAllocation ?? 0, 0, ',', ' ') }} FCF</strong>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const stGlobalDisponible = {{ max(0, (float) ($budgetDisponibleAllocation ?? 0)) }};

function openSTModal(stId, entreprise, projet, currentMontant) {
    document.getElementById('modalSTTitle').textContent = 'Budget: ' + entreprise;
    document.getElementById('st_entreprise').value = entreprise + ' (' + projet + ')';
    document.getElementById('modal_st_id_input').value = stId;
    document.getElementById('modal_st_montant').value = currentMontant;
    
    const input = document.getElementById('modal_st_montant');
    input.max = stGlobalDisponible + parseFloat(currentMontant);
    
    document.getElementById('modal_st_montant_hint').innerHTML = 
        `Montant autorisé: <strong>1</strong> à <strong>${(stGlobalDisponible + parseFloat(currentMontant)).toLocaleString('fr-FR')} FCF</strong>.`;
    
    new bootstrap.Modal(document.getElementById('modalUpdateST')).show();
}

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('modal_st_montant');
    if (input) {
        input.addEventListener('input', function() {
            const value = parseFloat(this.value);
            if (!isNaN(value) && value < 1) this.value = 1;
        });
    }
});
</script>
@endsection