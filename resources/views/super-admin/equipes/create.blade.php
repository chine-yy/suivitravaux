@extends('layouts.super-admin')

@section('title', 'Nouvelle Équipe')

@section('breadcrumb')
    <span class="text-muted">Nouvelle Équipe</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-people me-2"></i>Nouvelle Équipe</h1>
                <p class="cp-page-subtitle">Créer une équipe</p>
            </div>
            <a href="{{ route('super-admin.equipes.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        @include('partials.alerts')

        <div class="cp-chart-card">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-people me-2"></i>Détails de l'équipe</h6>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('super-admin.equipes.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">Nom de l'équipe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" name="nom" value="{{ old('nom') }}" placeholder="Ex: Équipe Alpha..." required>
                            @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Projet <span class="text-danger">*</span></label>
                            <select class="form-select @error('projet_id') is-invalid @enderror" name="projet_id" required>
                                <option value="">-- Sélectionner --</option>
                                @foreach($projets as $projet)
                                <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>{{ $projet->nom }}</option>
                                @endforeach
                            </select>
                            @error('projet_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Choisir le chef d'équipe <span class="text-danger">*</span></label>
                            <select name="chef_equipe_id" class="form-select @error('chef_equipe_id') is-invalid @enderror" required id="chef_equipe_select">
                                <option value="">Sélectionner un utilisateur...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('chef_equipe_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->role->nom ?? 'Sans rôle' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted small">Le chef d'équipe sera automatiquement inclus comme membre.</div>
                            @error('chef_equipe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Membres</label>
                            <div class="card bg-light border-0">
                                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                                    <div class="row">
                                        @foreach($users as $user)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="users[]" value="{{ $user->id }}" id="user_{{ $user->id }}" {{ is_array(old('users')) && in_array($user->id, old('users')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="user_{{ $user->id }}">
                                                    {{ $user->name }} <span class="text-muted small">({{ $user->role->nom ?? 'Sans rôle' }})</span>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @error('users') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('super-admin.equipes.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Créer l'Équipe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chefSelect = document.getElementById('chef_equipe_select');
        const userCheckboxes = document.querySelectorAll('input[name="users[]"]');

        function updateCheckboxes() {
            const selectedChefId = chefSelect.value;

            userCheckboxes.forEach(checkbox => {
                if (checkbox.value === selectedChefId && selectedChefId !== '') {
                    checkbox.checked = true;
                    // On ne le désactive pas pour qu'il soit bien envoyé dans le POST (ou on utilise readonly)
                    // Pour le visuel sans perturber le POST, on peut empêcher le clic :
                    checkbox.addEventListener('click', preventUncheck);
                    checkbox.parentElement.style.opacity = '0.6';
                    checkbox.parentElement.title = "Le chef d'équipe est membre de facto";
                } else {
                    checkbox.removeEventListener('click', preventUncheck);
                    checkbox.parentElement.style.opacity = '1';
                    checkbox.parentElement.title = "";
                }
            });
        }

        function preventUncheck(e) {
            e.preventDefault();
        }

        chefSelect.addEventListener('change', updateCheckboxes);
        updateCheckboxes();
    });
</script>
@endpush
