@extends('layouts.super-admin')

@section('title', 'Modifier l\'Équipe')

@section('breadcrumb')
    <a href="{{ route('super-admin.equipes.index') }}" class="text-decoration-none">Équipes</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Modifier</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier l'Équipe</h1>
                <p class="cp-page-subtitle">Mettez à jour les informations et les membres de l'équipe</p>
            </div>
            <a href="{{ route('super-admin.equipes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour à la liste
            </a>
        </div>

        @include('partials.alerts')

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-people me-2"></i>Informations de l'Équipe</h6>
                    </div>
                    <div class="p-4">
                        <form action="{{ route('super-admin.equipes.update', $equipe->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Nom de l'équipe <span class="text-danger">*</span></label>
                                <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" value="{{ old('nom', $equipe->nom) }}" required>
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Projet associé <span class="text-danger">*</span></label>
                                <select name="projet_id" class="form-select @error('projet_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner un projet</option>
                                    @foreach($projets as $projet)
                                        <option value="{{ $projet->id }}" {{ old('projet_id', $equipe->projet_id) == $projet->id ? 'selected' : '' }}>
                                            {{ $projet->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('projet_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $equipe->description) }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Statut</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="statut" id="statut_active" value="active" {{ old('statut', $equipe->statut) == 'active' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statut_active">Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="statut" id="statut_inactive" value="inactive" {{ old('statut', $equipe->statut) == 'inactive' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="statut_inactive">Inactive</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Choisir le chef d'équipe <span class="text-danger">*</span></label>
                                <select name="chef_equipe_id" class="form-select @error('chef_equipe_id') is-invalid @enderror" required id="chef_equipe_select">
                                    <option value="">Sélectionner un utilisateur...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('chef_equipe_id', $equipe->chef_equipe_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->role->nom ?? 'Sans rôle' }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted small">Le chef d'équipe sera automatiquement inclus comme membre.</div>
                                @error('chef_equipe_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Membres de l'équipe <span class="text-danger">*</span></label>
                                <div class="border rounded p-3">
                                    <div class="row">
                                        @php $selectedUsers = old('users', $equipe->users->pluck('id')->toArray()); @endphp
                                        @foreach($users as $user)
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="users[]" value="{{ $user->id }}" id="user_{{ $user->id }}" {{ in_array($user->id, $selectedUsers) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="user_{{ $user->id }}">
                                                        {{ $user->name }} <span class="text-muted small">({{ $user->role->nom ?? 'Sans rôle' }})</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('users') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex gap-2 pt-2 border-top">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check2 me-2"></i>Enregistrer
                                </button>
                                <a href="{{ route('super-admin.equipes.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Détails actuels</h6>
                    </div>
                    <div class="p-4">
                        <table class="table table-sm">
                            <tr>
                                <td class="text-muted">Créée le :</td>
                                <td>{{ $equipe->created_at->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Membres :</td>
                                <td>{{ $equipe->users->count() }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
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
