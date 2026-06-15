@extends('layouts.role-dynamique')

@section('title', 'Détails de la Sous-Traitance')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.sous-traitances.index') }}" class="text-decoration-none">Sous-Traitances</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Fiche #{{ $sousTraitance->id }}</span>
@endsection

@section('content')
    <div class="cp-dashboard">
        <div class="cp-content">
            <div class="cp-page-header">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-people fs-2" style="color: #009A44;"></i>
                    </div>
                    <div>
                        <h1 class="cp-page-title mb-1">{{ $sousTraitance->nom_entreprise }}</h1>
                        <p class="cp-page-subtitle mb-0">Partenaire sous-traitant</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('role-dynamique.sous-traitances.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>


            <div class="row g-4">
                <!-- Colonne Principale -->
                <div class="col-lg-8">
                    <div class="cp-chart-card mb-4 shadow-sm border-0">
                        <div class="cp-chart-header border-bottom py-3">
                            <h6 class="cp-chart-title mb-0"><i class="bi bi-card-heading me-2"></i>Détails du Contrat</h6>
                            @php
                                $statusBadge = [
                                    'en_attente' => 'bg-secondary',
                                    'en_cours' => 'bg-green',
                                    'terminee' => 'bg-success',
                                    'annule' => 'bg-danger'
                                ][$sousTraitance->statut] ?? 'bg-secondary';
                                $statusText = [
                                    'en_attente' => 'En attente',
                                    'en_cours' => 'En cours',
                                    'terminee' => 'Terminée',
                                    'annule' => 'Annulé'
                                ][$sousTraitance->statut] ?? ucfirst($sousTraitance->statut ?? 'N/A');
                            @endphp
                            <span class="badge {{ $statusBadge }} text-white px-3 py-2">
                                {{ $statusText }}
                            </span>
                        </div>
                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Entreprise</label>
                                    <p class="fw-medium mb-0 fs-5">{{ $sousTraitance->nom_entreprise }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Projet</label>
                                    <p class="fw-medium mb-0">
                                        @if($sousTraitance->projet)
                                            <i class="bi bi-kanban me-1 text-muted"></i>{{ $sousTraitance->projet->nom }}
                                        @else
                                            <span class="text-muted italic">Non associé</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Effectif Alloué</label>
                                    <p class="fw-bold fs-5 mb-0 text-dark"><i
                                            class="bi bi-person-workspace me-2 text-info"></i>{{ $sousTraitance->nombre_employes ?? 1 }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase fw-bold mb-1">Période</label>
                                    <div class="small fw-medium">
                                        <div><i class="bi bi-calendar-event me-1 text-success"></i> Début:
                                            {{ $sousTraitance->date_debut ? date('d/m/Y', strtotime($sousTraitance->date_debut)) : 'N/A' }}
                                        </div>
                                        <div class="mt-1"><i class="bi bi-calendar-check me-1 text-danger"></i> Fin:
                                            {{ $sousTraitance->date_fin ? date('d/m/Y', strtotime($sousTraitance->date_fin)) : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4 pt-4 border-top">
                                    <label class="text-muted small text-uppercase fw-bold mb-2">Description de la
                                        Tâche</label>
                                    <div class="bg-light p-3 rounded" style="min-height: 80px; white-space: pre-wrap;">
                                        {{ $sousTraitance->description_tache ?? 'Aucune description détaillée n’a été fournie.' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes et Observations -->
                    <div class="cp-chart-card shadow-sm border-0">
                        <div class="cp-chart-header border-bottom py-3">
                            <h6 class="cp-chart-title mb-0"><i class="bi bi-journal-text me-2"></i>Notes / Observations</h6>
                        </div>
                        <div class="p-4">
                            <div class="bg-light p-3 rounded-3 min-vh-10 border">
                                @if($sousTraitance->notes)
                                    {!! nl2br(e($sousTraitance->notes)) !!}
                                @else
                                    <p class="text-muted italic mb-0">Aucune observation additionnelle.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne Droite: Personne de Contact & Stats -->
                <div class="col-lg-4">
                    <div class="cp-chart-card shadow-sm border-0 sticky-top" style="top: 2rem;">
                        <div class="cp-chart-header border-bottom py-3">
                            <h6 class="cp-chart-title mb-0"><i class="bi bi-person-lines-fill me-2"></i>Contact Partenaire
                            </h6>
                        </div>
                        <div class="p-4">
                            <div class="text-center mb-4">
                                <div class="d-inline-flex p-4 text-white rounded-circle mb-3 shadow-sm"
                                    style="background-color: #009A44;">
                                    <i class="bi bi-person fs-1"></i>
                                </div>
                                <h5 class="mb-1">
                                    {{ $sousTraitance->contact_nom ? $sousTraitance->contact_prenom . ' ' . $sousTraitance->contact_nom : 'Contact Non Spécifié' }}
                                </h5>
                                <p class="text-muted small">Représentant direct</p>
                            </div>

                            <ul class="list-group list-group-flush border-top">
                                <li class="list-group-item px-0 py-3 bg-transparent">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 p-2 bg-light rounded text-muted me-3">
                                            <i class="bi bi-envelope"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted small d-block mb-1">Email</label>
                                            @if($sousTraitance->contact_email)
                                                <a href="mailto:{{ $sousTraitance->contact_email }}"
                                                    class="text-dark fw-medium text-decoration-none">
                                                    {{ $sousTraitance->contact_email }}
                                                </a>
                                            @else
                                                <span class="text-muted small italic">Non renseigné</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item px-0 py-3 bg-transparent border-bottom-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 p-2 bg-light rounded text-muted me-3">
                                            <i class="bi bi-telephone"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="text-muted small d-block mb-1">Téléphone</label>
                                            @if($sousTraitance->contact_telephone)
                                                <a href="tel:{{ $sousTraitance->contact_telephone }}"
                                                    class="text-dark fw-medium text-decoration-none">
                                                    {{ $sousTraitance->contact_telephone }}
                                                </a>
                                            @else
                                                <span class="text-muted small italic">Non renseigné</span>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
