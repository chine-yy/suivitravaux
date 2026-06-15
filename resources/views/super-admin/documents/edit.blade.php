@extends('layouts.super-admin')

@section('title', 'Modifier Document')

@section('breadcrumb')
    <a href="{{ route('super-admin.documents.index') }}" class="text-decoration-none">Documents</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Modifier</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier Document</h1>
                <p class="cp-page-subtitle">Mettez à jour les informations du document</p>
            </div>
            <a href="{{ route('super-admin.documents.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Documents
            </a>
        </div>

        @include('partials.alerts')

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-file-earmark me-2"></i>Détails du document</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $document->nom) }}" required>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="contrat" {{ $document->type == 'contrat' ? 'selected' : '' }}>Contrat</option>
                                <option value="facture" {{ $document->type == 'facture' ? 'selected' : '' }}>Facture</option>
                                <option value="rapport" {{ $document->type == 'rapport' ? 'selected' : '' }}>Rapport</option>
                                <option value="photo" {{ $document->type == 'photo' ? 'selected' : '' }}>Photo</option>
                                <option value="plan" {{ $document->type == 'plan' ? 'selected' : '' }}>Plan</option>
                                <option value="autre" {{ $document->type == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="div_type_personnalise" style="display: {{ old('type', $document->type) == 'autre' ? 'block' : 'none' }};">
                            <label class="form-label fw-semibold text-green">Précisez le type <span class="text-danger">*</span></label>
                            <input type="text" name="type_personnalise" id="input_type_personnalise" class="form-control @error('type_personnalise') is-invalid @enderror" value="{{ old('type_personnalise', $document->type_personnalise) }}">
                            @error('type_personnalise') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet</label>
                            <select name="projet_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ $document->projet_id == $projet->id ? 'selected' : '' }}>{{ $projet->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Catégorie</label>
                            <input type="text" name="categorie" class="form-control" value="{{ old('categorie', $document->categorie) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Fichier (laisser vide pour garder l'actuel)</label>
                            <input type="file" name="fichier" class="form-control">
                            @if($document->fichier)
                                <small class="text-muted">Fichier actuel: {{ basename($document->fichier) }}</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="actif" {{ $document->statut == 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="archive" {{ $document->statut == 'archive' ? 'selected' : '' }}>Archivé</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $document->description }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="{{ route('super-admin.documents.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
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

        const initialValue = customTypeInput.value;

        function toggleCustomType() {
            if (typeSelect.value === 'autre') {
                customTypeDiv.style.display = 'block';
                customTypeInput.setAttribute('required', 'required');
                if (!customTypeInput.value && initialValue) {
                    customTypeInput.value = initialValue;
                }
            } else {
                customTypeDiv.style.display = 'none';
                customTypeInput.removeAttribute('required');
            }
        }

        typeSelect.addEventListener('change', toggleCustomType);
        toggleCustomType(); // Initial check on page load
    });
</script>
@endpush
