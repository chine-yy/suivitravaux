@extends('layouts.super-admin')

@section('title', 'Modifier la Sous-Tâche')

@section('breadcrumb')
    <a href="{{ route('super-admin.sous-taches.index') }}" class="text-decoration-none">Sous-Tâches</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Modifier</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier la Sous-Tâche</h1>
                <p class="cp-page-subtitle">Mettez à jour les informations de la sous-tâche</p>
            </div>
            <a href="{{ route('super-admin.sous-taches.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list"></i> Liste des Sous-Tâches
            </a>
        </div>

        @include('partials.alerts')

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-task me-2"></i>Informations de la Sous-Tâche</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('super-admin.sous-taches.update', $sousTache->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                            <input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror" value="{{ old('titre', $sousTache->titre) }}" required>
                            @error('titre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tâche Parente <span class="text-danger">*</span></label>
                            <select name="tache_id" id="tache_id" class="form-select @error('tache_id') is-invalid @enderror" required>
                                <option value="">Sélectionner une tâche</option>
                                @foreach($taches as $t)
                                    <option value="{{ $t->id }}" data-projet-id="{{ $t->projet_id }}" data-phase-id="{{ $t->phase_id }}" {{ old('tache_id', $sousTache->tache_id) == $t->id ? 'selected' : '' }}>
                                        {{ $t->titre ?? 'Tâche #' . $t->id }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tache_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phase de la Tâche</label>
                            <div class="input-group">
                                <select name="phase_id" id="phase_id" class="form-select">
                                    <option value="">-- Sélectionner une phase --</option>
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="btn-new-phase" title="Nouvelle Phase">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Statut</label>
                            <select name="statut" class="form-select @error('statut') is-invalid @enderror">
                                <option value="en_attente" {{ $sousTache->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                <option value="en_cours" {{ $sousTache->statut == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="terminee" {{ $sousTache->statut == 'terminee' ? 'selected' : '' }}>Terminée</option>
                                <option value="bloquee" {{ $sousTache->statut == 'bloquee' ? 'selected' : '' }}>Bloquée</option>
                            </select>
                            @error('statut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Avancement (%)</label>
                            <input type="number" name="avancement" min="0" max="100" class="form-control @error('avancement') is-invalid @enderror" value="{{ old('avancement', $sousTache->avancement) }}">
                            @error('avancement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date de début</label>
                            <input type="date" name="date_debut" class="form-control @error('date_debut') is-invalid @enderror" value="{{ old('date_debut', $sousTache->date_debut?->format('Y-m-d')) }}">
                            @error('date_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Date fin prévue</label>
                            <input type="date" name="date_fin_prevue" class="form-control @error('date_fin_prevue') is-invalid @enderror" value="{{ old('date_fin_prevue', $sousTache->date_fin_prevue?->format('Y-m-d')) }}">
                            @error('date_fin_prevue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $sousTache->description }}</textarea>
                        </div>

                        <div class="col-md-12" id="user_id_wrapper">
                            <label class="form-label fw-semibold">Personne assignée</label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" id="user_id_select">
                                <option value="">-- Sélectionner une personne --</option>
                                @foreach($membres as $membre)
                                    <option value="{{ $membre->id }}" {{ old('user_id', $sousTache->user_id) == $membre->id ? 'selected' : '' }}>
                                        {{ $membre->name }} {{ $membre->prenom ? '(' . $membre->prenom . ')' : '' }} - {{ $membre->role->nom ?? 'Sans rôle' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="{{ route('super-admin.sous-taches.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Modal Nouvelle Phase -->
<div class="modal fade" id="modalPhase" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Phase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-new-phase">
                    @csrf
                    <input type="hidden" name="projet_id" id="modal_projet_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom de la Phase <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="btn-save-phase">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tacheSelect = document.getElementById('tache_id');
    const phaseSelect = document.getElementById('phase_id');
    const btnNewPhase = document.getElementById('btn-new-phase');
    const modalPhase = new bootstrap.Modal(document.getElementById('modalPhase'));
    const btnSavePhase = document.getElementById('btn-save-phase');

    const projetsData = @json($projets);

    function updatePhases(isInitial = false) {
        const selectedTache = tacheSelect.options[tacheSelect.selectedIndex];
        const projetId = selectedTache ? selectedTache.getAttribute('data-projet-id') : null;
        const currentPhaseId = selectedTache ? selectedTache.getAttribute('data-phase-id') : null;

        phaseSelect.innerHTML = '<option value="">-- Sélectionner une phase --</option>';

        if (projetId) {
            const projet = projetsData.find(p => p.id == projetId);
            if (projet && projet.phases) {
                projet.phases.forEach(phase => {
                    const option = document.createElement('option');
                    option.value = phase.id;
                    option.textContent = phase.nom;
                    if (isInitial && currentPhaseId == phase.id) {
                        option.selected = true;
                    }
                    phaseSelect.appendChild(option);
                });
            }
            btnNewPhase.disabled = false;
        } else {
            btnNewPhase.disabled = true;
        }
    }

    tacheSelect.addEventListener('change', () => updatePhases(true));
    updatePhases(true);

    btnNewPhase.addEventListener('click', function() {
        const selectedTache = tacheSelect.options[tacheSelect.selectedIndex];
        const projetId = selectedTache ? selectedTache.getAttribute('data-projet-id') : null;

        if (!projetId) {
            alert('Veuillez sélectionner une tâche parente d\'abord.');
            return;
        }
        document.getElementById('modal_projet_id').value = projetId;
        modalPhase.show();
    });

    btnSavePhase.addEventListener('click', function() {
        const form = document.getElementById('form-new-phase');
        const formData = new FormData(form);

        fetch("{{ route('super-admin.phases.store') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const projet = projetsData.find(p => p.id == formData.get('projet_id'));
                if (projet) {
                    if (!projet.phases) projet.phases = [];
                    projet.phases.push(data.phase);
                }

                const option = document.createElement('option');
                option.value = data.phase.id;
                option.textContent = data.phase.nom;
                option.selected = true;
                phaseSelect.appendChild(option);

                modalPhase.hide();
                form.reset();
            } else {
                alert('Erreur lors de la création de la phase');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue.');
        });
    });


});
</script>
@endpush
