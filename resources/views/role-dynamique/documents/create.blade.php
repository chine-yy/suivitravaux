@extends('layouts.role-dynamique')

@section('title', 'Nouveau Document')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.documents.index') }}" class="text-decoration-none">Documents</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouveau Document</h1>
                <p class="cp-page-subtitle">Ajoutez un nouveau document au système</p>
            </div>
            <a href="{{ route('role-dynamique.documents.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Documents
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-file-earmark me-2"></i>Détails du document</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.documents.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" required>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="contrat" {{ old('type') == 'contrat' ? 'selected' : '' }}>Contrat</option>
                                <option value="facture" {{ old('type') == 'facture' ? 'selected' : '' }}>Facture</option>
                                <option value="rapport" {{ old('type') == 'rapport' ? 'selected' : '' }}>Rapport</option>
                                <option value="photo" {{ old('type') == 'photo' ? 'selected' : '' }}>Photo</option>
                                <option value="plan" {{ old('type') == 'plan' ? 'selected' : '' }}>Plan</option>
                                <option value="autre" {{ old('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="div_type_personnalise" style="display: {{ old('type') == 'autre' ? 'block' : 'none' }};">
                            <label class="form-label fw-semibold text-green">Précisez le type <span class="text-danger">*</span></label>
                            <input type="text" name="type_personnalise" id="input_type_personnalise" class="form-control @error('type_personnalise') is-invalid @enderror" value="{{ old('type_personnalise') }}">
                            @error('type_personnalise') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet</label>
                            <select name="projet_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>{{ $projet->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie</label>
                            <input type="text" name="categorie" class="form-control" value="{{ old('categorie') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Fichier</label>
                            <input type="file" name="fichier" class="form-control @error('fichier') is-invalid @enderror">
                            @error('fichier') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="actif" {{ old('statut', 'actif') == 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="archive" {{ old('statut') == 'archive' ? 'selected' : '' }}>Archivé</option>
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
                        <a href="{{ route('role-dynamique.documents.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.querySelector('select[name="type"]');
        const customTypeDiv = document.getElementById('div_type_personnalise');
        const customTypeInput = document.getElementById('input_type_personnalise');

        function toggleCustomType() {
            if (typeSelect.value === 'autre') {
                customTypeDiv.style.display = 'block';
                customTypeInput.setAttribute('required', 'required');
            } else {
                customTypeDiv.style.display = 'none';
                customTypeInput.removeAttribute('required');
                customTypeInput.value = '';
            }
        }

        typeSelect.addEventListener('change', toggleCustomType);
        toggleCustomType(); // Initial check on page load
    });
</script>
@endpush
