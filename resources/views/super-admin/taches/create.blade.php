@extends('layouts.super-admin')

@section('title', 'Nouvelle Tâche')

@section('breadcrumb')
    <span class="text-muted">Nouvelle Tâche</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-plus-circle me-2"></i>Nouvelle Tâche</h1>
                <p class="cp-page-subtitle">Créer une nouvelle tâche</p>
            </div>
            <a href="{{ route('super-admin.taches.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        @include('partials.alerts')

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-list-task me-2"></i>Détails de la tâche</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('super-admin.taches.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-12">
                            <label class="form-label fw-bold">Titre de la Tâche <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom_tache') is-invalid @enderror" name="nom_tache" value="{{ old('nom_tache') }}" required>
                            @error('nom_tache') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Projet <span class="text-danger">*</span></label>
                            <select class="form-select @error('projet_id') is-invalid @enderror" name="projet_id" id="projet_id" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>{{ $projet->nom }}</option>
                                @endforeach
                            </select>
                            @error('projet_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phase</label>
                            <div class="input-group">
                                <select class="form-select @error('phase_id') is-invalid @enderror" name="phase_id" id="phase_id">
                                    <option value="">-- Sélectionner une phase --</option>
                                </select>
                                <button type="button" class="btn btn-outline-primary" id="btn-new-phase" title="Nouvelle Phase">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            @error('phase_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>


                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de Début <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_debut') is-invalid @enderror" name="date_debut" value="{{ old('date_debut') }}" required>
                            @error('date_debut') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_fin') is-invalid @enderror" name="date_fin" value="{{ old('date_fin') }}" required>
                            @error('date_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Description facultatif <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Personne assignée</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="has_assigned" name="has_assigned" value="1" {{ old('has_assigned') ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_assigned">
                                    Affecter une personne à cette tâche
                                </label>
                            </div>
                            <select class="form-select @error('user_id') is-invalid @enderror" name="user_id" id="user_id_select" {{ old('has_assigned') ? '' : 'disabled' }}>
                                <option value="">-- Sélectionner une personne --</option>
                                @foreach($membres as $membre)
                                    <option value="{{ $membre->id }}" {{ old('user_id') == $membre->id ? 'selected' : '' }}>
                                        {{ $membre->name }} {{ $membre->prenom ? '(' . $membre->prenom . ')' : '' }} - {{ $membre->role->nom ?? 'Sans rôle' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('super-admin.taches.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Créer la Tâche
                        </button>
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date Début</label>
                            <input type="date" name="date_debut" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date Fin Prévue</label>
                            <input type="date" name="date_fin_prevue" class="form-control">
                        </div>
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
    const projetSelect = document.getElementById('projet_id');
    const phaseSelect = document.getElementById('phase_id');
    const btnNewPhase = document.getElementById('btn-new-phase');
    const modalPhase = new bootstrap.Modal(document.getElementById('modalPhase'));
    const btnSavePhase = document.getElementById('btn-save-phase');
    const hasAssignedCheckbox = document.getElementById('has_assigned');
    const userIdSelect = document.getElementById('user_id_select');

    const projetsData = @json($projets);

    if (hasAssignedCheckbox && userIdSelect) {
        hasAssignedCheckbox.addEventListener('change', function() {
            if (this.checked) {
                userIdSelect.disabled = false;
            } else {
                userIdSelect.disabled = true;
                userIdSelect.value = '';
            }
        });
    }

    function updatePhases() {
        const projetId = projetSelect.value;
        phaseSelect.innerHTML = '<option value="">-- Sélectionner une phase --</option>';

        if (projetId) {
            const projet = projetsData.find(p => p.id == projetId);
            if (projet && projet.phases) {
                projet.phases.forEach(phase => {
                    const option = document.createElement('option');
                    option.value = phase.id;
                    option.textContent = phase.nom;
                    if ("{{ old('phase_id') }}" == phase.id) {
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

    projetSelect.addEventListener('change', updatePhases);
    updatePhases();

    btnNewPhase.addEventListener('click', function() {
        if (!projetSelect.value) {
            alert('Veuillez sélectionner un projet d\'abord.');
            return;
        }
        document.getElementById('modal_projet_id').value = projetSelect.value;
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
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
