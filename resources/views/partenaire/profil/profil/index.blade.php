@extends('layouts.partenaire')

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
                    <p class="cp-page-subtitle">Gérez vos informations personnelles</p>
                </div>
            </div>

            <div class="row g-4">
                {{-- Colonne principale --}}
                <div class="col-lg-7 col-xl-8 order-2 order-lg-1">

                    {{-- Informations personnelles + photo --}}
                    <div class="cp-chart-card">
                        <div class="cp-chart-header">
                            <h6 class="cp-chart-title"><i class="bi bi-person-badge me-2"></i>Informations personnelles</h6>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('partenaire.parametres.update') }}" method="POST"
                                enctype="multipart/form-data" id="profileForm">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nom</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $partenaire->name) }}" required>
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Prénom</label>
                                        <input type="text" name="prenom" class="form-control @error('prenom') is-invalid @enderror"
                                            value="{{ old('prenom', $partenaire->prenom) }}" required>
                                        @error('prenom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $partenaire->email) }}" required>
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Téléphone</label>
                                        <input type="tel" name="telephone" class="form-control"
                                            value="{{ old('telephone', $partenaire->telephone) }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Photo de profil</label>
                                        <input type="file" name="photo" id="photoInput"
                                            class="form-control @error('photo') is-invalid @enderror"
                                            accept="image/jpeg,image/png,image/jpg,image/gif">
                                        @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <small class="text-muted">Formats : JPEG, PNG, JPG, GIF. Max : 2 Mo.</small>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 pt-4 border-top mt-4">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-check2 me-2"></i>Enregistrer
                                    </button>
                                    <a href="{{ route('partenaire.dashboard') }}"
                                        class="btn btn-outline-secondary px-4">Annuler</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Changer le mot de passe --}}
                    <div class="cp-chart-card mt-4">
                        <div class="cp-chart-header">
                            <h6 class="cp-chart-title"><i class="bi bi-shield-lock me-2"></i>Sécurité — Changer le mot de passe</h6>
                        </div>
                        <div class="p-4">
                            <form method="POST" action="{{ route('partenaire.parametres.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $partenaire->name }}">
                                <input type="hidden" name="prenom" value="{{ $partenaire->prenom }}">
                                <input type="hidden" name="email" value="{{ $partenaire->email }}">
                                <input type="hidden" name="telephone" value="{{ $partenaire->telephone }}">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nouveau mot de passe</label>
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror"
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

                {{-- Aperçu du profil --}}
                <div class="col-lg-5 col-xl-4 order-1 order-lg-2">
                    <div class="cp-chart-card mb-4">
                        <div class="cp-chart-header">
                            <h6 class="cp-chart-title"><i class="bi bi-person me-2"></i>Aperçu du profil</h6>
                        </div>
                        <div class="p-4 text-center">
                            @php
                                $previewInitials = $partenaire ? strtoupper(substr($partenaire->prenom ?? '', 0, 1) . substr($partenaire->name ?? '', 0, 1)) : 'PA';
                            @endphp
                            {{-- Avatar --}}
                            @if($partenaire->photo_url)
                                <img src="{{ $partenaire->photo_url }}" id="profilePreview" alt="Photo de profil"
                                    class="rounded-circle mb-3"
                                    style="width: 100px; height: 100px; object-fit: cover;">
                                <div id="profilePreviewFallback" class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                    style="width:100px;height:100px;background:linear-gradient(135deg,#009A44,#d97706);color:#fff;font-size:2.5rem;font-weight:600;display:none!important;">
                                    {{ $previewInitials }}
                                </div>
                            @else
                                <img src="" id="profilePreview" alt="Photo de profil"
                                    class="rounded-circle mb-3"
                                    style="width:100px;height:100px;object-fit:cover;display:none;">
                                <div id="profilePreviewFallback" class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                    style="width:100px;height:100px;background:linear-gradient(135deg,#009A44,#d97706);color:#fff;font-size:2.5rem;font-weight:600;">
                                    {{ $previewInitials }}
                                </div>
                            @endif

                            <h5 class="fw-bold mb-1">{{ $partenaire->prenom }} {{ $partenaire->name }}</h5>
                            <p class="text-muted small mb-2">Partenaire</p>
                            <span class="badge mb-4" style="background-color: #009A44; color: #fff;">Actif</span>

                            @if($partenaire->photo_url)
                                <div class="mb-3" id="deletePhotoBtn">
                                    <form action="{{ route('partenaire.parametres.photo.destroy') }}" method="POST"
                                        onsubmit="return confirm('Voulez-vous vraiment supprimer votre photo de profil ? Cette action est irréversible.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-trash me-1"></i>Supprimer la photo
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="mb-3" id="deletePhotoBtn" style="display:none!important;"></div>
                            @endif

                            <div class="text-start mt-3">
                                <p class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i> {{ $partenaire->email }}</p>
                                <p class="mb-2"><i class="bi bi-telephone me-2 text-primary"></i>
                                    {{ $partenaire->telephone ?? 'Non renseigné' }}</p>
                                <p class="mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i> Inscrit depuis
                                    {{ $partenaire->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photoInput');
    const preview    = document.getElementById('profilePreview');
    const fallback   = document.getElementById('profilePreviewFallback');

    if (!photoInput) return;

    photoInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Vérification taille côté partenaire
        if (file.size > 2 * 1024 * 1024) {
            alert('L\'image ne doit pas dépasser 2 Mo.');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            if (fallback) {
                fallback.style.setProperty('display', 'none', 'important');
            }
            // Mise à jour avatar header en temps réel
            const headerImg = document.getElementById('headerAvatarImg');
            const headerFallback = document.getElementById('headerAvatarFallback');
            if (headerImg) {
                headerImg.src = e.target.result;
                headerImg.style.display = 'block';
                if (headerFallback) headerFallback.style.display = 'none';
            } else {
                // Pas encore d'image dans le header, la créer
                const wrapper = document.getElementById('headerAvatarWrapper');
                if (wrapper) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.id  = 'headerAvatarImg';
                    img.alt = 'Avatar';
                    img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:50%;';
                    wrapper.insertBefore(img, wrapper.firstChild);
                    if (headerFallback) headerFallback.style.setProperty('display', 'none', 'important');
                }
            }
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush