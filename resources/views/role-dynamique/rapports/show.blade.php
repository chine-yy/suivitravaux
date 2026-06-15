@extends('layouts.role-dynamique')

@section('title', 'Détails du Rapport')

@section('breadcrumb')
    <span class="text-muted"><a href="{{ route('role-dynamique.rapports.index') }}">Rapports</a></span> / 
    <span class="text-muted">Détails du Rapport</span>
@endsection

@section('content')
<div class="cp-content">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $rapport->titre }}</h1>
            <p class="cp-page-subtitle">Consultez les détails du rapport soumis.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('role-dynamique.rapports.voir-pdf', $rapport->id) }}" class="btn btn-outline-primary" target="_blank">
                <i class="bi bi-eye me-2"></i>Voir PDF
            </a>
            <a href="{{ route('role-dynamique.rapports.pdf', $rapport->id) }}" class="btn btn-outline-danger">
                <i class="bi bi-download me-2"></i>Télécharger PDF
            </a>
            <a href="{{ route('role-dynamique.rapports.edit', $rapport->id) }}" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-2"></i>Modifier
            </a>
            <a href="{{ route('role-dynamique.rapports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Message d'information -->
    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Information :</strong> En tant qu'Administrateur, vous pouvez uniquement modifier le statut de ce rapport. Tous les autres champs sont en lecture seule.
    </div>

    <!-- Formulaire modification statut -->
    <div class="cp-card mb-4">
        <div class="cp-card-body">
            <form action="{{ route('role-dynamique.rapports.update-statut', $rapport->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Modifier le statut du rapport</label>
                        <select name="statut" class="form-select">
                            <option value="en_attente" {{ $rapport->statut === 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="valide" {{ $rapport->statut === 'valide' ? 'selected' : '' }}>Validé</option>
                            <option value="rejete" {{ $rapport->statut === 'rejete' ? 'selected' : '' }}>Rejeté</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-lg me-2"></i>Enregistrer le statut
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="cp-card mb-4">
                <div class="cp-card-header">
                    <h5 class="mb-0">Contenu du rapport</h5>
                </div>
                <div class="cp-card-body">
                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Description / Activités</h6>
                        <div class="p-3 bg-light rounded" style="white-space: pre-wrap; min-height: 200px;">{{ $rapport->contenu ?? 'Aucun contenu fourni.' }}</div>
                    </div>

                    @if($rapport->observations)
                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Observations</h6>
                        <div class="p-3 border-start border-4 border-warning bg-light rounded">{{ $rapport->observations }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="cp-card mb-4">
                <div class="cp-card-header">
                    <h5 class="mb-0">Informations</h5>
                </div>
                <div class="cp-card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted">Projet</span>
                            <span class="fw-bold">{{ $rapport->projet->nom ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted">Type</span>
                            <span class="badge bg-light text-dark border">{{ $rapport->getTypeLabel() }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted">Statut</span>
                            <span class="badge {{ $rapport->statut === 'valide' ? 'bg-success' : ($rapport->statut === 'rejete' ? 'bg-danger' : 'bg-info') }}">
                                {{ $rapport->getStatutLabel() }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted">Date création</span>
                            <span>{{ $rapport->created_at->format('d/m/Y à H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="cp-card mb-4">
                <div class="cp-card-header">
                    <h5 class="mb-0">Auteur</h5>
                </div>
                <div class="cp-card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3 bg-light text-green border border-green-subtle shadow-sm fs-4">
                            {{ strtoupper(substr($rapport->auteur->name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold fs-5 text-dark">{{ $rapport->auteur->prenom ?? '' }} {{ $rapport->auteur->name ?? 'N/A' }}</div>
                            <div class="text-green small"><i class="bi bi-shield-lock me-1"></i>{{ $rapport->auteur->role->nom ?? 'Auteur' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
