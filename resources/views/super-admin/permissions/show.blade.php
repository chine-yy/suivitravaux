@extends('layouts.super-admin')

@section('title', 'Détails Permission')
@section('breadcrumb')
    <a href="{{ route('super-admin.permissions.index') }}" class="app-breadcrumb-item">Permissions</a>
    <span class="app-breadcrumb-separator">/</span>
    <span class="app-breadcrumb-item">{{ $permission->nom }}</span>
@endsection

@section('content')
<div class="admin-card">
    <div class="admin-card-header d-flex justify-content-between align-items-center">
        <div>
            <h3 class="admin-card-title"><i class="bi bi-shield-check me-2"></i>{{ $permission->nom }}</h3>
            <p class="text-muted mb-0 small">Détails de la permission</p>
        </div>
        <a href="{{ route('super-admin.permissions.index') }}" class="admin-btn admin-btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="admin-card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nom</label>
                    <p class="form-control-plaintext">{{ $permission->nom }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Slug</label>
                    <p class="form-control-plaintext"><code class="text-primary">{{ $permission->slug }}</code></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Module</label>
                    <p class="form-control-plaintext">{{ ucwords(str_replace('-', ' ', $permission->module)) }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Action</label>
                    <p class="form-control-plaintext">
                        <span class="badge bg-{{ $permission->color ?? 'secondary' }}">
                            {{ \App\Models\Permission::$actionLabels[$permission->action] ?? ucfirst($permission->action) }}
                        </span>
                    </p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Groupe</label>
                    <p class="form-control-plaintext">{{ $permission->group }}</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold">Rôles associés</label>
                    <div class="d-flex flex-wrap gap-2">
                        @forelse($permission->roles as $role)
                            <span class="badge bg-primary">{{ $role->nom }}</span>
                        @empty
                            <span class="text-muted">Aucun rôle associé</span>
                        @endforelse
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Créée le</label>
                    <p class="form-control-plaintext">{{ $permission->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Mise à jour le</label>
                    <p class="form-control-plaintext">{{ $permission->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
