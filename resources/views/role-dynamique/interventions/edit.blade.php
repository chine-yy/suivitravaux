@extends('layouts.role-dynamique')

@section('title', 'Modifier Intervention')

@section('breadcrumb')
    <a href="{{ route('role-dynamique.interventions.index') }}" class="text-decoration-none">Interventions</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Modifier</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier Intervention</h1>
                <p class="cp-page-subtitle">Mettez à jour les informations de l'intervention</p>
            </div>
            <a href="{{ route('role-dynamique.interventions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Interventions
            </a>
        </div>


        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-tools me-2"></i>Détails de l'intervention</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.interventions.update', $intervention->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                            <select name="type" id="type_select" class="form-select" required onchange="toggleTypeAutre()">
                                <option value="installation" {{ $intervention->type == 'installation' ? 'selected' : '' }}>Installation</option>
                                <option value="maintenance" {{ $intervention->type == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="reparation" {{ $intervention->type == 'reparation' ? 'selected' : '' }}>Réparation</option>
                                <option value="inspection" {{ $intervention->type == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                <option value="autre" {{ $intervention->type == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="type_autre_container" style="display: {{ $intervention->type == 'autre' ? 'block' : 'none' }};">
                            <label class="form-label fw-semibold">Type (Préciser) <span class="text-danger">*</span></label>
                            <input type="text" name="type_autre" class="form-control @error('type_autre') is-invalid @enderror" value="{{ old('type_autre', $intervention->type_autre) }}" placeholder="Spécifier le type">
                            @error('type_autre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <script>
                            function toggleTypeAutre() {
                                var select = document.getElementById('type_select');
                                var container = document.getElementById('type_autre_container');
                                if (select.value === 'autre') {
                                    container.style.display = 'block';
                                    container.querySelector('input').setAttribute('required', 'required');
                                } else {
                                    container.style.display = 'none';
                                    container.querySelector('input').removeAttribute('required');
                                }
                            }
                            // Run on load to ensure correct state if validation failed
                            window.addEventListener('DOMContentLoaded', toggleTypeAutre);
                        </script>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date Intervention <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="date_intervention" class="form-control" value="{{ $intervention->date_intervention ? date('Y-m-d\TH:i', strtotime($intervention->date_intervention)) : '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mission (Type) <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mission_type" id="mission_tache" value="tache" {{ $intervention->sous_tache_id ? '' : 'checked' }} onchange="toggleMissionFields()">
                                    <label class="form-check-label" for="mission_tache">Tâche</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mission_type" id="mission_sous_tache" value="sous_tache" {{ $intervention->sous_tache_id ? 'checked' : '' }} onchange="toggleMissionFields()">
                                    <label class="form-check-label" for="mission_sous_tache">Sous-tâche</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold" id="mission_display_label">{{ $intervention->sous_tache_id ? 'Sous-tâche' : 'Tâche' }}</label>
                            
                            <div id="tache_container" style="display: {{ $intervention->sous_tache_id ? 'none' : 'block' }};">
                                <select name="tache_id" class="form-select">
                                    <option value="">-- Sélectionner une tâche --</option>
                                    @foreach($taches as $tache)
                                    <option value="{{ $tache->id }}" {{ $intervention->tache_id == $tache->id ? 'selected' : '' }}>{{ $tache->titre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="sous_tache_container" style="display: {{ $intervention->sous_tache_id ? 'block' : 'none' }};">
                                <select name="sous_tache_id" class="form-select">
                                    <option value="">-- Sélectionner une sous-tâche --</option>
                                    @foreach($sousTaches as $st)
                                    <option value="{{ $st->id }}" {{ $intervention->sous_tache_id == $st->id ? 'selected' : '' }}>{{ $st->titre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <script>
                            function toggleMissionFields() {
                                var isTache = document.getElementById('mission_tache').checked;
                                document.getElementById('mission_display_label').innerText = isTache ? 'Tâche' : 'Sous-tâche';
                                document.getElementById('tache_container').style.display = isTache ? 'block' : 'none';
                                document.getElementById('sous_tache_container').style.display = isTache ? 'none' : 'block';
                            }
                            window.addEventListener('DOMContentLoaded', toggleMissionFields);
                        </script>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Projet</label>
                            <select name="projet_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ $intervention->projet_id == $projet->id ? 'selected' : '' }}>{{ $projet->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Personnels</label>
                            <select name="technicien_id" class="form-select">
                                <option value="">-- Aucun --</option>
                                @foreach($techniciens as $technicien)
                                <option value="{{ $technicien->id }}" {{ $intervention->technicien_id == $technicien->id ? 'selected' : '' }}>
                                    {{ $technicien->name }} {{ $technicien->prenom }} - {{ $technicien->role->nom ?? 'Sans rôle' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="planifie" {{ $intervention->statut == 'planifie' ? 'selected' : '' }}>Planifié</option>
                                <option value="en_cours" {{ $intervention->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="termine" {{ $intervention->statut == 'termine' ? 'selected' : '' }}>Terminé</option>
                                <option value="annule" {{ $intervention->statut == 'annule' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $intervention->description }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 pt-4 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="{{ route('role-dynamique.interventions.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
