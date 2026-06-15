@extends('layouts.role-dynamique')

@section('title', 'Détails du Rendez-vous')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.rendezvous.index') }}" class="text-decoration-none">Rendez-vous</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">{{ $rendezvous->titre }}</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div class="d-flex align-items-center">
                <div class="me-3 p-3 bg-primary rounded-3 text-white shadow-sm">
                    <i class="bi bi-calendar-event fs-2"></i>
                </div>
                <div>
                    <h1 class="cp-page-title mb-1">{{ $rendezvous->titre }}</h1>
                    <p class="cp-page-subtitle mb-0">Planifié le {{ $rendezvous->date_heure ? $rendezvous->date_heure->format('d/m/Y à H:i') : 'N/A' }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                @if($has('edit-rendezvous'))
                <a href="{{ route('role-dynamique.rendezvous.edit', $rendezvous->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                @endif
                <a href="{{ route('role-dynamique.rendezvous.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>


        <div class="row g-4">
            <!-- Colonne Gauche: Détails RDV -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4 shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-info-circle me-2"></i>Informations générales</h6>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Type de rendez-vous</label>
                                <div>
                                    @php
                                        $types = ['reunion' => 'Réunion', 'visite' => 'Visite', 'appel' => 'Appel', 'autre' => 'Autre'];
                                    @endphp
                                    <span class="badge bg-light text-dark px-3 py-2 fs-6">
                                        {{ $rendezvous->type == 'autre' ? ($rendezvous->type_autre ?? 'Autre') : ($types[$rendezvous->type] ?? $rendezvous->type) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Statut</label>
                                <div>
                                    @php
                                        $statutClass = ['planifie' => 'bg-info', 'confirme' => 'bg-success', 'termine' => 'bg-secondary', 'annule' => 'bg-danger'];
                                        $statutText = ['planifie' => 'Planifié', 'confirme' => 'Confirmé', 'termine' => 'Terminé', 'annule' => 'Annulé'];
                                    @endphp
                                    <span class="badge {{ $statutClass[$rendezvous->statut] ?? 'bg-secondary' }} px-3 py-2">
                                        {{ $statutText[$rendezvous->statut] ?? $rendezvous->statut }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Date et Heure</label>
                                <p class="fw-medium mb-0">
                                    <i class="bi bi-calendar3 me-2 text-primary"></i>
                                    {{ $rendezvous->date_heure ? $rendezvous->date_heure->format('d/m/Y') : 'N/A' }}
                                    <span class="ms-2"><i class="bi bi-clock me-1 text-primary"></i> {{ $rendezvous->date_heure ? $rendezvous->date_heure->format('H:i') : '' }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Durée estimée</label>
                                <p class="fw-medium mb-0"><i class="bi bi-hourglass-split me-2 text-primary"></i>{{ $rendezvous->duree_minutes }} minutes</p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Lieu</label>
                                <div class="bg-light p-3 rounded-3 border">
                                    <i class="bi bi-geo-alt-fill me-2 text-danger"></i>
                                    <span class="fw-medium">{{ $rendezvous->lieu ?? 'Non spécifié' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="cp-chart-card shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-justify-left me-2"></i>Notes & Description</h6>
                    </div>
                    <div class="p-4">
                        <div class="bg-light p-4 rounded-3 min-vh-20 border">
                            @if($rendezvous->description)
                                {!! nl2br(e($rendezvous->description)) !!}
                            @else
                                <p class="text-muted italic mb-0">Aucune description pour ce rendez-vous.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne Droite: Projet -->
            <div class="col-lg-4">
                <!-- Bloc Projet -->
                <div class="cp-chart-card shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-kanban me-2"></i>Projet Lié</h6>
                    </div>
                    <div class="p-4">
                        @if($rendezvous->projet)
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-3 bg-success bg-opacity-10 rounded-circle text-success me-3">
                                    <i class="bi bi-briefcase fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $rendezvous->projet->nom }}</h5>
                                    <p class="text-muted small mb-0">Réf: {{ $rendezvous->projet->reference ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="d-grid mt-3">
                                <a href="{{ route('role-dynamique.projets.show', $rendezvous->projet->id) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-eye me-1"></i>Voir détails projet
                                </a>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted mb-0 italic">Aucun projet lié</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions de suppression -->
                @if($has('delete-rendezvous'))
                <div class="mt-4 p-2">
                    <form action="{{ route('role-dynamique.rendezvous.destroy', $rendezvous->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-warning w-100" onclick="return confirm('Supprimer définitivement ce rendez-vous ?')">
                            <i class="bi bi-trash me-2"></i>Supprimer ce rendez-vous
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
