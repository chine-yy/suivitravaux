@extends('layouts.super-admin')

@section('title', 'Rapports et Analytique - Super Admin')

@section('breadcrumb')
<span class="text-muted">Rapports</span>
@endsection

@section('content')
<div class="cp-rapports">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">Rapports</h1>
                <p class="cp-page-subtitle">Vue d'ensemble et statistiques des rapports et le bouton envoyer permet l'envoi du rapport au partenaire(s) rattacher au projet</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-danger" onclick="exportToPdf('rapportsTable', 'Liste des rapports', 'rapports_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter tout
                </button>
            </div>
        </div>


        <!-- Statistics Cards -->
        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-file-earmark-text"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $rapports->total() }}</div>
                    <div class="cp-stat-label">Total Rapports</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-hourglass-split"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $rapportsParStatut['en_attente'] }}</div>
                    <div class="cp-stat-label">En attente</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $rapportsParStatut['valide'] }}</div>
                    <div class="cp-stat-label">Validés</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-danger"><i class="bi bi-x-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $rapportsParStatut['rejete'] }}</div>
                    <div class="cp-stat-label">Rejetés</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <!-- Monthly chart -->
            <div class="col-xl-8 mb-4">
                <div class="cp-chart-card h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-bar-chart me-2"></i>Rapports par mois</h6>
                    </div>
                    <div class="cp-chart-body">
                        <canvas id="rapportsParMoisChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <!-- Status chart -->
            <div class="col-xl-4 mb-4">
                <div class="cp-chart-card h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-pie-chart me-2"></i>Statuts des rapports</h6>
                    </div>
                    <div class="cp-chart-body">
                        <canvas id="rapportsParStatutChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-funnel me-2"></i>Filtres</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.rapports.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Projet</label>
                        <select name="projet_id" class="form-select form-select-sm">
                            <option value="">Tous les projets</option>
                            @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ request('projet_id') == $projet->id ? 'selected' : '' }}>{{ $projet->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Statut</label>
                        <select name="statut" class="form-select form-select-sm">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                            <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">Tous les types</option>
                            <option value="journalier" {{ request('type') == 'journalier' ? 'selected' : '' }}>Journalier</option>
                            <option value="hebdomadaire" {{ request('type') == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="mensuel" {{ request('type') == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                            <option value="incident" {{ request('type') == 'incident' ? 'selected' : '' }}>Incident</option>
                            <option value="fin_tache" {{ request('type') == 'fin_tache' ? 'selected' : '' }}>Fin de Tâche</option>
                            <option value="sous_tache" {{ request('type') == 'sous_tache' ? 'selected' : '' }}>Sous-tâche</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Titre</label>
                        <input type="text" name="titre" class="form-control form-control-sm" placeholder="Rechercher..." value="{{ request('titre') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('super-admin.rapports.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                            <i class="bi bi-x-circle me-1"></i> Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Rapports Table -->
        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-table me-2"></i>Liste des rapports</h6>
            </div>
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle" id="rapportsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Titre</th>
                            <th>Projet</th>
                            <th>Auteur</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rapports as $rapport)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $rapport->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $rapport->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $rapport->titre }}</div>
                                <small class="text-muted">{{ Str::limit($rapport->contenu, 30) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $rapport->projet->nom ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2">{{ strtoupper(substr($rapport->auteur->name ?? 'U', 0, 1)) }}</div>
                                    {{ $rapport->auteur->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $rapport->type)) }}</span>
                            </td>
                            <td>
                                @if($rapport->statut === 'valide')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Validé</span>
                                @elseif($rapport->statut === 'rejete')
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejeté</span>
                                @else
                                <span class="badge bg-warning"><i class="bi bi-hourglass-split me-1"></i>En attente</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-info" title="Voir" data-bs-toggle="modal" data-bs-target="#viewRapportModal{{ $rapport->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-warning" title="Modifier le statut" data-bs-toggle="modal" data-bs-target="#statusOnlyModal{{ $rapport->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    @include('partials.row-export', ['id' => $rapport->id, 'prefix' => 'rapport', 'title' => 'Rapport - ' . ($rapport->titre ?? $rapport->id)])
                                    @if(!$rapport->est_envoye)
                                    <form action="{{ route('super-admin.rapports.envoyer-partenaire', $rapport->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" title="Envoyer au partenaire" onclick="return confirm('Envoyer ce rapport au partenaire ?')">
                                            <i class="bi bi-send"></i>
                                        </button>
                                    </form>
                                    @else
                                    <span class="btn btn-outline-secondary btn-sm" title="Déjà envoyé au partenaire">
                                        <i class="bi bi-send-check-fill text-success"></i>
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Aucun rapport trouvé
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @foreach($rapports as $rapport)
            @php
                $currentStatut = $rapport->statut;
                $approuveStatuts = ['valide', 'approuve'];
                $soumisStatuts = ['soumis', 'en_revision', 'en_revue'];
            @endphp

            <div class="modal fade js-rapport-modal" id="statusOnlyModal{{ $rapport->id }}" tabindex="-1" aria-labelledby="statusOnlyModalLabel{{ $rapport->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-md">
                    <div class="modal-content">
                        <form action="{{ route('super-admin.rapports.update-statut', $rapport->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header py-3">
                                <h5 class="modal-title fw-semibold" id="statusOnlyModalLabel{{ $rapport->id }}">
                                    Modifier le statut du rapport #{{ $rapport->id }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                            </div>
                            <div class="modal-body py-3">
                                <p class="text-muted mb-3">Vous pouvez modifier uniquement le statut de ce rapport.</p>
                                <label for="statut_{{ $rapport->id }}" class="form-label fw-semibold">Statut du rapport <span class="text-danger">*</span></label>
                                <select name="statut" id="statut_{{ $rapport->id }}" class="form-select" required>
                                    <option value="soumis" {{ in_array($currentStatut, $soumisStatuts) ? 'selected' : '' }}>Soumis / En révision</option>
                                    <option value="valide" {{ in_array($currentStatut, $approuveStatuts) ? 'selected' : '' }}>Validé</option>
                                    <option value="rejete" {{ $currentStatut == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                    <option value="brouillon" {{ $currentStatut == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                </select>
                            </div>
                            <div class="modal-footer py-3 d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-green px-4">
                                    <i class="bi bi-check-circle me-2"></i>Enregistrer le statut
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade js-rapport-modal" id="viewRapportModal{{ $rapport->id }}" tabindex="-1" aria-labelledby="viewRapportModalLabel{{ $rapport->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                    <div class="modal-content">
                        <div class="modal-header py-3">
                            <h5 class="modal-title fw-semibold" id="viewRapportModalLabel{{ $rapport->id }}">
                                <i class="bi bi-file-earmark-text me-2"></i>{{ $rapport->titre }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body py-3">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light h-100">
                                        <h6 class="fw-bold mb-3"><i class="bi bi-file-earmark-text me-2"></i>Détails du rapport</h6>
                                        <p class="mb-2"><strong>Titre :</strong> {{ $rapport->titre ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>Projet :</strong> {{ $rapport->projet->nom ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>Type :</strong> {{ ucfirst(str_replace('_', ' ', $rapport->type ?? 'N/A')) }}</p>
                                        <p class="mb-2"><strong>Date de création :</strong> {{ $rapport->created_at ? $rapport->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                                        <p class="mb-0"><strong>Heure d'envoi :</strong> {{ $rapport->date_envoi ? \Carbon\Carbon::parse($rapport->date_envoi)->format('d/m/Y H:i') : ($rapport->created_at ? $rapport->created_at->format('d/m/Y H:i') : 'N/A') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded h-100">
                                        <h6 class="fw-bold mb-3"><i class="bi bi-person-badge me-2"></i>Détails de l'utilisateur</h6>
                                        <p class="mb-2"><strong>Prénom :</strong> {{ $rapport->auteur->prenom ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>Nom :</strong> {{ $rapport->auteur->name ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>Rôle :</strong> {{ $rapport->auteur->role->nom ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>Numéro :</strong> {{ $rapport->auteur->phone ?? $rapport->auteur->telephone ?? 'N/A' }}</p>
                                        <p class="mb-0"><strong>Email :</strong> {{ $rapport->auteur->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Statut :</strong>
                                @if($rapport->statut === 'valide')
                                <span class="badge bg-success ms-2">Validé</span>
                                @elseif($rapport->statut === 'rejete')
                                <span class="badge bg-danger ms-2">Rejeté</span>
                                @else
                                <span class="badge bg-warning ms-2">En attente</span>
                                @endif
                            </div>

                            @if($rapport->contenu)
                            <div>
                                <strong>Contenu :</strong>
                                <div class="bg-light p-3 rounded mt-2 border">{{ $rapport->contenu }}</div>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer py-3 d-flex justify-content-between flex-wrap gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                            <a href="{{ route('super-admin.export.pdf.direct', ['type' => 'rapport', 'id' => $rapport->id]) }}" class="btn btn-primary">
                                <i class="bi bi-download me-2"></i>Télécharger PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Pagination -->
            @if($rapports->hasPages())
            <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center p-3">
                <div class="text-muted">
                    Affichage de {{ $rapports->firstItem() }} à {{ $rapports->lastItem() }} sur {{ $rapports->total() }} résultats
                </div>
                <div>
                    {{ $rapports->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
    // Ensure report modals are attached to <body> so they overlay the full viewport.
    document.addEventListener('show.bs.modal', function (event) {
        const modal = event.target;
        if (!modal.classList || !modal.classList.contains('js-rapport-modal')) {
            return;
        }
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    // Monthly chart
    const rapportsParMois = @json($rapportsParMois);
    const labels = rapportsParMois.map(r => `Mois ${r.mois}/${r.annee}`);
    const data = rapportsParMois.map(r => r.total);

    new Chart(document.getElementById('rapportsParMoisChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Rapports',
                data: data,
                borderColor: '#fd7e14',
                backgroundColor: 'rgba(253, 126, 20, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Auto-open modal if redirected from show route
    @if(session('open_modal'))
    var modalToOpen = document.getElementById('{{ session('open_modal') }}');
    if (modalToOpen) {
        var modal = new bootstrap.Modal(modalToOpen);
        modal.show();
    }
    @endif

    // Status chart
    const statuts = @json($rapportsParStatut);
    new Chart(document.getElementById('rapportsParStatutChart'), {
        type: 'doughnut',
        data: {
            labels: ['En attente', 'Validés', 'Rejetés'],
            datasets: [{
                data: [statuts.en_attente, statuts.valide, statuts.rejete],
                backgroundColor: ['#ffc107', '#198754', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
@endsection
