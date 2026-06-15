@extends('layouts.role-dynamique')

@section('title', 'Modifier le Rôle')

@section('breadcrumb')
<a href="{{ route('role-dynamique.roles.index') }}" class="text-decoration-none">Rôles</a>
<span class="cp-breadcrumb-separator">/</span>
<span class="cp-breadcrumb-item">Modifier</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-pencil-square me-2"></i>Modifier le Role</h1>
                <p class="cp-page-subtitle">Modifiez le role <strong>{{ $role->nom }}</strong> et ses permissions</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('role-dynamique.configuration.logs') }}" class="btn btn-outline-info">
                    <i class="bi bi-file-earmark-text me-2"></i>Voir les logs
                </a>
                <a href="{{ route('role-dynamique.roles.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>
        </div>


        <div class="row g-4">
            <div class="col-lg-8">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-shield-check me-2"></i>Modification du Rôle</h6>
                    </div>
                    <div class="p-4">
                        <form action="{{ route('role-dynamique.roles.update', $role) }}" method="POST">
                            @csrf @method('PUT')

                            @php
                                $isAdminEntreprise = $role->nom === 'Administrateur Entreprise';
                            @endphp
                            <div class="mb-4">
                                <label for="nom" class="form-label fw-semibold">Nom du Rôle <span class="text-danger">*</span></label>
                                <input type="text" name="nom" id="nom" class="form-control form-control-lg @error('nom') is-invalid @enderror" 
                                    value="{{ old('nom', $role->nom) }}" 
                                    {{ $isAdminEntreprise ? 'readonly' : '' }} 
                                    {{ $isAdminEntreprise ? 'style="background-color: #e9ecef;"' : '' }}
                                    required>
                                @if($isAdminEntreprise)
                                    <small class="text-muted">Le nom du rôle "Administrateur Entreprise" ne peut pas être modifié.</small>
                                @endif
                                @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold"><i class="bi bi-shield-lock me-2"></i>Permissions d'accès</label>
                                <p class="text-muted small mb-3">Cochez les autorisations pour chaque module.</p>

                                @php
                                $groupedPermissions = \App\Models\Permission::getGroupedPermissions();
                                $rolePermissionIds = $role->permissions->pluck('id')->toArray();
                                $checkedIds = old('permissions', $rolePermissionIds);
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
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check-lg me-2"></i>Enregistrer
                                </button>
                                <a href="{{ route('role-dynamique.roles.index') }}" class="btn btn-outline-secondary px-4">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="cp-chart-card mb-3">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-people me-2"></i>Utilisateurs avec ce rôle</h6>
                    </div>
                    <div class="p-4">
                        @php $usersRole = $role->users()->get(); @endphp
                        @if($usersRole->count() > 0)
                        <ul class="list-unstyled mb-0">
                            @foreach($usersRole as $u)
                            <li class="d-flex align-items-center gap-2 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-size:.75rem;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold small">{{ $u->name }} {{ $u->prenom ?? '' }}</div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ $u->email }}</div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-muted text-center mb-0">Aucun utilisateur associé à ce rôle.</p>
                        @endif
                    </div>
                </div>

                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Règles des permissions</h6>
                    </div>
                    <div class="p-3">
                        <div class="alert alert-info py-2 px-3 mb-3 small">
                            <i class="bi bi-eye me-1"></i>
                            <strong>Voir</strong> est <span class="text-danger fw-bold">obligatoire</span> pour activer toute autre action.
                        </div>
                        <ul class="text-muted small mb-3 ps-3">
                            <li>Cocher <em>Créer / Modifier / Supprimer</em>… → <strong>Voir</strong> s'active automatiquement</li>
                            <li>Décocher <strong>Voir</strong> → toutes les autres actions se désactivent</li>
                        </ul>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary"><i class="bi bi-eye me-1"></i>Voir *</span>
                            <span class="badge bg-success"><i class="bi bi-plus me-1"></i>Créer</span>
                            <span class="badge bg-warning text-dark"><i class="bi bi-pencil me-1"></i>Modifier</span>
                            <span class="badge bg-danger"><i class="bi bi-trash me-1"></i>Supprimer</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/permissions-matrix.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/permissions-matrix.js') }}"></script>
@endpush
