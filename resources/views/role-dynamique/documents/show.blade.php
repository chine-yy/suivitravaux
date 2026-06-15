@extends('layouts.role-dynamique')

@section('title', 'Détails du Document')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.documents.index') }}" class="text-decoration-none">Documents</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">{{ $document->nom }}</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div class="d-flex align-items-center">
                <div class="me-3 p-3 bg-primary rounded-3 text-white shadow-sm">
                    <i class="bi bi-file-earmark-text fs-2"></i>
                </div>
                <div>
                    <h1 class="cp-page-title mb-1">{{ $document->nom }}</h1>
                    <p class="cp-page-subtitle mb-0">Informations du document</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                @if($document->fichier && $has('download-documents'))
                    <a href="{{ asset('storage/' . $document->fichier) }}" target="_blank" class="btn btn-success px-4">
                        <i class="bi bi-download me-2"></i>Télécharger fichier
                    </a>
                @endif
                @if($has('edit-documents'))
                <a href="{{ route('role-dynamique.documents.edit', $document->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                @endif
                <a href="{{ route('role-dynamique.documents.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>


        <div class="row g-4">
            <!-- Colonne Principale -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4 shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-info-circle me-2"></i>Aperçu des informations</h6>
                        <span class="badge {{ $document->statut == 'actif' ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                            {{ $document->statut == 'actif' ? 'Actif' : 'Archivé' }}
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Nom du Fichier</label>
                                <p class="fw-medium mb-0">{{ $document->nom }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Type</label>
                                <p class="fw-medium mb-0">
                                    @php
                                        $types = ['contrat' => 'Contrat', 'facture' => 'Facture', 'rapport' => 'Rapport', 'photo' => 'Photo', 'plan' => 'Plan', 'autre' => 'Autre'];
                                        $displayType = $document->type === 'autre' && $document->type_personnalise 
                                            ? $document->type_personnalise 
                                            : ($types[$document->type] ?? ucfirst($document->type));
                                    @endphp
                                    <span class="badge border border-secondary text-dark">{{ $displayType }}</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Catégorie</label>
                                <p class="fw-medium mb-0 text-primary">{{ $document->categorie ?? 'Non spécifiée' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Projet Associé</label>
                                <p class="fw-medium mb-0">
                                    @if($document->projet)
                                        <i class="bi bi-kanban me-1 text-muted"></i>{{ $document->projet->nom }}
                                    @else
                                        <span class="text-muted italic">Aucun projet</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="cp-chart-card shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-body-text me-2"></i>Description / Contenu</h6>
                    </div>
                    <div class="p-4">
                        <div class="bg-light p-3 rounded-3 min-vh-10 border">
                            @if($document->description)
                                {!! nl2br(e($document->description)) !!}
                            @else
                                <p class="text-muted italic mb-0">Aucune description disponible pour ce document.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne Droite: Métadonnées -->
            <div class="col-lg-4">
                <div class="cp-chart-card shadow-sm border-0 sticky-top" style="top: 2rem;">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-hdd me-2"></i>Méta-informations</h6>
                    </div>
                    <div class="p-4">
                        <div class="text-center mb-4">
                            @if($document->type == 'photo')
                                <div class="d-inline-flex p-4 text-white rounded-circle mb-3 shadow-sm" style="background-color: #009A44;">
                                    <i class="bi bi-image fs-1"></i>
                                </div>
                            @elseif($document->type == 'plan')
                                <div class="d-inline-flex p-4 text-white rounded-circle mb-3 shadow-sm" style="background-color: #009A44;">
                                    <i class="bi bi-rulers fs-1"></i>
                                </div>
                            @else
                                <div class="d-inline-flex p-4 text-white rounded-circle mb-3 shadow-sm" style="background-color: #009A44;">
                                    <i class="bi bi-file-earmark-pdf fs-1"></i>
                                </div>
                            @endif
                            <h5 class="mb-1 text-truncate" title="{{ $document->nom }}">{{ Str::limit($document->nom, 25) }}</h5>
                        </div>

                        <ul class="list-group list-group-flush border-top">
                            <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-transparent">
                                <span class="text-muted small"><i class="bi bi-person me-2"></i>Ajouté par</span>
                                <span class="fw-medium text-end">{{ $document->user->name ?? 'Système' }}</span>
                            </li>
                            <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-transparent">
                                <span class="text-muted small"><i class="bi bi-calendar-plus me-2"></i>Date d'ajout</span>
                                <span class="fw-medium">{{ $document->created_at->format('d/m/Y H:i') }}</span>
                            </li>
                            <li class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-transparent">
                                <span class="text-muted small"><i class="bi bi-calendar-check me-2"></i>Dernière modif.</span>
                                <span class="fw-medium">{{ $document->updated_at->format('d/m/Y H:i') }}</span>
                            </li>
                        </ul>

                        <div class="mt-4 pt-4 border-top">
                            @if($has('delete-documents'))
                            <div class="d-grid">
                                <form action="{{ route('role-dynamique.documents.destroy', $document->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Attention ! Supprimer ce document supprimera également le fichier associé. Continuer ?')">
                                        <i class="bi bi-trash me-2"></i>Supprimer le document
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
