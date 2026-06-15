@extends('layouts.role-dynamique')

@section('title', 'Gestion des Budgets')

@section('breadcrumb')
<span class="text-muted">Budgets</span>
@endsection

@section('content')
<div class="cp-budget">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">Gestion des Budgets</h1>
                <p class="cp-page-subtitle">Suivi détaillé des finances et alertes</p>
            </div>
            <div class="d-flex gap-2">
                @if($annualBudget)
                @if($has('gerer-budgets'))
                <a href="{{ route('role-dynamique.budget.edit', $annualBudget->id) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-2"></i>Modifier Budget ({{ $currentYear }})
                </a>
                @endif
                @endif
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalHistoryBudget">
                    <i class="bi bi-clock-history me-2"></i>Historique des Budgets
                </button>
            </div>
        </div>

        @if(!$annualBudget)
        <div class="alert alert-warning d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-exclamation-triangle fs-4"></i>
            <div>
                <strong>Attention !</strong> Le budget manuel pour {{ $currentYear }} n'a pas encore été défini. Les
                accès aux autres fonctionnalités de cette année sont actuellement restreints.
            </div>
        </div>
        @endif

        <!-- Financial Alerts -->
        @if(!empty($alertes))
        <div class="alert-container mb-4">
            @foreach($alertes as $alerte)
            <div class="alert alert-{{ $alerte['type'] }} d-flex align-items-start gap-3">
                <i class="bi {{ $alerte['icon'] }} fs-4"></i>
                <div>
                    <strong>{{ $alerte['titre'] }}</strong>
                    <p class="mb-0">{{ $alerte['message'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Global Budget Stats -->
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
                <div
                    class="cp-stat-icon {{ ($budgetTotalGlobal > 0 && ($budgetAlloueGlobal / $budgetTotalGlobal) > 0.8) ? 'cp-bg-danger' : 'cp-bg-info' }}">
                    <i class="bi bi-pie-chart"></i>
                </div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $budgetTotalGlobal > 0 ? number_format(($budgetAlloueGlobal /
                        $budgetTotalGlobal) * 100, 2, ',', ' ') : 0 }}%</div>
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
                        <h6 class="cp-chart-title">Répartition du Budget ({{ $currentYear }})</h6>
                    </div>
                    <div class="cp-chart-body" style="height: 45vh;">
                        <canvas id="budgetDistributionChart" data-total="{{ $budgetTotalGlobal ?? 0 }}"
                            data-consomme="{{ $budgetAlloueGlobal ?? 0 }}"
                            data-restant="{{ $budgetRestantGlobal ?? 0 }}"></canvas>
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
                        <canvas id="budgetYearChart" data-budgets='@json($budgetParAnnee)'></canvas>
                    </div>
                </div>
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
                            @forelse($budgetParAnnee as $b)
                            <tr>
                                <td class="ps-4 fw-bold">
                                    {{ $b->annee }}
                                    @if($b->annee == date('Y'))
                                    <span class="badge bg-primary ms-2">Année en cours</span>
                                    @endif
                                </td>
                                <td>{{ number_format($b->total, 0, ',', ' ') }} FCF</td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('role-dynamique.budget.index', ['annee' => $b->annee]) }}"
                                            class="btn btn-sm {{ $currentYear == $b->annee ? 'btn-secondary disabled' : 'btn-outline-primary' }}">
                                            @if($currentYear == $b->annee)
                                            <i class="bi bi-eye-slash"></i> Actuel
                                            @else
                                            <i class="bi bi-eye"></i> Voir
                                            @endif
                                        </a>
                                        @if($has('gerer-budgets'))
                                        <form action="{{ route('role-dynamique.budget.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Supprimer ce budget annuel ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    Aucun historique de budget disponible.
                                </td>
                            </tr>
                            @endforelse
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

@endsection

@push('scripts')
<script src="{{ asset('js/chart.min.js') }}"></script>
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
                        labels: ['Alloué', 'Restant à Allouer'],
                        datasets: [{
                            data: [consomme, Math.max(restant, 0)],
                            backgroundColor: ['#009A44', '#a3e635'],
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
                            backgroundColor: '#009A44',
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
@endpush
