@extends('layouts.super-admin')

@section('title', 'Gestion des Permissions')
@section('breadcrumb')
    <span class="app-breadcrumb-item">Permissions</span>
@endsection

@section('content')
<div class="admin-card">
    <div class="admin-card-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="admin-card-title"><i class="bi bi-shield-check me-2"></i>Gestion des Permissions</h3>
            <p class="text-muted mb-0 small">{{ $totalPermissions }} permissions réparties sur {{ $totalRoles }} rôles</p>
        </div>
    </div>

    <div class="admin-card-body p-0">
        @forelse($groupedPermissions as $groupName => $groupPermissions)
            @php
                $groupTotal = collect($groupPermissions)->sum(fn ($module) => count($module['permissions']));
            @endphp
            <div class="permission-module-section">
                <div class="permission-module-header d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-folder text-green me-2"></i>
                        {{ $groupName }}
                        <span class="badge bg-secondary-soft text-secondary ms-2">{{ $groupTotal }}</span>
                    </h6>
                    <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#group-{{ Str::slug($groupName) }}">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>

                <div class="collapse show" id="group-{{ Str::slug($groupName) }}">
                    @foreach($groupPermissions as $moduleName => $moduleData)
                        <div class="p-3 border-bottom">
                            <h6 class="mb-2 fw-semibold">
                                <i class="bi bi-{{ $moduleData['icon'] ?? 'circle' }} me-2 text-primary"></i>
                                {{ $moduleData['nom'] }}
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($moduleData['permissions'] as $permission)
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-{{ $permission->color ?? 'secondary' }} me-1">
                                            {{ \App\Models\Permission::$actionLabels[$permission->action] ?? ucfirst($permission->action) }}
                                        </span>
                                        <span class="text-muted small">{{ $permission->nom }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shield-slash display-1 opacity-25"></i>
                <p class="mt-3">Aucune permission définie</p>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('styles')
<style>
.permission-module-section {
    border-bottom: 1px solid #dee2e6;
}
.permission-module-section:last-child {
    border-bottom: none;
}
.permission-module-header {
    background-color: #f8f9fa !important;
}
.bg-secondary-soft {
    background-color: #f8f9fa !important;
}
.text-green {
    color: #009A44 !important;
}
</style>
@endpush
