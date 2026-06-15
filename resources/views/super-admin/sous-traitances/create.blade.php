@extends('layouts.super-admin')

@section('title', 'Nouvelle Sous-Traitance')

@section('breadcrumb')
    <a href="{{ route('super-admin.sous-traitances.index') }}" class="text-decoration-none">Sous-Traitances</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouvelle Sous-Traitance</h1>
                <p class="cp-page-subtitle">Ajoutez une intervention de sous-traitance à un projet</p>
            </div>
            <a href="{{ route('super-admin.sous-traitances.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Sous-Traitances
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-building me-2"></i>Informations de la sous-traitance</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.sous-traitances.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Projet <span class="text-danger">*</span></label>
                            <select name="projet_id" class="form-select @error('projet_id') is-invalid @enderror" required>
                                <option value="">Sélectionner un projet</option>
                                @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                        {{ $projet->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('projet_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nom de l'entreprise <span class="text-danger">*</span></label>
                            <input type="text" name="nom_entreprise" class="form-control @error('nom_entreprise') is-invalid @enderror" value="{{ old('nom_entreprise') }}" required>
                            @error('nom_entreprise') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre d'employés <span class="text-danger">*</span></label>
                            <input type="number" name="nombre_employes" class="form-control @error('nombre_employes') is-invalid @enderror" value="{{ old('nombre_employes', 1) }}" min="1" required>
                            @error('nombre_employes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Description de la tâche</label>
                            <textarea name="description_tache" class="form-control" rows="3">{{ old('description_tache') }}</textarea>
                        </div>

                        <div class="col-md-12 mt-4"><h6 class="fw-semibold mb-3">Contact</h6></div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nom du contact</label>
                            <input type="text" name="contact_nom" class="form-control" value="{{ old('contact_nom') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Prénom du contact</label>
                            <input type="text" name="contact_prenom" class="form-control" value="{{ old('contact_prenom') }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Téléphone</label>
                            <input type="text" name="contact_telephone" class="form-control" value="{{ old('contact_telephone') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date début</label>
                            <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date fin</label>
                            <input type="date" name="date_fin" class="form-control" value="{{ old('date_fin') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="en_attente" {{ old('statut', 'en_attente') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="terminee" {{ old('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                                <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="{{ route('super-admin.sous-traitances.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
