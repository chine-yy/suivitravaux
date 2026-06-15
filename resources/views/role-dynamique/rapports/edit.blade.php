@extends('layouts.role-dynamique')

@section('title', 'Modifier le Rapport')

@section('breadcrumb')
    <span class="text-muted"><a href="{{ route('role-dynamique.rapports.index') }}">Rapports</a></span> /
    <span class="text-muted">Modifier le Rapport</span>
@endsection

@section('content')
<div class="cp-content">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">Modifier le Rapport</h1>
            <p class="cp-page-subtitle">Mettez à jour les informations de votre rapport.</p>
        </div>
        <div>
            <a href="{{ route('role-dynamique.rapports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    @php
        $canOnlyEditStatus = $canOnlyEditStatus ?? false;
        $currentStatut = old('statut') ?? $rapport->statut;
        $approuveStatuts = ['valide', 'approuve'];
        $soumisStatuts = ['soumis', 'en_revision', 'en_revue'];
    @endphp

    @if(!$canOnlyEditStatus)
        <div class="cp-card">
            <div class="cp-card-header">
                <h5 class="mb-0">Détails du rapport #{{ $rapport->id }}</h5>
            </div>
            <div class="cp-card-body">
                <form action="{{ route('role-dynamique.rapports.update', $rapport->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="projet_id" class="form-label">Projet concerné <span class="text-danger">*</span></label>
                            <select name="projet_id" id="projet_id" class="form-select @error('projet_id') is-invalid @enderror" required>
                                <option value="">Sélectionnez un projet...</option>
                                @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}" {{ (old('projet_id') ?? $rapport->projet_id) == $projet->id ? 'selected' : '' }}>
                                        {{ $projet->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('projet_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="type" class="form-label">Type de Rapport <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                @php
                                    $currentType = old('type') ?? $rapport->type;
                                @endphp
                                <option value="journalier" {{ $currentType == 'journalier' ? 'selected' : '' }}>Journalier</option>
                                <option value="hebdomadaire" {{ $currentType == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                                <option value="mensuel" {{ $currentType == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                                <option value="incident" {{ $currentType == 'incident' ? 'selected' : '' }}>Incident</option>
                                <option value="fin_tache" {{ $currentType == 'fin_tache' ? 'selected' : '' }}>Fin de Tâche</option>
                                <option value="sous_tache" {{ $currentType == 'sous_tache' ? 'selected' : '' }}>Sous Tâche</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="titre" class="form-label">Titre du Rapport <span class="text-danger">*</span></label>
                            <input type="text" name="titre" id="titre" class="form-control @error('titre') is-invalid @enderror" value="{{ old('titre') ?? $rapport->titre }}" required>
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="contenu" class="form-label">Contenu Détaillé</label>
                            <textarea name="contenu" id="contenu" rows="6" class="form-control @error('contenu') is-invalid @enderror">{{ old('contenu') ?? $rapport->contenu }}</textarea>
                            @error('contenu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(auth()->user()->isAdminEntreprise() || auth()->user()->isSuperAdmin())
                        <div class="col-md-6">
                            <label for="statut" class="form-label">Statut du Rapport <span class="text-danger">*</span></label>
                            <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror">
                                <option value="soumis" {{ in_array($currentStatut, $soumisStatuts) ? 'selected' : '' }}>Soumis / En révision</option>
                                <option value="valide" {{ in_array($currentStatut, $approuveStatuts) ? 'selected' : '' }}>Validé</option>
                                <option value="rejete" {{ $currentStatut == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                <option value="brouillon" {{ $currentStatut == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="col-12 d-flex justify-content-end align-items-center gap-2 mt-4">
                            <a href="{{ route('role-dynamique.rapports.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-check-circle me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="modal fade" id="statusOnlyModal" tabindex="-1" aria-labelledby="statusOnlyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('role-dynamique.rapports.update', $rapport->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="statusOnlyModalLabel">Modifier le statut du rapport #{{ $rapport->id }}</h5>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-3">
                                Ce rapport ne vous appartient pas. Vous pouvez uniquement modifier son statut.
                            </p>

                            <label for="statut" class="form-label">Statut du Rapport <span class="text-danger">*</span></label>
                            <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                <option value="soumis" {{ in_array($currentStatut, $soumisStatuts) ? 'selected' : '' }}>Soumis / En révision</option>
                                <option value="valide" {{ in_array($currentStatut, $approuveStatuts) ? 'selected' : '' }}>Validé</option>
                                <option value="rejete" {{ $currentStatut == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                <option value="brouillon" {{ $currentStatut == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('role-dynamique.rapports.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-green">
                                <i class="bi bi-check-circle me-2"></i>Enregistrer le statut
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modalElement = document.getElementById('statusOnlyModal');
                if (!modalElement) return;

                const statusModal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static',
                    keyboard: false
                });

                statusModal.show();

                modalElement.addEventListener('hidden.bs.modal', function () {
                    window.location.href = "{{ route('role-dynamique.rapports.index') }}";
                });
            });
        </script>
    @endif
@endsection
