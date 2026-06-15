@extends('layouts.role-dynamique')

@section('title', 'Mes Permissions - ' . config('app.name'))

@section('breadcrumb')
<span class="cp-breadcrumb-item">Mon Profil</span>
<span class="cp-breadcrumb-separator">/</span>
<span class="cp-breadcrumb-item">Permissions</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-shield-check me-2"></i>Mes Permissions</h1>
                <p class="cp-page-subtitle">Gérez vos permissions d'accès ({{ $user->role->nom ?? 'Aucun rôle' }})</p>
            </div>
            <div>
                <a href="{{ route('role-dynamique.parametres') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Retour au profil
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('role-dynamique.permissions.update') }}" method="POST">
            @csrf

            @php
            $groupedPermissions = \App\Models\Permission::getGroupedPermissions();
            $checkedIds = $userPermissions;
            @endphp

            <div class="permission-matrix bg-white border rounded-3 overflow-hidden">
                @forelse($groupedPermissions as $groupName => $modules)
                    @include('partials.permission-matrix-body', ['groupedPermissions' => [$groupName => $modules], 'checkedIds' => $checkedIds])
                @empty
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-exclamation-triangle fs-3 text-warning mb-2"></i>
                    <p class="mb-0">Aucune permission disponible. Exécutez le seeder.</p>
                </div>
                @endforelse
            </div>

            <div class="d-flex gap-2 pt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-2"></i>Enregistrer les modifications
                </button>
                <a href="{{ route('role-dynamique.parametres') }}" class="btn btn-outline-secondary px-4">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/permissions-matrix.css') }}">
<style>
.text-green { color: #009A44 !important; }
.btn-outline-green { color: #009A44; border-color: #009A44; }
.btn-outline-green:hover { background: #009A44; color: #fff; }
.bg-secondary-soft { background-color: #f8f9fa !important; }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/permissions-matrix.js') }}"></script>
@endpush