@extends('layouts.role-dynamique')

@section('title', 'Signaler un Incident')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.incidents.index') }}" class="text-decoration-none">Incidents</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-exclamation-triangle me-2 text-green"></i>Signaler un nouvel Incident</h1>
                <p class="cp-page-subtitle">Renseignez les informations de l'incident. Il sera rattaché au projet sélectionné.</p>
            </div>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-pencil-square me-2 text-green"></i>Détails de l'Incident</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.incidents.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="projet_id" class="form-label fw-semibold">Projet concerné <span class="text-danger">*</span></label>
                            <select name="projet_id" id="projet_id" class="form-select @error('projet_id') is-invalid @enderror" required>
                                <option value="" selected disabled>Choisir un projet...</option>
                                @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                        {{ $projet->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('projet_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="titre" class="form-label fw-semibold">Titre de l'incident <span class="text-danger">*</span></label>
                            <input type="text" name="titre" id="titre" class="form-control @error('titre') is-invalid @enderror"
                                value="{{ old('titre') }}" placeholder="Ex: Retard livraison matériel, Panne machine..." required>
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold d-block">Niveau de gravité <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-4 pt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gravite" id="gravite_faible" value="faible" {{ old('gravite') == 'faible' ? 'checked' : '' }} required>
                                    <label class="form-check-label text-info fw-medium" for="gravite_faible">
                                        <i class="bi bi-info-circle me-1"></i>Faible
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gravite" id="gravite_moyen" value="moyen" {{ old('gravite') == 'moyen' || !old('gravite') ? 'checked' : '' }}>
                                    <label class="form-check-label text-warning fw-medium" for="gravite_moyen">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Moyen
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gravite" id="gravite_critique" value="critique" {{ old('gravite') == 'critique' ? 'checked' : '' }}>
                                    <label class="form-check-label text-danger fw-medium" for="gravite_critique">
                                        <i class="bi bi-fire me-1"></i>Critique
                                    </label>
                                </div>
                            </div>
                            @error('gravite')
                                <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Description détaillée <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Décrivez précisément l'incident rencontré..." required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-3 pt-3 border-top mt-4 justify-content-end">
                        <a href="{{ route('role-dynamique.incidents.index') }}" class="btn btn-outline-secondary px-4 py-2">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-green btn-with-border px-5 py-2 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-2"></i>Enregistrer l'Incident
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .text-green { color: #009A44 !important; }
    .btn-green { background: #009A44; color: white; border: none; transition: all 0.3s ease; }
    .btn-green:hover { background: #007a35; color: white; }
    .btn-with-border { border: 2px solid #009A44 !important; }
    .form-control:focus, .form-select:focus { border-color: #009A44; box-shadow: 0 0 0 0.25rem rgba(0, 154, 68, 0.25); }
    .cp-breadcrumb-separator { margin: 0 0.5rem; color: #6c757d; }
    .cp-breadcrumb-item { color: #009A44; font-weight: 600; }
</style>
@endpush
