@extends('layouts.super-admin')

@section('title', 'Nouveau Projet')

@section('breadcrumb')
    <span class="text-muted">Nouveau Projet</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouveau Projet</h1>
                <p class="cp-page-subtitle">Créer un nouveau projet</p>
            </div>
            <a href="{{ route('super-admin.projets.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        @include('partials.alerts')

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-briefcase me-2"></i>Détails du projet</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('super-admin.projets.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nom du Projet <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom') }}" placeholder="Ex: Construction Villa..." required autofocus>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Statut <span class="text-danger">*</span></label>
                            <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                <option value="">Choisir un statut</option>
                                <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En Attente</option>
                                <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En Cours</option>
                                <option value="termine" {{ old('statut') == 'termine' ? 'selected' : '' }}>Terminé</option>
                                <option value="en_retard" {{ old('statut') == 'en_retard' ? 'selected' : '' }}>En Retard</option>
                            </select>
                            @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Avancement initial (%)</label>
                            <input type="number" name="avancement" min="0" max="100" step="1" class="form-control @error('avancement') is-invalid @enderror" value="{{ old('avancement', 0) }}" placeholder="0">
                            @error('avancement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>





                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de Début <span class="text-danger">*</span></label>
                            <input type="date" name="date_debut" class="form-control @error('date_debut') is-invalid @enderror" value="{{ old('date_debut') }}" required>
                            @error('date_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de Fin Prévue <span class="text-danger">*</span></label>
                            <input type="date" name="date_fin" class="form-control @error('date_fin') is-invalid @enderror" value="{{ old('date_fin') }}" required>
                            @error('date_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Détails, objectifs...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('super-admin.projets.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Créer le projet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
