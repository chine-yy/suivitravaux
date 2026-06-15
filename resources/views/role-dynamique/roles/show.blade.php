@extends('layouts.role-dynamique')

@section('title', 'Détails du Rôle - ' . $role->nom)

@section('breadcrumb')
    <span class="text-muted"><a href="{{ route('role-dynamique.roles.index') }}">Rôles</a></span>
    <span class="mx-2 text-muted">/</span>
    <span class="text-muted">Détails</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <!-- Header -->
        <div class="cp-page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-shield-check me-2"></i>Détails du Rôle: {{ $role->nom }}</h1>
                <p class="cp-page-subtitle">Visualisez les informations et les permissions de ce rôle</p>
            </div>
            <a href="{{ route('role-dynamique.roles.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-2"></i>Retour
            </a>
        </div>

        <div class="row g-4">
            <!-- Informations générales -->
            <div class="col-lg-4">
                <div class="cp-chart-card mb-4 h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-info-circle me-2"></i>Informations générales</h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Nom du Rôle</label>
                            <div class="fs-5 fw-bold text-dark">{{ $role->nom }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Slug</label>
                            <div><code class="text-primary">{{ $role->slug }}</code></div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small fw-bold">Nombre d'Utilisateurs</label>
                            <div class="fs-5 fw-bold text-success">{{ $role->users->count() }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="text-muted small fw-bold">Nombre de Permissions</label>
                            <div class="fs-5 fw-bold text-warning">{{ $role->permissions->count() }}</div>
                        </div>
                        
                        @if(auth()->user()->hasPermission('edit-roles-permissions'))
                        <div class="d-grid gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('role-dynamique.roles.edit', $role) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>Modifier les permissions
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Permissions associées -->
            <div class="col-lg-8">
                <div class="cp-chart-card mb-4">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-key me-2"></i>Permissions Associées</h6>
                    </div>
                    <div class="p-4">
                        @if($role->permissions->isEmpty())
                            <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning-emphasis">
                                <i class="bi bi-exclamation-triangle me-2"></i>Ce rôle ne possède aucune permission pour le moment.
                            </div>
                        @else
                            @php
                            $groupes = [
                                'Projet & Exécution' => ['projet', 'tache', 'sous_tache', 'incident', 'phase', 'equipe'],
                                'Partenaires & Ventes' => ['partenaire', 'contrat', 'facture', 'satisfaction'],
                                'Ressources & Logistique' => ['intervention', 'fournisseur', 'stock', 'document'],
                                'Finances' => ['budget', 'assigner_budget', 'depense'],
                                'Communication & RDV' => ['chat', 'rendezvous'],
                                'Suivi' => ['rapport'],
                                'Administration' => ['role', 'utilisateur', 'historique', 'sous_traitance'],
                            ];
                            @endphp

                            <div class="row g-3">
                                @foreach($groupes as $groupeNom => $slugs)
                                    @php
                                        $permsGroupe = $role->permissions->filter(function($p) use ($slugs) {
                                            return in_array($p->slug, $slugs);
                                        });
                                    @endphp
                                    @if($permsGroupe->count() > 0)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 border-0 shadow-sm" style="background: rgba(99,102,241,.03);">
                                            <div class="card-body p-3">
                                                <h6 class="text-uppercase fw-bold text-primary mb-3" style="font-size: 0.8rem;">
                                                    <i class="bi bi-folder2-open me-1"></i> {{ $groupeNom }}
                                                </h6>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($permsGroupe as $p)
                                                        <li class="mb-2 small d-flex align-items-center">
                                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                            <span title="{{ $p->slug }}">{{ $p->nom }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Utilisateurs avec ce rôle -->
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-people me-2"></i>Utilisateurs ({{ $role->users->count() }})</h6>
                    </div>
                    <div class="p-0 table-responsive max-h-300" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Nom Prénom</th>
                                    <th>Email</th>
                                    <th>Date d'inscription</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($role->users->count() > 0)
                                    @foreach($role->users as $roleUser)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; font-weight: bold;">
                                                    {{ strtoupper(substr($roleUser->prenom ?? $roleUser->name, 0, 1)) }}{{ strtoupper(substr($roleUser->nom ?? '', 0, 1)) }}
                                                </div>
                                                <span class="fw-bold">{{ $roleUser->prenom }} {{ $roleUser->nom ?? $roleUser->name }}</span>
                                            </div>
                                        </td>
                                        <td><span class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $roleUser->email }}</span></td>
                                        <td>{{ $roleUser->created_at ? $roleUser->created_at->format('d/m/Y') : 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="bi bi-person-x fs-4 d-block mb-2"></i>
                                        Aucun utilisateur ne possède ce rôle pour le moment.
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
