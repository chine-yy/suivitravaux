@extends('layouts.super-admin')

@section('title', 'Inscrire des Partenaires')

@section('breadcrumb')
    <a href="{{ route('super-admin.partenaires.index') }}" class="text-decoration-none text-muted">Partenaires</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Inscription Multiple</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-person-plus me-2"></i>Inscrire des Partenaires</h1>
                <p class="cp-page-subtitle">Création de {{ $count }} compte(s) partenaire pour un projet</p>
            </div>
            <a href="{{ route('super-admin.partenaires.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>


        <form action="{{ route('super-admin.partenaires.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                <!-- Project Selection (Shared for all partenaires in this batch) -->
                <div class="col-12">
                    <div class="cp-chart-card mb-4 border-0 shadow-sm">
                        <div class="cp-chart-header py-3 bg-light">
                            <h6 class="cp-chart-title mb-0"><i class="bi bi-kanban me-2"></i>1. Sélection du Projet</h6>
                        </div>
                        <div class="p-4">
                            <label class="form-label fw-bold small">Projet à suivre <span class="text-danger">*</span></label>
                            <select class="form-select @error('projet_id') is-invalid @enderror" name="projet_id" required>
                                <option value="">-- Sélectionner un projet non attribué --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                    {{ $projet->nom }}
                                </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i> Seuls les projets n'ayant aucun partenaire associé sont affichés.
                            </div>
                            @error('projet_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Multiple Partenaires Info -->
                <div class="col-12">
                    <div class="cp-chart-card border-0 shadow-sm">
                        <div class="cp-chart-header py-3 bg-light">
                            <h6 class="cp-chart-title mb-0"><i class="bi bi-people me-2"></i>2. Informations des Partenaires ({{ $count }})</h6>
                        </div>
                        <div class="p-4">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Type de compte</label>
                                    <input type="text" class="form-control" value="Partenaire" readonly style="background-color: #f8f9fa;">
                                </div>
                            </div>
                            @for($i = 0; $i < $count; $i++)
                            <div class="partenaire-form-block {{ $i > 0 ? 'mt-5 pt-5 border-top' : '' }}">
                                <h6 class="fw-bold text-green mb-4"><i class="bi bi-person-circle me-2"></i>Partenaire #{{ $i + 1 }}</h6>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Nom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error("nom.$i") is-invalid @enderror" name="nom[]" value="{{ old("nom.$i") }}" required placeholder="Nom de famille">
                                        @error("nom.$i") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error("prenom.$i") is-invalid @enderror" name="prenom[]" value="{{ old("prenom.$i") }}" required placeholder="Prénom">
                                        @error("prenom.$i") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error("email.$i") is-invalid @enderror" name="email[]" value="{{ old("email.$i") }}" required placeholder="exemple@email.com">
                                        @error("email.$i") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Téléphone</label>
                                        <input type="text" class="form-control @error("telephone.$i") is-invalid @enderror" name="telephone[]" value="{{ old("telephone.$i") }}" placeholder="+225 ...">
                                        @error("telephone.$i") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 d-flex justify-content-end gap-2">
                <a href="{{ route('super-admin.partenaires.index') }}" class="btn btn-outline-secondary px-5 py-2">
                    Annuler
                </a>
                <button type="submit" class="btn btn-primary text-white px-5 py-2 fw-bold shadow-sm">
                    <i class="bi bi-check-circle me-2"></i>Inscrire les Partenaires
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
