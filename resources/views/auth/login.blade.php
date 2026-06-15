@extends('layouts.app')

@section('title', 'Connexion')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="brand-icon">
                    <i class="bi bi-building"></i>
                </div>
                <h1>{{ config('app.name') }}</h1>
                <p>Suivi Travaux — Connectez-vous à votre espace</p>
            </div>

            <div class="auth-card-body">
                @include('partials.alerts')

                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate id="loginForm">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="form-label">Adresse e-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="votre@email.com"
                                   required
                                   autocomplete="email"
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="form-text text-muted mt-1">
                            <i class="bi bi-info-circle me-1"></i>Entrez votre email, nous détecterons automatiquement votre type de compte (entreprise ou partenaire).
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">Mot de passe</label>
                            <a href="{{ route('password.request') }}" class="small text-primary forgot-link">
                                <i class="bi bi-question-circle me-1"></i>Mot de passe oublié ?
                            </a>
                        </div>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="••••••••"
                                   required
                                   autocomplete="current-password">
                            <button type="button" class="input-group-text btn-toggle-pw" data-target="password" style="cursor:pointer;">
                                <i class="bi bi-eye-fill" id="toggleIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="remember">Se souvenir de moi</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="loginBtn">
                        <i class="bi bi-box-arrow-in-right me-2"></i>
                        Se connecter
                    </button>
                </form>

                <div class="auth-footer">
                    @if(!\App\Models\Entreprise::hasRegisteredAccount())
                        Pas encore de compte ?
                        <a href="{{ route('entreprise.register') }}" class="text-primary fw-600">Creer un compte entreprise</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Maintenance active - inscription impossible --}}
<div class="modal fade" id="maintenanceRegisterModal" tabindex="-1" aria-labelledby="maintenanceRegisterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maintenanceRegisterModalLabel">
                    <i class="bi bi-tools text-primary me-2"></i>Mode Maintenance Actif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="bi bi-wrench-adjustable-circle" style="font-size: 3rem; color: #007a35;"></i>
                <h5 class="mt-3 mb-2">Inscription temporairement indisponible</h5>
                <p class="text-muted">
                    L'application est actuellement en maintenance.<br>
                    La création de nouveaux comptes est suspendue.<br>
                    Veuillez réessayer ultérieurement.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-circle me-2"></i>Compris
                </button>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
(() => {
    // Toggle password visibility
    document.querySelectorAll('.btn-toggle-pw').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.target);
            if (!target) return;
            target.type = target.type === 'password' ? 'text' : 'password';
            const icon = btn.querySelector('i');
            icon.classList.toggle('bi-eye-fill');
            icon.classList.toggle('bi-eye-slash-fill');
        });
    });

    // Partenaire-side validation
    const form = document.getElementById('loginForm');
    form.addEventListener('submit', function(e) {
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        let valid = true;

        // Remove previous partenaire-side errors
        form.querySelectorAll('.partenaire-error').forEach(el => el.remove());

        if (!email.value.trim()) {
            showError(email, 'L\'adresse email est obligatoire.');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            showError(email, 'Veuillez saisir une adresse email valide.');
            valid = false;
        }

        if (!password.value.trim()) {
            showError(password, 'Le mot de passe est obligatoire.');
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
            return;
        }

        // Show loading
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Connexion...';
    });

    function showError(input, message) {
        input.classList.add('is-invalid');
        const parent = input.closest('.input-group') || input.parentElement;
        // Don't add duplicate
        if (parent.querySelector('.partenaire-error')) return;
        const div = document.createElement('div');
        div.className = 'invalid-feedback d-block partenaire-error';
        div.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>' + message;
        parent.after(div);
    }

    // Clear error on input
    document.querySelectorAll('#loginForm input').forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('is-invalid');
            const next = input.closest('.input-group')?.nextElementSibling;
            if (next && next.classList.contains('partenaire-error')) next.remove();
        });
    });
})();
</script>
@endpush
