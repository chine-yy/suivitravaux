@extends('layouts.super-admin')

@section('title', 'Mon Profil')

@section('breadcrumb')
    <span class="cp-breadcrumb-item">Mon Profil</span>
@endsection

@section('content')
    <div class="cp-dashboard">
        <div class="cp-content">
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title"><i class="bi bi-person me-2"></i>Mon Profil</h1>
                    <p class="cp-page-subtitle">Gérez vos informations et vos préférences</p>
                </div>
            </div>


            <div class="row g-4">
                <div class="col-lg-7 col-xl-8 order-2 order-lg-1">
                    <div class="cp-chart-card">
                        <div class="cp-chart-header">
                            <h6 class="cp-chart-title"><i class="bi bi-person-badge me-2"></i>Informations personnelles</h6>
                        </div>
                        <div class="p-4">
                            <form method="POST" action="{{ route('super-admin.parametres.update') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nom</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Prénom</label>
                                        <input type="text" class="form-control" name="prenom"
                                            value="{{ old('prenom', $user->prenom) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Téléphone</label>
                                        <input type="text" class="form-control" name="telephone"
                                            value="{{ old('telephone', $user->telephone) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Photo de profil</label>
                                        <input type="file" class="form-control" name="photo" accept="image/*">
                                        <small class="text-muted">Formats: JPEG, PNG, JPG, GIF. Max: 2 Mo.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Statut</label>
                                        <select class="form-select" name="is_active">
                                            <option value="1" {{ (string) old('is_active', (int) ($user->is_active ?? true)) === '1' ? 'selected' : '' }}>
                                                Actif
                                            </option>
                                            <option value="0" {{ (string) old('is_active', (int) ($user->is_active ?? true)) === '0' ? 'selected' : '' }}>
                                                Inactif
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 pt-4 border-top">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-check2 me-2"></i>Enregistrer
                                    </button>
                                    <a href="{{ route('super-admin.dashboard') }}"
                                        class="btn btn-outline-secondary px-4">Annuler</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Password change --}}
                    <div class="cp-chart-card mt-4">
                        <div class="cp-chart-header">
                            <h6 class="cp-chart-title"><i class="bi bi-shield-lock me-2"></i>Sécurité — Changer le mot de
                                passe</h6>
                        </div>
                        <div class="p-4">
                            <form method="POST" action="{{ route('super-admin.parametres.update') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="email" value="{{ $user->email }}">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nouveau mot de passe</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            name="password" placeholder="Laisser vide pour ne pas changer">
                                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Confirmer le mot de passe</label>
                                        <input type="password" class="form-control" name="password_confirmation"
                                            placeholder="Répétez le nouveau mot de passe">
                                    </div>
                                </div>

                                <div class="d-flex gap-2 pt-4 border-top mt-4">
                                    <button type="submit" class="btn btn-warning px-4">
                                        <i class="bi bi-lock me-2"></i>Mettre à jour le mot de passe
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 col-xl-4 order-1 order-lg-2">
                    <div class="cp-chart-card mb-4">
                        <div class="cp-chart-header">
                            <h6 class="cp-chart-title"><i class="bi bi-person me-2"></i>Aperçu du profil</h6>
                        </div>
                        <div class="p-4 text-center">
                            @if ($user->photo_url)
                                <img src="{{ $user->photo_url }}" class="rounded-circle mb-3"
                                    style="width: 100px; height: 100px; object-fit: cover;" alt="Avatar">
                            @else
                                @php
                                    $previewInitials = strtoupper(substr($user->prenom ?? '', 0, 1) . substr($user->name ?? '', 0, 1));
                                @endphp
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                    style="width: 100px; height: 100px; background: linear-gradient(135deg, #009A44, #d97706); color: #fff; font-size: 2.5rem; font-weight: 600;">
                                    {{ $previewInitials }}
                                </div>
                            @endif

                            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                            <p class="text-muted small mb-2">Super Admin</p>
                            <span class="badge mb-4"
                                style="background-color: #009A44; color: #fff;">{{ ($user->is_active ?? true) ? 'Actif' : 'Inactif' }}</span>

                            <div class="text-start mt-4">
                                <p class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i> {{ $user->email }}</p>
                                <p class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i>
                                    {{ $user->telephone ?? 'Non renseigné' }}</p>
                                <p class="mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i> Inscrit depuis
                                    {{ $user->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                            <div class="mt-2">
                                <form method="POST" action="{{ route('super-admin.parametres.photo.destroy') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm px-3"
                                        style="background-color: #009A44; color: #fff; border: none;"
                                        onclick="return confirm('Confirmer la suppression de la photo ?');">
                                        <i class="bi bi-trash me-1"></i>Supprimer photo
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection