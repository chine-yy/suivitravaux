@extends('layouts.super-admin')

@section('title', 'Créer une Phase')

@section('breadcrumb')
<a href="{{ route('super-admin.phases.index') }}" class="text-decoration-none">Phases</a>
<span class="cp-breadcrumb-separator">/</span>
<span class="cp-breadcrumb-item">Nouveau</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Créer une Phase</h1>
                <p class="cp-page-subtitle">Renseignez les informations de la phase</p>
            </div>
        </div>

        @include('partials.alerts')

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-pencil-square me-2 text-green"></i>Informations de la Phase</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.phases.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="projet_id" class="form-label fw-semibold">Projet <span class="text-danger">*</span></label>
                            <select name="projet_id" id="projet_id" class="form-select @error('projet_id') is-invalid @enderror" required>
                                <option value="" selected disabled>-- Sélectionner un projet --</option>
                                @foreach($projets as $p)
                                    <option value="{{ $p->id }}" {{ old('projet_id') == $p->id ? 'selected' : '' }}>{{ $p->nom }}</option>
                                @endforeach
                            </select>
                            @error('projet_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="nom" class="form-label fw-semibold">Nom de la Phase <span class="text-danger">*</span></label>
                            <input type="text" name="nom" id="nom" class="form-control @error('nom') is-invalid @enderror"
                                value="{{ old('nom') }}" placeholder="Ex: Fondations, Gros Oeuvre..." required>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date_debut" class="form-label fw-semibold">Date de début</label>
                            <input type="date" name="date_debut" id="date_debut" class="form-control @error('date_debut') is-invalid @enderror"
                                value="{{ old('date_debut') }}">
                            @error('date_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date_fin_prevue" class="form-label fw-semibold">Date fin prévue</label>
                            <input type="date" name="date_fin_prevue" id="date_fin_prevue" class="form-control @error('date_fin_prevue') is-invalid @enderror"
                                value="{{ old('date_fin_prevue') }}">
                            @error('date_fin_prevue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-semibold">Description (Optionnel)</label>
                            <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Décrivez brièvement la phase...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-3 pt-3 border-top mt-4 justify-content-end">
                        <a href="{{ route('super-admin.phases.index') }}" class="btn btn-outline-secondary px-4 py-2">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                            <i class="bi bi-check-circle me-2"></i>Créer la Phase
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
