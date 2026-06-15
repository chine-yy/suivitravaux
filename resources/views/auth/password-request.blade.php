@extends('layouts.app')

@section('title', 'Mot de passe oublié')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="brand-icon">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h1>Mot de passe oublié</h1>
                <p>Saisissez votre email pour recevoir un code OTP</p>
            </div>

            <div class="auth-card-body">
                @include('partials.alerts')

                <form method="POST" action="{{ route('password.send-otp') }}" class="needs-validation" novalidate id="forgotForm">
                    @csrf

                    <div class="mb-3">
                        <label for="role" class="form-label">Type de compte</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                            <select class="form-select @error('role') is-invalid @enderror"
                                    id="role"
                                    name="role"
                                    required>
                                <option value="">Sélectionnez votre type de compte</option>
                                <optgroup label="🔷 Administration">
                                    <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                </optgroup>
                                @php
                                    $rolesDisponibles = \App\Models\Role::orderBy('nom')->get();
                                @endphp
                                @if($rolesDisponibles->count() > 0)
                                <optgroup label="👤 Rôles Personnalisés">
                                    @foreach($rolesDisponibles as $role)
                                    <option value="role_{{ $role->id }}" {{ old('role') == 'role_'.$role->id ? 'selected' : '' }}>
                                        {{ $role->nom }}
                                    </option>
                                    @endforeach
                                </optgroup>
                                @endif
                                <optgroup label="🔷 Administration">
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administration</option>
                                </optgroup>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

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
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                        <i class="bi bi-send-fill me-2"></i>
                        Envoyer le code OTP
                    </button>
                </form>

                <div class="auth-footer">
                    <a href="{{ route('login') }}" class="text-primary fw-600">
                        <i class="bi bi-arrow-left me-1"></i> Retour à la connexion
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/auth/password-request.js') }}"></script>
@endpush
