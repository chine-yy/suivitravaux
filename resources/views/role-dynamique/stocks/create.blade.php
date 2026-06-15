@extends('layouts.role-dynamique')

@section('title', 'Nouveau Stock')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.stocks.index') }}" class="text-decoration-none">Stocks</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouveau Stock</h1>
                <p class="cp-page-subtitle">Ajoutez un nouvel élément au stock</p>
            </div>
            <a href="{{ route('role-dynamique.stocks.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Stocks
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-box-seam me-2"></i>Détails du stock</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.stocks.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Référence</label>
                            <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie</label>
                            <input type="text" name="categorie" class="form-control" value="{{ old('categorie') }}" placeholder="Ex: Outillage, Matériaux...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fournisseur</label>
                            <select name="fournisseur_id" class="form-select">
                                <option value="">-- Sélectionner --</option>
                                @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>{{ $fournisseur->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Quantité <span class="text-danger">*</span></label>
                            <input type="number" name="quantite" class="form-control @error('quantite') is-invalid @enderror" value="{{ old('quantite', 0) }}" required>
                            @error('quantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prix unitaire</label>
                            <input type="number" name="prix_unitaire" class="form-control" value="{{ old('prix_unitaire', 0) }}" step="0.01" min="0">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Emplacement</label>
                            <input type="text" name="emplacement" class="form-control" value="{{ old('emplacement') }}" placeholder="Ex: Entrepôt A, Rayon 3...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="disponible" {{ old('statut', 'disponible') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="epuise" {{ old('statut') == 'epuise' ? 'selected' : '' }}>Épuisé</option>
                                <option value="en_reapprovisionnement" {{ old('statut') == 'en_reapprovisionnement' ? 'selected' : '' }}>En réapprovisionnement</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="{{ route('role-dynamique.stocks.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
