@extends('layouts.role-dynamique')

@section('title', 'Nouveau Rapport')

@section('breadcrumb')
    <span class="text-muted"><a href="{{ route('role-dynamique.rapports.index') }}">Rapports</a></span> / 
    <span class="text-muted">Nouveau Rapport</span>
@endsection

@section('content')
<div class="cp-content">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">Envoyer un Rapport</h1>
            <p class="cp-page-subtitle">Rédigez et soumettez un nouveau rapport pour vos projets.</p>
        </div>
        <div>
            <a href="{{ route('role-dynamique.rapports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="cp-card">
        <div class="cp-card-header">
            <h5 class="mb-0">Formulaire de rapport</h5>
        </div>
        <div class="cp-card-body">
            <form action="{{ route('role-dynamique.rapports.store') }}" method="POST">
                @csrf
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="projet_id" class="form-label">Projet concerné <span class="text-danger">*</span></label>
                        <select name="projet_id" id="projet_id" class="form-select @error('projet_id') is-invalid @enderror" required>
                            <option value="">Sélectionnez un projet...</option>
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
                        <label for="type" class="form-label">Type de Rapport <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="journalier" {{ old('type') == 'journalier' ? 'selected' : '' }}>Journalier</option>
                            <option value="hebdomadaire" {{ old('type') == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                            <option value="mensuel" {{ old('type') == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                            <option value="incident" {{ old('type') == 'incident' ? 'selected' : '' }}>Incident</option>
                            <option value="fin_tache" {{ old('type') == 'fin_tache' ? 'selected' : '' }}>Fin de Tâche</option>
                            <option value="sous_tache" {{ old('type') == 'sous_tache' ? 'selected' : '' }}>Sous Tâche</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="titre" class="form-label">Titre du Rapport <span class="text-danger">*</span></label>
                        <input type="text" name="titre" id="titre" class="form-control @error('titre') is-invalid @enderror" value="{{ old('titre') }}" required placeholder="Ex: Avancement de la phase X">
                        @error('titre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="contenu" class="form-label">Contenu Détaillé</label>
                        <textarea name="contenu" id="contenu" rows="6" class="form-control @error('contenu') is-invalid @enderror" placeholder="Décrivez l'activité, les problèmes rencontrés, etc.">{{ old('contenu') }}</textarea>
                        @error('contenu')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 d-flex justify-content-end align-items-center gap-2 mt-4">
                        <!-- Pour les rôles dynamiques classiques, le statut de création initial est généralement "soumis" pour attente de validation -->
                        <input type="hidden" name="statut" value="soumis">
                        
                        <button class="btn btn-outline-secondary" type="submit" name="statut" value="brouillon" onclick="this.form.statut.value='brouillon'">
                            <i class="bi bi-save me-2"></i>Brouillon
                        </button>
                        <button type="submit" class="btn btn-green">
                            <i class="bi bi-send-fill me-2"></i>Envoyer le rapport
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
