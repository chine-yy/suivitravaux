@extends('layouts.app')

@section('title', 'Nouveau mot de passe')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="brand-icon">
                    <i class="bi bi-key-fill"></i>
                </div>
                <h1>Nouveau mot de passe</h1>
                <p>Choisissez un nouveau mot de passe sécurisé</p>
            </div>

            <div class="auth-card-body">
                @include('partials.alerts')

                <form method="POST" action="{{ route('password.update') }}" class="needs-validation" novalidate id="resetForm">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="role" value="{{ $role }}">
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   placeholder="Min. 8 caractères"
                                   required
                                   autocomplete="new-password">
                            <button type="button" class="input-group-text btn-toggle-pw" data-target="password" style="cursor:pointer;">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="password-strength mt-2" id="pwStrength"></div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password"
                                   class="form-control"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   placeholder="Répétez le mot de passe"
                                   required
                                   autocomplete="new-password">
                            <button type="button" class="input-group-text btn-toggle-pw" data-target="password_confirmation" style="cursor:pointer;">
                                <i class="bi bi-eye-fill"></i>
                            </button>
                        </div>
                        <div id="pwMatch" class="mt-1" style="font-size: 0.8rem;"></div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-2"></i>
                        Réinitialiser le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/auth/password-reset.js') }}"></script>
@endpush
