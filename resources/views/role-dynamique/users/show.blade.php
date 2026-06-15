@extends('layouts.role-dynamique')

@section('title', 'Profil: '.$user->name)
@section('breadcrumb')
    <a href="{{ route('role-dynamique.users.index') }}" class="text-decoration-none">Utilisateurs</a>
    <span class="cp-breadcrumb-separator">/</span>
    <span class="cp-breadcrumb-item">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <div class="cp-page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-person-circle me-2"></i>Détails Utilisateur</h1>
                <p class="cp-page-subtitle">Fiche descriptive de {{ $user->name }} {{ $user->prenom }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('role-dynamique.users.edit', $user) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Modifier
                </a>
                <a href="{{ route('role-dynamique.users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-lg-4">
                <div class="cp-chart-card text-center p-4">
                    @if($user->photo_url)
                        <img src="{{ $user->photo_url }}" class="rounded-circle mb-3"
                            style="width:80px;height:80px;object-fit:cover;" alt="Photo">
                    @else
                        <div class="mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3"
                            style="width:80px;height:80px;background:linear-gradient(135deg,#6366f1,#8b5cf6);font-size:2rem;color:#fff;font-weight:700;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr($user->prenom ?? '', 0, 1)) }}
                        </div>
                    @endif
                    <h4 class="fw-bold mb-1">{{ $user->name }} {{ $user->prenom }}</h4>
                    @if($user->role)
                        <span class="badge rounded-pill px-3 py-2 mb-3" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">{{ $user->role->nom }}</span>
                    @endif
                    <div class="mb-2">
                        @if($user->is_active ?? true)
                            <span class="badge bg-success px-3">Compte Actif</span>
                        @else
                            <span class="badge bg-danger px-3">Compte Inactif</span>
                        @endif
                    </div>
                    <hr>
                    <div class="text-start">
                        <p class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i> {{ $user->email }}</p>
                        <p class="mb-2"><i class="bi bi-telephone me-2 text-success"></i> {{ $user->telephone ?? 'Non renseigné' }}</p>
                        <p class="mb-0"><i class="bi bi-calendar me-2 text-muted"></i> Créé le {{ $user->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-key me-2"></i>Permissions du rôle "{{ $user->role->nom ?? 'N/A' }}"</h6>
                    </div>
                    <div class="p-4">
                        @if($user->role && $user->role->permissions->count() > 0)
                        <div class="row g-3">
                            @foreach($user->role->permissions as $perm)
                            <div class="col-md-6">
                                <div class="p-3 rounded border d-flex align-items-center gap-2"
                                    style="background:linear-gradient(135deg,#e8f5e9,#c8e6c9);">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span class="fw-semibold">{{ $perm->nom }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                            <p class="text-muted">Aucune permission associée à ce rôle.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
