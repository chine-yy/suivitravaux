@extends('layouts.super-admin')

@section('title', 'Nouveau Rendez-vous')

@section('breadcrumb')
    <a href="{{ route('super-admin.rendezvous.index') }}" class="text-decoration-none">Rendez-vous</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouveau Rendez-vous</h1>
                <p class="cp-page-subtitle">Planifiez un nouveau rendez-vous</p>
            </div>
            <a href="{{ route('super-admin.rendezvous.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Rendez-vous
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-calendar-event me-2"></i>Détails du rendez-vous</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.rendezvous.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                            <input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror" value="{{ old('titre') }}" required>
                            @error('titre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" id="type_select" class="form-select" required>
                                <option value="reunion" {{ old('type') == 'reunion' ? 'selected' : '' }}>Réunion</option>
                                <option value="visite" {{ old('type') == 'visite' ? 'selected' : '' }}>Visite</option>
                                <option value="appel" {{ old('type') == 'appel' ? 'selected' : '' }}>Appel</option>
                                <option value="autre" {{ old('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="type_autre_container" style="{{ old('type') == 'autre' ? '' : 'display: none;' }}">
                            <label class="form-label fw-semibold">Précisez le type <span class="text-danger">*</span></label>
                            <input type="text" name="type_autre" class="form-control @error('type_autre') is-invalid @enderror" value="{{ old('type_autre') }}" placeholder="Ex: Déjeuner d'affaires, etc.">
                            @error('type_autre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date & Heure <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_heure" class="form-control @error('date_heure') is-invalid @enderror" value="{{ old('date_heure') }}" required>
                            @error('date_heure') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Durée (minutes)</label>
                            <input type="number" name="duree_minutes" class="form-control" value="{{ old('duree_minutes', 60) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lieu</label>
                            <input type="text" name="lieu" class="form-control" value="{{ old('lieu') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet</label>
                            <select name="projet_id" id="projet_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>{{ $projet->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="planifie" {{ old('statut', 'planifie') == 'planifie' ? 'selected' : '' }}>Planifié</option>
                                <option value="confirme" {{ old('statut') == 'confirme' ? 'selected' : '' }}>Confirmé</option>
                                <option value="termine" {{ old('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                                <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
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
                        <a href="{{ route('super-admin.rendezvous.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
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
        const typeSelect = document.getElementById('type_select');
        const typeAutreContainer = document.getElementById('type_autre_container');
        const typeAutreInput = typeAutreContainer.querySelector('input');

        function toggleTypeAutre() {
            if (typeSelect.value === 'autre') {
                typeAutreContainer.style.display = 'block';
                typeAutreInput.setAttribute('required', 'required');
            } else {
                typeAutreContainer.style.display = 'none';
                typeAutreInput.removeAttribute('required');
            }
        }

        typeSelect.addEventListener('change', toggleTypeAutre);
        toggleTypeAutre();
    });
</script>
@endpush

