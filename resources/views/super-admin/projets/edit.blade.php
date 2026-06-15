@extends('layouts.super-admin')

@section('title', 'Modifier le Projet')

@section('breadcrumb')
    <span class="text-muted">Modifier Projet</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier le Projet</h1>
                <p class="cp-page-subtitle">{{ $projet->nom }}</p>
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
                <form action="{{ route('super-admin.projets.update', $projet->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nom du Projet <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $projet->nom) }}" required>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>






                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de Début <span class="text-danger">*</span></label>
                            <input type="date" name="date_debut" class="form-control @error('date_debut') is-invalid @enderror" value="{{ old('date_debut', $projet->date_debut ? date('Y-m-d', strtotime($projet->date_debut)) : '') }}" required>
                            @error('date_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de Fin Prévue <span class="text-danger">*</span></label>
                            <input type="date" name="date_fin" class="form-control @error('date_fin') is-invalid @enderror" value="{{ old('date_fin', $projet->date_fin_prevue ? date('Y-m-d', strtotime($projet->date_fin_prevue)) : '') }}" required>
                            @error('date_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de Fin Réelle</label>
                            <input type="date" name="date_fin_reelle" class="form-control @error('date_fin_reelle') is-invalid @enderror" value="{{ old('date_fin_reelle', $projet->date_fin_reelle ? date('Y-m-d', strtotime($projet->date_fin_reelle)) : '') }}">
                            @error('date_fin_reelle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Statut</label>
                            <select name="statut" class="form-select @error('statut') is-invalid @enderror">
                                <option value="en_attente" {{ $projet->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="en_cours" {{ $projet->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="termine" {{ $projet->statut == 'termine' ? 'selected' : '' }}>Terminé</option>
                                <option value="en_retard" {{ $projet->statut == 'en_retard' ? 'selected' : '' }}>En retard</option>
                            </select>
                            @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Avancement (%)</label>
                            <input type="number" name="avancement" min="0" max="100" class="form-control @error('avancement') is-invalid @enderror" value="{{ old('avancement', $projet->avancement) }}">
                            @error('avancement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $projet->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('super-admin.projets.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
