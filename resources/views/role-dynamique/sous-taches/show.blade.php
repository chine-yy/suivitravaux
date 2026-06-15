@extends('layouts.role-dynamique')

@section('title', 'Détails de la Sous-Tâche')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.sous-taches.index') }}" class="text-decoration-none">Sous-Tâches</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-eye me-2"></i>Détails de la Sous-Tâche</h1>
                <p class="cp-page-subtitle">Consultez les informations de la sous-tâche</p>
            </div>
            <div class="d-flex gap-2">
                @include('partials.row-export', ['id' => $sousTache->id, 'prefix' => 'soustache', 'title' => 'Détails de la Sous-Tâche'])
                @if($has('edit-sous-taches'))
                <a href="{{ route('role-dynamique.sous-taches.edit', $sousTache->id) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i>Modifier
                </a>
                @endif
                <a href="{{ route('role-dynamique.sous-taches.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-list me-1"></i>Retour à la liste
                </a>
            </div>
        </div>

        @php
            $statusBadge = [
                'en_attente' => 'bg-secondary',
                'en_cours' => 'bg-primary',
                'terminee' => 'bg-success',
                'bloquee' => 'bg-danger',
            ][$sousTache->statut] ?? 'bg-secondary';

            $statusText = [
                'en_attente' => 'En attente',
                'en_cours' => 'En cours',
                'terminee' => 'Terminée',
                'bloquee' => 'Bloquée',
            ][$sousTache->statut] ?? ucfirst($sousTache->statut ?? 'N/A');
        @endphp

        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Informations générales</h6>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Titre</label>
                        <div class="fw-semibold">{{ $sousTache->titre }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Statut</label>
                        <div><span class="badge {{ $statusBadge }}">{{ $statusText }}</span></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Tâche parente</label>
                        <div class="fw-semibold">{{ $sousTache->tache->titre ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Projet</label>
                        <div class="fw-semibold">{{ $sousTache->tache->projet->nom ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Phase</label>
                        <div class="fw-semibold">{{ $sousTache->tache->phase->nom ?? 'N/A' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Avancement</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $sousTache->avancement ?? 0 }}%;"></div>
                            </div>
                            <span class="small fw-bold" style="min-width: 42px;">{{ $sousTache->avancement ?? 0 }}%</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Date de début</label>
                        <div class="fw-semibold">
                            @if($sousTache->date_debut)
                                {{ $sousTache->date_debut->format('d/m/Y') }}
                            @else
                                <span class="text-muted fst-italic">Non définie</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Date de fin prévue</label>
                        <div class="fw-semibold">
                            @if($sousTache->date_fin_prevue)
                                {{ $sousTache->date_fin_prevue->format('d/m/Y') }}
                            @else
                                <span class="text-muted fst-italic">Non définie</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small text-muted mb-1">Description</label>
                        <div>{{ $sousTache->description ?: 'Aucune description renseignée.' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-people me-2"></i>Personnels affectés</h6>
            </div>
            <div class="p-4">
                @php
                    $personnels = $sousTache->assignedPersonnels();
                @endphp
                @forelse($personnels as $personnel)
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                            style="width:28px;height:28px;font-size:0.75rem;">
                            {{ strtoupper(substr($personnel->name, 0, 1)) }}
                        </div>
                        <span>{{ $personnel->name }}</span>
                    </div>
                @empty
                    <span class="text-muted small">Aucun personnel affecté.</span>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
