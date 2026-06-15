@extends('layouts.role-dynamique')

@section('title', 'Détails de la Phase')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.phases.index') }}" class="text-decoration-none">Phases</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-kanban me-2"></i>{{ $phase->nom }}</h1>
                <p class="cp-page-subtitle">Vue d'ensemble de la phase et de ses tâches</p>
            </div>
            <div class="d-flex gap-2">
                @include('partials.row-export', ['id' => $phase->id, 'prefix' => 'phase', 'title' => 'Détails de la Phase'])
                <a href="{{ route('role-dynamique.phases.edit', $phase->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                <a href="{{ route('role-dynamique.phases.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-list-task"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $phase->taches->count() }}</div>
                    <div class="cp-stat-label">Tâches</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-diagram-3"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $phase->taches->sum(fn($tache) => $tache->sousTaches->count()) }}</div>
                    <div class="cp-stat-label">Sous-tâches</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-bar-chart-line"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $phase->avancement ?? 0 }}%</div>
                    <div class="cp-stat-label">Avancement</div>
                </div>
            </div>
            <div class="cp-stat-card cp-stat-danger">
                <div class="cp-stat-icon cp-bg-danger"><i class="bi bi-flag"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ ucfirst(str_replace('_', ' ', $phase->statut ?? 'en_attente')) }}</div>
                    <div class="cp-stat-label">Statut</div>
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
                                <label class="text-muted small fw-bold">Nom de la phase</label>
                                <div class="fs-5 fw-bold">{{ $phase->nom }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Projet</label>
                                <div class="fw-semibold">{{ $phase->projet->nom ?? 'Non défini' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Responsable du projet</label>
                                <div class="fw-semibold">
                                    {{ trim(($phase->projet->admin->prenom ?? '') . ' ' . ($phase->projet->admin->name ?? '')) ?: '' }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Partenaire</label>
                                <div class="fw-semibold">{{ $phase->projet->partenaire->nom ?? $phase->projet->partenaire->name ?? 'Non défini' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Date de début</label>
                                <div>{{ $phase->date_debut ? $phase->date_debut->format('d/m/Y') : 'Non définie' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Date de fin prévue</label>
                                <div>{{ $phase->date_fin_prevue ? $phase->date_fin_prevue->format('d/m/Y') : 'Non définie' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Date de fin réelle</label>
                                <div>{{ $phase->date_fin_reelle ? $phase->date_fin_reelle->format('d/m/Y') : 'Non définie' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Ordre</label>
                                <div>{{ $phase->ordre ?? 'Non défini' }}</div>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small fw-bold">Description</label>
                                <div class="bg-light rounded p-3" style="min-height: 110px;">
                                    {!! nl2br(e($phase->description ?: 'Aucune description renseignée.')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-list-check me-2"></i>Tâches de la phase</h6>
                    </div>
                    <div class="p-4">
                        @if($phase->taches->isEmpty())
                            <p class="text-muted mb-0">Aucune tâche n'est encore liée à cette phase.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tâche</th>
                                            <th>Responsable</th>
                                            <th>Statut</th>
                                            <th>Avancement</th>
                                            <th>Sous-tâches</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($phase->taches as $tache)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $tache->titre }}</div>
                                                <div class="small text-muted">{{ $tache->date_fin_prevue ? 'Échéance: '.$tache->date_fin_prevue->format('d/m/Y') : 'Sans échéance' }}</div>
                                            </td>
                                            <td>{{ trim(($tache->responsable->prenom ?? '') . ' ' . ($tache->responsable->name ?? '')) ?: '' }}</td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'en_attente' => 'bg-secondary',
                                                        'en_cours' => 'bg-primary',
                                                        'terminee' => 'bg-success',
                                                        'bloquee' => 'bg-danger',
                                                        'en_retard' => 'bg-danger',
                                                    ][$tache->statut] ?? 'bg-secondary';
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $tache->statut ?? 'en_attente')) }}</span>
                                            </td>
                                            <td style="min-width: 150px;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 8px;">
                                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $tache->avancement ?? 0 }}%;"></div>
                                                    </div>
                                                    <span class="small fw-bold">{{ $tache->avancement ?? 0 }}%</span>
                                                </div>
                                            </td>
                                            <td>{{ $tache->sousTaches->count() }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-graph-up-arrow me-2"></i>Suivi rapide</h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="text-muted small fw-bold d-block mb-2">Progression globale</label>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $phase->avancement ?? 0 }}%;"></div>
                            </div>
                            <div class="small text-muted mt-2">{{ $phase->avancement ?? 0 }}% d'avancement</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Tâches terminées</span>
                            <span class="fw-bold">{{ $phase->taches->where('statut', 'terminee')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Tâches en cours</span>
                            <span class="fw-bold">{{ $phase->taches->where('statut', 'en_cours')->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Tâches en attente</span>
                            <span class="fw-bold">{{ $phase->taches->where('statut', 'en_attente')->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-clock-history me-2"></i>Historique</h6>
                    </div>
                    <div class="p-4">
                        <div class="small text-muted mb-2">Créée le</div>
                        <div class="fw-semibold mb-3">{{ $phase->created_at->format('d/m/Y à H:i') }}</div>
                        <div class="small text-muted mb-2">Dernière modification</div>
                        <div class="fw-semibold">{{ $phase->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
