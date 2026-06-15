@extends('layouts.role-dynamique')

@section('title', 'Modifier le Partenaire')

@section('breadcrumb')
    <span class="text-muted">Modifier Partenaire</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier le Partenaire</h1>
                <p class="cp-page-subtitle">{{ $partenaire->prenom }} {{ $partenaire->name }}</p>
            </div>
            <a href="{{ route('role-dynamique.partenaires.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-person me-2"></i>Informations du partenaire</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('role-dynamique.partenaires.update', $partenaire) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $partenaire->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('prenom') is-invalid @enderror" name="prenom" value="{{ old('prenom', $partenaire->prenom) }}" required>
                            @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $partenaire->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Téléphone</label>
                            <input type="text" class="form-control @error('telephone') is-invalid @enderror" name="telephone" value="{{ old('telephone', $partenaire->telephone) }}">
                            @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Projet à suivre <span class="text-danger">*</span></label>
                            <select class="form-select @error('projet_id') is-invalid @enderror" name="projet_id" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id', $partenaire->projet_id) == $projet->id ? 'selected' : '' }}>
                                    {{ $projet->nom }}
                                </option>
                                @endforeach
                            </select>
                            @error('projet_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('role-dynamique.partenaires.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
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
