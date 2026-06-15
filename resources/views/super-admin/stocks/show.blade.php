@extends('layouts.super-admin')

@section('title', 'Détails du Stock')

@section('breadcrumb')
    <a href="{{ route('super-admin.stocks.index') }}" class="text-decoration-none">Stocks</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">{{ $stock->nom }}</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div class="d-flex align-items-center">
                <div class="me-3 p-3 bg-primary rounded-3 text-white shadow-sm">
                    <i class="bi bi-box-seam fs-2"></i>
                </div>
                <div>
                    <h1 class="cp-page-title mb-1">{{ $stock->nom }}</h1>
                    <p class="cp-page-subtitle mb-0">Article Référence: {{ $stock->reference ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('super-admin.stocks.edit', $stock->id) }}" class="btn btn-primary px-4">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                <a href="{{ route('super-admin.stocks.index') }}" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>


        <div class="row g-4">
            <!-- Colonne Gauche: Détails Article -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4 shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-info-circle me-2"></i>Informations sur l'article</h6>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Nom de l'article</label>
                                <p class="fw-medium mb-0">{{ $stock->nom }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Statut</label>
                                <div>
                                    @php
                                        $statutClass = ['disponible' => 'bg-success', 'epuise' => 'bg-danger', 'en_reapprovisionnement' => 'bg-primary'];
                                        $statutText = ['disponible' => 'Disponible', 'epuise' => 'Épuisé', 'en_reapprovisionnement' => 'Réapprovisionnement'];
                                    @endphp
                                    <span class="badge {{ $statutClass[$stock->statut] ?? 'bg-secondary' }} px-3 py-2">
                                        {{ $statutText[$stock->statut] ?? $stock->statut }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Catégorie</label>
                                <p class="fw-medium mb-0 text-primary">{{ $stock->categorie ?? 'Non spécifiée' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Référence</label>
                                <p class="fw-medium mb-0">{{ $stock->reference ?? 'Aucune' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Quantité en Stock</label>
                                <p class="fw-bold fs-4 mb-0 text-dark">{{ $stock->quantite }}</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Prix Unitaire</label>
                                <p class="fw-medium mb-0">{{ number_format($stock->prix_unitaire, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Valeur Totale</label>
                                <p class="fw-bold mb-0 text-success">{{ number_format($stock->getValeurTotale(), 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Emplacement</label>
                                <p class="fw-medium mb-0">
                                    <i class="bi bi-geo-alt me-1 text-muted"></i>{{ $stock->emplacement ?? 'Non spécifié' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="cp-chart-card shadow-sm border-0">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-journal-text me-2"></i>Description / Notes</h6>
                    </div>
                    <div class="p-4">
                        <div class="bg-light p-3 rounded-3 min-vh-10 border">
                            @if($stock->description)
                                {!! nl2br(e($stock->description)) !!}
                            @else
                                <p class="text-muted italic mb-0">Aucune description disponible.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne Droite: Fournisseur -->
            <div class="col-lg-4">
                <div class="cp-chart-card shadow-sm border-0 sticky-top" style="top: 2rem;">
                    <div class="cp-chart-header border-bottom py-3">
                        <h6 class="cp-chart-title mb-0"><i class="bi bi-truck me-2"></i>Fournisseur</h6>
                    </div>
                    <div class="p-4">
                        @if($stock->fournisseur)
                            <div class="text-center mb-4">
                                <div class="d-inline-flex p-4 bg-primary rounded-circle text-white mb-3 shadow-sm">
                                    <i class="bi bi-truck fs-1"></i>
                                </div>
                                <h5 class="mb-1">{{ $stock->fournisseur->nom }}</h5>
                                <p class="text-muted small">Fournisseur attitré</p>
                            </div>

                            <div class="list-group list-group-flush border-top">
                                <div class="list-group-item px-0 py-3 bg-transparent border-bottom-0">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-envelope text-muted me-3"></i>
                                        <span class="text-dark">{{ $stock->fournisseur->email ?? 'N/A' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-telephone text-muted me-3"></i>
                                        <span class="text-dark">{{ $stock->fournisseur->telephone ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-top">
                                <div class="d-grid">
                                    <a href="{{ route('super-admin.fournisseurs.show', $stock->fournisseur->id) }}" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-eye me-2"></i>Voir Fiche Fournisseur
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-person-x display-4 text-muted mb-3"></i>
                                <p class="text-muted italic">Aucun fournisseur associé</p>
                            </div>
                        @endif

                        <div class="mt-4 pt-4 border-top">
                            <div class="d-grid">
                                <form action="{{ route('super-admin.stocks.destroy', $stock->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Supprimer cet article de l’inventaire ?')">
                                        <i class="bi bi-trash me-2"></i>Supprimer du stock
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
