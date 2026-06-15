@extends('layouts.role-dynamique')

@section('title', 'Détails de l\'Incident')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.incidents.index') }}" class="text-decoration-none">Incidents</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Détails #{{ $incident->id }}</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-exclamation-triangle me-2 text-green"></i>{{ $incident->titre }}</h1>
                <p class="cp-page-subtitle">Consultez les informations complètes sur cet incident</p>
            </div>
            <div class="d-flex gap-2">
                @include('partials.row-export', ['id' => $incident->id, 'prefix' => 'incident', 'title' => 'Détails de l\'Incident'])
                @if($has('edit-incidents'))
                <a href="{{ route('role-dynamique.incidents.edit', $incident->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                @endif
                @if($has('delete-incidents'))
                <form action="{{ route('role-dynamique.incidents.destroy', $incident->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="bi bi-trash me-2"></i>Supprimer
                    </button>
                </form>
                @endif
                <a href="{{ route('role-dynamique.incidents.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Main Content: Description -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Description de l'Incident</h6>
                    </div>
                    <div class="p-4">
                        <div class="bg-light rounded p-4 border" style="min-height: 200px; white-space: pre-line;">
                            {{ $incident->description ?: 'Aucune description renseignée.' }}
                        </div>
                    </div>
                </div>

                <!-- Informations complémentaires -->
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-clock-history me-2"></i>Historique</h6>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Date de signalement</label>
                                <div class="fw-semibold">
                                    <i class="bi bi-calendar3 me-2 text-green"></i>{{ $incident->created_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Dernière modification</label>
                                <div class="fw-semibold">
                                    <i class="bi bi-clock me-2 text-primary"></i>{{ $incident->updated_at->format('d/m/Y à H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Metadata & Status -->
            <div class="col-lg-4">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Informations Clés</h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Projet</label>
                            <div class="d-flex align-items-center">
                                <div class="bg-green-soft p-2 rounded me-3">
                                    <i class="bi bi-building text-green"></i>
                                </div>
                                <span class="fw-semibold">{{ $incident->projet->nom ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Signalé par</label>
                            <div class="d-flex align-items-center">
                                <div class="bg-blue-soft p-2 rounded me-3">
                                    <i class="bi bi-person text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $incident->signalePar->name ?? 'Système' }}</div>
                                    <div class="small text-muted">{{ $incident->signalePar->role->nom ?? '' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Gravité</label>
                            @php
                                $gravityClass = [
                                    'faible' => 'bg-info',
                                    'moyen' => 'bg-warning text-dark',
                                    'critique' => 'bg-danger'
                                ][$incident->gravite] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $gravityClass }} px-3 py-2 text-uppercase fw-bold">
                                {{ $incident->gravite }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Statut Actuel</label>
                            @php
                                $statusClass = [
                                    'ouvert' => 'bg-danger-soft text-danger border-danger',
                                    'en_traitement' => 'bg-warning-soft text-warning border-warning',
                                    'resolu' => 'bg-success-soft text-success border-success'
                                ][$incident->statut] ?? 'bg-secondary-soft text-secondary border-secondary';
                            @endphp
                            <span class="badge border {{ $statusClass }} px-3 py-2 text-uppercase fw-bold">
                                {{ str_replace('_', ' ', $incident->statut) }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Date de début</label>
                            <div class="fw-semibold">
                                <i class="bi bi-calendar me-2 text-primary"></i>
                                @if($incident->date_debut)
                                    {{ $incident->date_debut->format('d/m/Y') }}
                                @else
                                    <span class="text-muted fst-italic">Non définie</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-2">Date de fin prévue</label>
                            <div class="fw-semibold">
                                <i class="bi bi-calendar-check me-2 text-success"></i>
                                @if($incident->date_fin_prevue)
                                    {{ $incident->date_fin_prevue->format('d/m/Y') }}
                                @else
                                    <span class="text-muted fst-italic">Non définie</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-green { color: #009A44 !important; }
    .bg-green-soft { background-color: rgba(0, 154, 68, 0.1); }
    .bg-blue-soft { background-color: rgba(0, 123, 255, 0.1); }
    .bg-danger-soft { background-color: rgba(220, 53, 69, 0.1); }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .bg-success-soft { background-color: rgba(40, 167, 69, 0.1); }
    .bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1); }
    .cp-breadcrumb-separator { margin: 0 0.5rem; color: #6c757d; }
    .cp-breadcrumb-item { color: #009A44; font-weight: 600; }
</style>
@endpush
