@extends('layouts.super-admin')

@section('title', 'Détails Fournisseur')

@section('breadcrumb')
    <a href="{{ route('super-admin.fournisseurs.index') }}" class="text-decoration-none">Fournisseurs</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">{{ $fournisseur->nom }}</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div class="d-flex align-items-center">
                <div class="me-3 p-3 bg-primary rounded-3 text-white shadow-sm">
                    <i class="bi bi-truck fs-2"></i>
                </div>

                <div>
                    <h1 class="cp-page-title mb-1">{{ $fournisseur->nom }}</h1>
                    <p class="cp-page-subtitle mb-0">Fiche détaillée du fournisseur</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.fournisseurs.edit', $fournisseur->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                <a href="{{ route('super-admin.fournisseurs.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>


        <div class="row g-4">
            <!-- Colonne Gauche: Infos Entreprise -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4 shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-building me-2"></i>Informations de l'entreprise</h6>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Nom du Fournisseur</label>
                                <p class="fw-medium mb-0">{{ $fournisseur->nom }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Statut</label>
                                <div>
                                    <span class="badge {{ $fournisseur->statut == 'actif' ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                                        {{ $fournisseur->statut == 'actif' ? 'Actif' : 'Inactif' }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Catégorie</label>
                                <p class="fw-medium mb-0 text-primary">{{ $fournisseur->categorie ?? 'Non spécifiée' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Email Entreprise</label>
                                <p class="fw-medium mb-0">
                                    @if($fournisseur->email)
                                        <a href="mailto:{{ $fournisseur->email }}" class="text-decoration-none text-dark">
                                            <i class="bi bi-envelope me-1 text-muted"></i>{{ $fournisseur->email }}
                                        </a>
                                    @else
                                        <span class="text-muted italic">Non renseigné</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Téléphone Entreprise</label>
                                <p class="fw-medium mb-0">
                                    @if($fournisseur->telephone)
                                        <a href="tel:{{ $fournisseur->telephone }}" class="text-decoration-none text-dark">
                                            <i class="bi bi-phone me-1 text-muted"></i>{{ $fournisseur->telephone }}
                                        </a>
                                    @else
                                        <span class="text-muted italic">Non renseigné</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Site Web</label>
                                <p class="fw-medium mb-0">
                                    @if($fournisseur->site_web)
                                        <a href="{{ $fournisseur->site_web }}" target="_blank" class="text-primary text-decoration-none">
                                            <i class="bi bi-globe me-1 text-primary"></i>Visualiser le site
                                        </a>
                                    @else
                                        <span class="text-muted italic">Non renseigné</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Adresse</label>
                                <p class="fw-medium mb-0">
                                    <i class="bi bi-geo-alt me-1 text-muted"></i>{{ $fournisseur->adresse ?? 'Non spécifiée' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="cp-chart-card shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-journal-text me-2"></i>Notes & Observations</h6>
                    </div>
                    <div class="p-4">
                        <div class="bg-light p-3 rounded-3 min-vh-10 border">
                            @if($fournisseur->notes)
                                {!! nl2br(e($fournisseur->notes)) !!}
                            @else
                                <p class="text-muted italic mb-0">Aucune note particulière.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne Droite: Personne de Contact -->
            <div class="col-lg-4">
                <div class="cp-chart-card shadow-sm border-0 sticky-top" style="top: 2rem;">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-person-badge me-2"></i>Personne de contact</h6>
                    </div>
                    <div class="p-4">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex p-4 bg-primary rounded-circle text-white mb-3 shadow-sm">
                                <i class="bi bi-person fs-1"></i>
                            </div>

                            <h5 class="mb-1">{{ $fournisseur->contact_prenom }} {{ $fournisseur->contact_nom }}</h5>
                            <p class="text-muted small">Interlocuteur privilégié</p>
                        </div>

                        <div class="list-group list-group-flush border-top">
                            <div class="list-group-item px-0 py-3 bg-transparent border-bottom-0">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 p-2 bg-light rounded text-muted me-3">
                                        <i class="bi bi-telephone"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="text-muted small display-block">Téléphone Direct</label>
                                        @if($fournisseur->contact_telephone)
                                            <a href="tel:{{ $fournisseur->contact_telephone }}" class="text-dark fw-medium text-decoration-none">
                                                {{ $fournisseur->contact_telephone }}
                                            </a>
                                        @else
                                            <span class="text-muted small italic">Non renseigné</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-top">
                            <div class="d-grid">
                                <form action="{{ route('super-admin.fournisseurs.destroy', $fournisseur->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Attention ! Cette action est irréversible. Supprimer ce fournisseur ?')">
                                        <i class="bi bi-trash me-2"></i>Supprimer le fournisseur
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
