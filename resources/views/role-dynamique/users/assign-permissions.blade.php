@extends('layouts.role-dynamique')

@section('title', 'Assigner les Permissions - ' . $admin->name)

@section('breadcrumb')
<a href="{{ route('role-dynamique.users.index') }}" class="text-decoration-none">Utilisateurs</a>
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
                <a href="{{ route('role-dynamique.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>

        <form action="{{ route('role-dynamique.admins.permissions.update', $admin->id) }}" method="POST">
            @csrf

            @php
            $groupedPermissions = \App\Models\Permission::getGroupedPermissions();
            @endphp

            <div class="permission-matrix bg-white border rounded-3 overflow-hidden">
                @forelse($groupedPermissions as $groupName => $modules)
                @php
                $groupIcon = match(true) {
                    str_contains($groupName, 'Gestion Globale')     => 'shield-lock',
                    str_contains($groupName, 'Projets')             => 'kanban',
                    str_contains($groupName, 'Ressources Humaines') => 'people',
                    str_contains($groupName, 'Partenaires')             => 'person-badge',
                    str_contains($groupName, 'Interventions')       => 'wrench',
                    str_contains($groupName, 'Fournisseurs')        => 'truck',
                    str_contains($groupName, 'Rendez-vous')         => 'calendar-event',
                    str_contains($groupName, 'Documents')           => 'folder2-open',
                    str_contains($groupName, 'Communication')       => 'chat-dots',
                    default                                         => 'grid',
                };
                @endphp
                <div class="permission-group" data-group="{{ \Illuminate\Support\Str::slug($groupName) }}">
                    <!-- Group Header -->
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom group-header bg-light cursor-pointer" data-toggle="{{ \Illuminate\Support\Str::slug($groupName) }}">
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                            <i class="bi bi-{{ $groupIcon }} text-green me-2 fs-5"></i>
                            {{ $groupName }}
                        </h6>
                        <div class="d-flex align-items-center gap-3">
                            <span class="badge bg-secondary-soft text-secondary rounded-pill px-2 py-1">{{ count($modules) }} modules</span>
                            <button type="button" class="btn btn-sm btn-outline-green group-select-all px-3" data-group="{{ \Illuminate\Support\Str::slug($groupName) }}" data-full-text="true">
                                <i class="bi bi-check-all me-1"></i>Tout cocher
                            </button>
                            <i class="bi bi-chevron-down text-muted chevron-icon"></i>
                        </div>
                    </div>

                    <!-- Modules List -->
                    <div class="group-content">
                        @foreach($modules as $moduleSlug => $moduleData)
                        <div class="d-flex flex-wrap flex-md-nowrap align-items-center p-3 border-bottom module-row">
                            <div class="module-name fw-medium text-secondary d-flex align-items-center mb-2 mb-md-0">
                                <i class="bi bi-{{ $moduleData['icon'] }} me-2 fs-5 text-muted opacity-75"></i>
                                {{ $moduleData['nom'] }}
                            </div>
                            <div class="d-flex flex-wrap gap-2 flex-grow-1 border-start ps-md-3 border-opacity-25">
                                @foreach($moduleData['permissions'] as $permission)
                                @php $isChecked = in_array($permission->id, $adminPermissions); @endphp
                                <label class="perm-switch {{ $isChecked ? 'active' : '' }}" data-action="{{ $permission->action }}">
                                    <input class="d-none" type="checkbox" name="permissions[]" value="{{ $permission->id }}" data-group="{{ \Illuminate\Support\Str::slug($groupName) }}" {{ $isChecked ? 'checked' : '' }}>
                                    <span class="perm-btn">
                                        <i class="bi bi-{{ $permission->action == 'view' ? 'eye' : ($permission->action == 'create' ? 'plus' : ($permission->action == 'delete' ? 'trash' : ($permission->action == 'edit' ? 'pencil' : 'circle'))) }} me-1"></i>
                                        {{ \App\Models\Permission::$actionLabels[$permission->action] ?? ucfirst($permission->action) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
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
                <a href="{{ route('role-dynamique.users.index') }}" class="btn btn-outline-secondary px-4">
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
