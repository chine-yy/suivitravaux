@extends('layouts.partenaire')

@section('title', 'Paramètres - Mon Profil')

@section('content')
    @php
        $photoProfil = null;
        if ($partenaire && !empty($partenaire->photo)) {
            if (strpos($partenaire->photo, 'uploads/') === 0) {
                if (file_exists(public_path($partenaire->photo))) {
                    $photoProfil = asset($partenaire->photo);
                } else {
                    $photoProfil = asset('storage/' . $partenaire->photo);
                }
            } else {
                if (file_exists(public_path('uploads/profil-images/' . $partenaire->photo))) {
                    $photoProfil = asset('uploads/profil-images/' . $partenaire->photo);
                } else {
                    $photoProfil = asset('storage/uploads/profil-images/' . $partenaire->photo);
                }
            }
        }
    @endphp

    <div class="cp-dashboard">
        <div class="cp-content">
            <div class="cp-page-header">
                <div>
                    <h1 class="cp-page-title">Mon Profil</h1>
                    <p class="cp-page-subtitle">Gérez vos informations personnelles et vos préférences.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="cp-card-elevated">
                        <div class="cp-card-header">
                            <h5 class="mb-0">Informations personnelles</h5>
                        </div>
                        <div class="cp-card-body">
                            <form action="{{ route('partenaire.parametres.update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Nom</label>
                                        <input type="text" name="nom" class="form-control" value="{{ $partenaire->nom }}"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Prénom</label>
                                        <input type="text" name="prenom" class="form-control" value="{{ $partenaire->prenom }}"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ $partenaire->email }}"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-medium">Téléphone</label>
                                        <input type="tel" name="telephone" class="form-control"
                                            value="{{ $partenaire->telephone }}">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-medium">Photo de profil</label>
                                        <input type="file" name="photo" class="form-control cp-profile-file-input"
                                            accept="image/*">
                                        <div class="form-text">Formats acceptés : JPG, PNG. Taille max : 2Mo.</div>
                                    </div>
                                </div>

                                <div class="cp-card-footer mt-4">
                                    <button type="submit" class="btn btn-primary"
                                        style="background-color: var(--cp-primary); border-color: var(--cp-primary); color: white;">Enregistrer
                                        les changements</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="cp-card-elevated mb-4">
                        <div class="cp-card-header">
                            <h5 class="mb-0">Aperçu du profil</h5>
                        </div>
                        <div class="cp-card-body">
                            <div class="text-center mb-4">
                                @if($photoProfil)
                                    <img src="{{ $photoProfil }}" alt="Photo de profil" class="cp-avatar-large mx-auto mb-3"
                                        style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                                @else
                                    <div class="cp-avatar-large mx-auto mb-3">
                                        <i class="bi bi-person"></i>
                                    </div>
                                @endif
                                <h6>{{ $partenaire->prenom }} {{ $partenaire->nom }}</h6>
                                <span class="badge bg-secondary">Partenaire {{ config('app.name') }}</span>
                            </div>

                            <div class="cp-profile-info mt-4">
                                <div class="cp-info-item">
                                    <i class="bi bi-envelope"></i>
                                    <span>{{ $partenaire->email }}</span>
                                </div>
                                <div class="cp-info-item">
                                    <i class="bi bi-telephone"></i>
                                    <span>{{ $partenaire->telephone ?? 'Non renseigné' }}</span>
                                </div>
                                <div class="cp-info-item">
                                    <i class="bi bi-building"></i>
                                    <span>Projet : {{ $partenaire->projet->nom ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cp-card-elevated">
                        <div class="cp-card-header">
                            <h5 class="mb-0">Préférences</h5>
                        </div>
                        <div class="cp-card-body">
                            <div class="cp-settings-list">
                                <div class="cp-setting-row">
                                    <div>
                                        <div class="cp-setting-title">Activer les notifications</div>
                                        <div class="cp-setting-text">Affiche les notifications dans la section notification
                                            du site.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input cp-setting-switch" type="checkbox"
                                            id="cpSwitchSiteNotif">
                                    </div>
                                </div>

                                <div class="cp-setting-row">
                                    <div>
                                        <div class="cp-setting-title">Afficher sur l'appareil</div>
                                        <div class="cp-setting-text">Affiche aussi les notifications natives sur votre
                                            appareil.</div>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input cp-setting-switch" type="checkbox"
                                            id="cpSwitchDeviceNotif">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection