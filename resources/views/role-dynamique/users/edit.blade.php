@extends('layouts.role-dynamique')

@section('title', 'Modifier: '.$user->name)
@section('breadcrumb')
    <a href="{{ route('role-dynamique.users.index') }}" class="text-decoration-none">Utilisateurs</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Modifier</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier l'Utilisateur</h1>
                <p class="cp-page-subtitle">Modifiez les informations de {{ $user->name }} {{ $user->prenom }}</p>
            </div>
        </div>


        <div class="cp-chart-card" style="max-width:700px;">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title">Informations de l'Utilisateur</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="prenom" class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" id="prenom" class="form-control @error('prenom') is-invalid @enderror"
                                value="{{ old('prenom', $user->prenom) }}" required>
                            @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label fw-semibold">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" class="form-control"
                            value="{{ old('telephone', $user->telephone) }}">
                    </div>

                    <div class="mb-3">
                        <label for="photo" class="form-label fw-semibold">Photo de profil</label>
                        @if($user->photo_url)
                            <div class="mb-2">
                                <img src="{{ $user->photo_url }}" alt="Photo actuelle"
                                    style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
                            </div>
                        @endif
                        <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Formats: JPEG, PNG, JPG, GIF. Max: 2 Mo.</small>
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label fw-semibold">Rôle <span class="text-danger">*</span></label>
                        <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->nom }}
                            </option>
                            @endforeach
                        </select>
                        @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Statut</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1"
                                {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Compte actif</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check2 me-2"></i>Enregistrer
                        </button>
                        <a href="{{ route('role-dynamique.users.index') }}" class="btn btn-outline-secondary px-4">Annuler</a>
                        
                        <div class="ms-auto">
                            <button type="button" class="btn btn-outline-danger px-4" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                <i class="bi bi-trash me-2"></i>Supprimer le profil
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer définitivement le profil de <strong>{{ $user->name }} {{ $user->prenom }}</strong> ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('role-dynamique.users.destroy', $user) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
