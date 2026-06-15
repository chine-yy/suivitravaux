@extends('layouts.app')

@section('title', 'Vérification OTP')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/password-verify.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-card-header">
                <div class="brand-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h1>Vérification OTP</h1>
                <p>Saisissez le code envoyé à<br><strong>{{ $email }}</strong></p>
            </div>

            <div class="auth-card-body">
                @include('partials.alerts')


                <form method="POST" action="{{ route('password.verify-otp.submit') }}" id="otpForm">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <input type="hidden" name="role" value="{{ $role }}">
                    <input type="hidden" name="otp" id="otpHidden" value="">

                    <div class="mb-3">
                        <label for="role" class="form-label">Type de compte</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge-fill"></i></span>
                            <select class="form-select" id="role_confirm" disabled>
                                <optgroup label="🔷 Administration">
                                    @if($role == 'super_admin')
                                    <option selected>Super Admin</option>
                                    @endif
                                </optgroup>
                                <optgroup label="👤 Rôles Personnalisés">
                                    @php
                                        $rolesDisponibles = \App\Models\Role::orderBy('nom')->get();
                                    @endphp
                                    @foreach($rolesDisponibles as $r)
                                    @if('role_' . $r->id == $role)
                                    <option selected>{{ $r->nom }}</option>
                                    @endif
                                    @endforeach
                                </optgroup>
                                <optgroup label="🔷 Administration">
                                    @if($role == 'admin')
                                    <option selected>Administration</option>
                                    @endif
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="otp-inputs">
                        <input type="text" maxlength="1" class="otp-digit" data-index="0" inputmode="numeric" autofocus>
                        <input type="text" maxlength="1" class="otp-digit" data-index="1" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-digit" data-index="2" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-digit" data-index="3" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-digit" data-index="4" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-digit" data-index="5" inputmode="numeric">
                    </div>

                    @error('otp')
                        <div class="alert alert-danger py-2 text-center" style="font-size: 0.875rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i> {{ $message }}
                        </div>
                    @enderror

                    <button type="submit" class="btn btn-primary w-100" id="verifyBtn" disabled>
                        <i class="bi bi-check-circle me-2"></i>
                        Vérifier le code
                    </button>
                </form>

                <div class="auth-footer">
                    <div class="resend-link">
                        Code non reçu ?
                        <a href="{{ route('password.request') }}">Renvoyer un code</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/auth/password-verify.js') }}"></script>
@endpush
