@extends('layouts.role-dynamique')

@section('title', 'Détails de la Tâche')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.taches.index') }}" class="text-decoration-none">Tâches</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-list-task me-2"></i>{{ $tache->titre }}</h1>
                <p class="cp-page-subtitle">Vue d'ensemble de la tâche et de son exécution</p>
            </div>
            <div class="d-flex gap-2">
                @include('partials.row-export', ['id' => $tache->id, 'prefix' => 'tache', 'title' => 'Détails de la Tâche'])
                @if($has('edit-taches'))
                <a href="{{ route('role-dynamique.taches.edit', $tache->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                @endif
                <a href="{{ route('role-dynamique.taches.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-primary"><i class="bi bi-diagram-3"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $tache->sousTaches->count() }}</div>
                    <div class="cp-stat-label">Sous-tâches</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-person"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $tache->assignedPersonnels()->count() }}</div>
                    <div class="cp-stat-label">Personnels</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-bar-chart-line"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $tache->avancement ?? 0 }}%</div>
                    <div class="cp-stat-label">Avancement</div>
                </div>
            </div>
            <div class="cp-stat-card cp-stat-danger">
                <div class="cp-stat-icon cp-bg-danger"><i class="bi bi-flag"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ ucfirst(str_replace('_', ' ', $tache->statut ?? 'a_faire')) }}</div>
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
                                <label class="text-muted small fw-bold">Titre</label>
                                <div class="fs-5 fw-bold">{{ $tache->titre }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Projet</label>
                                <div class="fw-semibold">{{ $tache->projet->nom ?? 'Non défini' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Phase</label>
                                <div>{{ $tache->phase->nom ?? 'Aucune phase' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Priorité</label>
                                <div>{{ ucfirst($tache->priorite ?? 'normale') }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Statut</label>
                                <div>{{ ucfirst(str_replace('_', ' ', $tache->statut ?? 'a_faire')) }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Date de début</label>
                                <div>{{ $tache->date_debut_prevue ? $tache->date_debut_prevue->format('d/m/Y') : 'Non définie' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Date de fin prévue</label>
                                <div>{{ $tache->date_fin_prevue ? $tache->date_fin_prevue->format('d/m/Y') : 'Non définie' }}</div>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small fw-bold">Description</label>
                                <div class="bg-light rounded p-3" style="min-height: 110px;">
                                    {!! nl2br(e($tache->description ?: 'Aucune description renseignée.')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-list-check me-2"></i>Sous-tâches</h6>
                    </div>
                    <div class="p-4">
                        @if($tache->sousTaches->isEmpty())
                            <p class="text-muted mb-0">Aucune sous-tâche n'est encore liée à cette tâche.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Sous-tâche</th>
                                            <th>Statut</th>
                                            <th>Avancement</th>
                                            <th>Date de fin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tache->sousTaches as $sousTache)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $sousTache->titre }}</div>
                                                <div class="small text-muted">{{ $sousTache->description ?: 'Sans description' }}</div>
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $sousTache->statut ?? 'en_attente')) }}</td>
                                            <td>{{ $sousTache->avancement ?? 0 }}%</td>
                                            <td>{{ $sousTache->date_fin_prevue ? $sousTache->date_fin_prevue->format('d/m/Y') : 'Non définie' }}</td>
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
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $tache->avancement ?? 0 }}%;"></div>
                            </div>
                            <div class="small text-muted mt-2">{{ $tache->avancement ?? 0 }}% d'avancement</div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Projet</span>
                            <span class="fw-bold">{{ $tache->projet->nom ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Partenaire</span>
                            <span class="fw-bold">{{ $tache->projet->partenaire->nom ?? $tache->projet->partenaire->name ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Sous-tâches terminées</span>
                            <span class="fw-bold">{{ $tache->sousTaches->where('statut', 'terminee')->count() }}</span>
                        </div>
                    </div>
                </div>

                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-clock-history me-2"></i>Historique</h6>
                    </div>
                    <div class="p-4">
                        <div class="small text-muted mb-2">Créée le</div>
                        <div class="fw-semibold mb-3">{{ $tache->created_at->format('d/m/Y à H:i') }}</div>
                        <div class="small text-muted mb-2">Dernière modification</div>
                        <div class="fw-semibold">{{ $tache->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
