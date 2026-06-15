@extends('layouts.role-dynamique')

@section('title', 'Créer un Utilisateur')
@section('breadcrumb')
    <a href="{{ route('role-dynamique.users.index') }}" class="text-decoration-none">Utilisateurs</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Nouveau</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-person-plus me-2"></i>Créer un Utilisateur</h1>
                <p class="cp-page-subtitle">Renseignez les informations du nouvel utilisateur. Le mot de passe sera généré automatiquement.</p>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-lg-7">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-person-badge me-2"></i>Informations de l'Utilisateur</h6>
                    </div>
                    <div class="p-4">
                        <form action="{{ route('role-dynamique.users.store') }}" method="POST" id="createUserForm">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}" placeholder="Nom de famille" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" name="prenom" id="prenom" class="form-control @error('prenom') is-invalid @enderror"
                                        value="{{ old('prenom') }}" placeholder="Prénom" required>
                                    @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="mb-3 mt-3">
                                <label for="email" class="form-label fw-semibold">Adresse Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" placeholder="utilisateur@exemple.com" required>
                                </div>
                                @error('email')<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="telephone" class="form-label fw-semibold">Numéro de Téléphone</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="telephone" id="telephone" class="form-control @error('telephone') is-invalid @enderror"
                                        value="{{ old('telephone') }}" placeholder="+221 77 000 00 00">
                                </div>
                                @error('telephone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label for="role_id" class="form-label fw-semibold">Rôle assigné <span class="text-danger">*</span></label>
                                @if($roles->isEmpty())
                                    <div class="alert alert-danger">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        Aucun rôle disponible. <a href="{{ route('role-dynamique.roles.create') }}">Créer un rôle d'abord.</a>
                                    </div>
                                @else
                                <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un rôle</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->nom }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @endif
                            </div>

                            <div class="alert alert-info border-0" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                                    <div>
                                        <strong>Mot de passe automatique</strong><br>
                                        <small>Un mot de passe sécurisé sera généré automatiquement et affiché à la création du compte.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 pt-2">
                                <button type="submit" class="btn btn-primary px-4" {{ $roles->isEmpty() ? 'disabled' : '' }}>
                                    <i class="bi bi-person-plus me-2"></i>Créer l'Utilisateur
                                </button>
                                <a href="{{ route('role-dynamique.users.index') }}" class="btn btn-outline-secondary px-4">
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="cp-chart-card mb-3">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-shield-check me-2"></i>Rôles disponibles</h6>
                    </div>
                    <div class="p-4">
                        @if($roles->isEmpty())
                            <p class="text-muted text-center">Aucun rôle. <a href="{{ route('role-dynamique.roles.create') }}">Créer un rôle</a></p>
                        @else
                        @foreach($roles as $role)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <span class="badge rounded-pill" style="background:linear-gradient(135deg,#009A44,#007a35);">{{ $role->nom }}</span>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-green" data-bs-toggle="modal" data-bs-target="#modalRole{{ $role->id }}">
                                    <i class="bi bi-eye me-1"></i>Voir
                                </button>
                                @if((int) ($role->entreprise_id ?? 0) === (int) (auth()->user()->entreprise_id ?? 0))
                                <a href="{{ route('role-dynamique.roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil me-1"></i>Modifier
                                </a>
                                @endif
                            </div>
                        </div>

                        <!-- Modal Permissions du rôle -->
                        <div class="modal fade" id="modalRole{{ $role->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header py-2">
                                        <h6 class="modal-title"><i class="bi bi-shield-check me-2 text-green"></i>{{ $role->nom }}</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-3" style="max-height:60vh;overflow-y:auto;">
                                        @php
                                            $rolePermIds = $role->permissions->pluck('id')->toArray();
                                            $grouped = \App\Models\Permission::getGroupedPermissions();
                                        @endphp
                                        @if(count($rolePermIds) > 0)
                                            @foreach($grouped as $groupName => $modules)
                                                @php
                                                    $roleModules = [];
                                                    foreach ($modules as $modSlug => $modData) {
                                                        $activePerms = collect($modData['permissions'])->filter(fn($p) => in_array($p->id, $rolePermIds));
                                                        if ($activePerms->count() > 0) {
                                                            $roleModules[$modSlug] = ['nom' => $modData['nom'], 'icon' => $modData['icon'], 'perms' => $activePerms];
                                                        }
                                                    }
                                                @endphp
                                                @if(count($roleModules) > 0)
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bi bi-folder2-open text-green me-2"></i>
                                                        <strong class="small">{{ $groupName }}</strong>
                                                    </div>
                                                    @foreach($roleModules as $mod)
                                                    <div class="d-flex align-items-center mb-1 ms-3">
                                                        <i class="bi bi-{{ $mod['icon'] }} text-muted me-2 small"></i>
                                                        <span class="text-muted small me-2" style="min-width:120px;">{{ $mod['nom'] }}</span>
                                                        <div class="d-flex flex-wrap gap-1">
                                                            @foreach($mod['perms'] as $p)
                                                                <span class="badge bg-green-soft text-green small">{{ \App\Models\Permission::$actionLabels[$p->action] ?? ucfirst($p->action) }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div class="text-center py-3 text-muted">
                                                <i class="bi bi-shield-slash"></i>
                                                <p class="mt-1 mb-0 small">Aucune permission</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-green { color: #009A44 !important; }
.bg-green-soft { background-color: rgba(0, 154, 68, 0.1) !important; }
.btn-outline-green { color: #009A44; border-color: #009A44; }
.btn-outline-green:hover { background: #009A44; color: #fff; }
</style>
@endsection
