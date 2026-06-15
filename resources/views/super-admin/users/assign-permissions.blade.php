@extends('layouts.super-admin')

@section('title', 'Assigner les Permissions - ' . $admin->name)

@section('breadcrumb')
<a href="{{ route('super-admin.users.index') }}" class="text-decoration-none">Utilisateurs</a>
<span class="cp-breadcrumb-separator">/</span>
<span class="cp-breadcrumb-item">Permissions</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-shield-check me-2"></i>Gestion des Permissions</h1>
                <p class="cp-page-subtitle">Sélectionnez les permissions pour <strong>{{ $admin->name }}</strong> ({{ $admin->entreprise->nom_entreprise ?? 'Entreprise N/A' }})</p>
            </div>
            <div>
                <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>

        <form action="{{ route('super-admin.admins.permissions.update', $admin->id) }}" method="POST">
            @csrf

            @php
            $groupedPermissions = \App\Models\Permission::getGroupedPermissions();
            $checkedIds = $adminPermissions;
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
                <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary px-4">
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
