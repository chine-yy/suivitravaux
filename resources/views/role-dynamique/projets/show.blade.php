@extends('layouts.role-dynamique')

@section('title', 'Détails du Projet')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.projets.index') }}" class="text-decoration-none">Projets</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-briefcase me-2"></i>{{ $projet->nom }}</h1>
                <p class="cp-page-subtitle">Vue d'ensemble du projet et de son avancement</p>
            </div>
            <div class="d-flex gap-2">
                @include('partials.row-export', ['id' => $projet->id, 'prefix' => 'projet', 'title' => 'Détails du Projet'])
                @if($has('edit-projets'))
                <a href="{{ route('role-dynamique.projets.edit', $projet->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                @endif
                <a href="{{ route('role-dynamique.projets.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-list-task"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $projet->taches->count() }}</div>
                    <div class="cp-stat-label">Tâches</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-layers"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $projet->phases->count() }}</div>
                    <div class="cp-stat-label">Phases</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-cash-stack"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ number_format($budgetTotal, 0, ',', ' ') }}</div>
                    <div class="cp-stat-label">Budget alloué</div>
                </div>
            </div>
            <div class="cp-stat-card cp-stat-danger">
                <div class="cp-stat-icon cp-bg-danger"><i class="bi bi-bar-chart-line"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $projet->avancement ?? 0 }}%</div>
                    <div class="cp-stat-label">Avancement</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Informations générales</h6>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Nom du projet</label>
                                <div class="fs-5 fw-bold">{{ $projet->nom }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Statut</label>
                                @php
                                    $statusClass = [
                                        'en_attente' => 'bg-secondary',
                                        'en_cours' => 'bg-primary',
                                        'termine' => 'bg-success',
                                        'en_retard' => 'bg-danger',
                                    ][$projet->statut] ?? 'bg-secondary';
                                @endphp
                                <div>
                                    <span class="badge {{ $statusClass }} px-3 py-2 mt-1">{{ ucfirst(str_replace('_', ' ', $projet->statut)) }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Partenaire</label>
                                <div class="fw-semibold">
                                    @if(!empty($partenaires) && $partenaires->count() > 0)
                                        @foreach($partenaires as $i => $c)
                                            <div>N°{{ $i + 1 }} {{ $c->nom ?? '—' }} {{ $c->prenom ?? '' }} {{ $c->email ?? '' }} {{ $c->telephone ?? '' }}</div>
                                        @endforeach
                                    @else
                                        <div>Aucun partenaire associé</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Entreprise</label>
                                <div>{{ optional(optional($projet->admin)->entreprise)->nom_entreprise ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Date de début</label>
                                <div>{{ $projet->date_debut ? $projet->date_debut->format('d/m/Y') : 'Non définie' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Date de fin prévue</label>
                                <div>{{ $projet->date_fin_prevue ? $projet->date_fin_prevue->format('d/m/Y') : 'Non définie' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Date de fin réelle</label>
                                <div>{{ $projet->date_fin_reelle ? $projet->date_fin_reelle->format('d/m/Y') : 'Non définie' }}</div>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small fw-bold">Description</label>
                                <div class="bg-light rounded p-3" style="min-height: 110px;">
                                    {!! nl2br(e($projet->description ?: 'Aucune description renseignée.')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-list-check me-2"></i>Suivi des tâches</h6>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <div class="small text-muted mb-1">En attente</div>
                                    <div class="fs-4 fw-bold">{{ $tachesStats['en_attente'] }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <div class="small text-muted mb-1">En cours</div>
                                    <div class="fs-4 fw-bold">{{ $tachesStats['en_cours'] }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="border rounded p-3 text-center">
                                    <div class="small text-muted mb-1">Terminées</div>
                                    <div class="fs-4 fw-bold">{{ $tachesStats['terminee'] }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="text-muted small fw-bold d-block mb-2">Progression globale</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress flex-grow-1" style="height: 10px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $projet->avancement ?? 0 }}%;"></div>
                                </div>
                                <span class="fw-bold">{{ $projet->avancement ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-kanban me-2"></i>Phases du projet</h6>
                    </div>
                    <div class="p-4">
                        @if($projet->phases->isEmpty())
                            <p class="text-muted mb-0">Aucune phase enregistrée pour ce projet.</p>
                        @else
                            <div class="row g-3">
                                @foreach($projet->phases as $phase)
                                <div class="col-md-6">
                                    <div class="border rounded p-3 h-100">
                                        <div class="fw-bold mb-1">{{ $phase->nom }}</div>
                                        <div class="small text-muted mb-2">{{ $phase->description ?: 'Sans description' }}</div>
                                        <div class="small text-muted">Début : @if($phase->date_debut){{ $phase->date_debut->format('d/m/Y') }}@else<span class="fst-italic">Non définie</span>@endif</div>
                                        <div class="small text-muted">Fin : @if($phase->date_fin_prevue){{ $phase->date_fin_prevue->format('d/m/Y') }}@else<span class="fst-italic">Non définie</span>@endif</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-wallet2 me-2"></i>Budget</h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Budget alloué</label>
                            <div class="fs-5 fw-bold text-success">{{ number_format($budgetTotal, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Montant consommé</label>
                            <div class="fs-5 fw-bold text-primary">{{ number_format($budgetConsomme, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Budget restant</label>
                            <div class="fs-5 fw-bold text-dark">{{ number_format($budgetRestant, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div>
                            <label class="text-muted small fw-bold d-block mb-2">Taux de consommation</label>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $budgetPourcentage }}%;"></div>
                            </div>
                            <div class="small text-muted mt-2">{{ $budgetPourcentage }}% du budget utilisé</div>
                        </div>
                    </div>
                </div>

                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-diagram-3 me-2"></i>Sous-tâches</h6>
                    </div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>En cours</span>
                            <span class="fw-bold">{{ $sousTachesStats['en_cours'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Terminées</span>
                            <span class="fw-bold">{{ $sousTachesStats['terminee'] }}</span>
                        </div>
                    </div>
                </div>

                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-clock-history me-2"></i>Historique</h6>
                    </div>
                    <div class="p-4">
                        <div class="small text-muted mb-2">Créé le</div>
                        <div class="fw-semibold mb-3">{{ $projet->created_at->format('d/m/Y à H:i') }}</div>
                        <div class="small text-muted mb-2">Dernière modification</div>
                        <div class="fw-semibold">{{ $projet->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
