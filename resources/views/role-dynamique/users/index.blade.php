@extends('layouts.role-dynamique')

@section('title', 'Gestion des Utilisateurs')
@section('breadcrumb')
<span class="text-muted">Utilisateurs</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <!-- Header -->
        <div class="cp-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-people me-2"></i>Gestion des Utilisateurs</h1>
                <p class="cp-page-subtitle">Créez et gérez les comptes utilisateurs</p>
            </div>
            <div class="d-flex gap-2">
                @if($has('exporter-pdf-utilisateurs'))
                <a href="{{ route('role-dynamique.export.users.pdf') }}" class="btn btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter PDF
                </a>
                @endif
                @if($has('create-utilisateurs'))
                <a href="{{ route('role-dynamique.users.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-person-plus me-2"></i>Nouvel Utilisateur
                </a>
                @endif
            </div>
        </div>


        {{-- Affichage du mot de passe généré --}}
        @if(session('generated_password'))
        <div class="alert border-0 shadow mb-4 p-4" id="passwordAlert"
            style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9); border-left: 5px solid #009A44 !important;">
            <div class="d-flex align-items-start gap-3">
                <div class="fs-1">🔐</div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-1" style="color:#009A44;">Mot de Passe Généré</h5>
                    <p class="mb-1">Utilisateur : <strong>{{ session('new_user_name') }}</strong> — <span
                            class="text-muted">{{ session('new_user_email') }}</span></p>
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <code class="fs-4 fw-bold px-3 py-2 rounded"
                            style="background:#fff; border: 2px solid #009A44; letter-spacing:3px; color:#009A44;">{{ session('generated_password') }}</code>
                        <button class="btn btn-sm btn-outline-warning" id="copyPwdBtn"
                            onclick="navigator.clipboard.writeText('{{ session('generated_password') }}'); this.innerHTML='<i class=\'bi bi-check2\' ></i> Copié !';">
                            <i class="bi bi-clipboard"></i> Copier
                        </button>
                    </div>
                    <p class="mt-2 small text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i>Ce mot
                        de passe ne sera plus affiché. Communiquez-le à l'utilisateur maintenant.</p>
                </div>
                <button class="btn-close" onclick="document.getElementById('passwordAlert').remove()"></button>
            </div>
        </div>
        @endif

        <!-- Stats -->
        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-people"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $totalUsers }}</div>
                    <div class="cp-stat-label">Total Utilisateurs</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-person-check"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $usersActifs }}</div>
                    <div class="cp-stat-label">Actifs</div>
                </div>
            </div>
            <div class="cp-stat-card cp-stat-danger">
                <div class="cp-stat-icon cp-bg-danger"><i class="bi bi-person-x"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $usersInactifs }}</div>
                    <div class="cp-stat-label">Inactifs</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-shield-check"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $rolesCount }}</div>
                    <div class="cp-stat-label">Rôles Créés</div>
                </div>
            </div>
        </div>

        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('role-dynamique.users.index') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label small fw-bold">Nom / Prénom / Email</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Rôle</label>
                        <select name="role_id" class="form-select form-select-sm">
                            <option value="">Tous les rôles</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id')==$role->id ? 'selected' : '' }}>
                                {{ $role->nom }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filtrer
                        </button>
                        <a href="{{ route('role-dynamique.users.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <!-- Graphique utilisateurs par rôle -->
            <div class="col-lg-4">
                <div class="cp-chart-card h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-pie-chart me-2"></i>Répartition par Rôle</h6>
                    </div>
                    <div class="cp-chart-body" style="height:260px;">
                        <canvas id="usersChart" data-users='@json($usersParRole)'></canvas>
                    </div>
                </div>
            </div>

            <!-- Tableau des utilisateurs -->
            <div class="col-lg-8">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Utilisateurs</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="usersTable">
                            <thead>
                                <tr style="background:rgba(99,102,241,.08);">
                                    <th>Utilisateur</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr class="user-row">
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                                style="width:36px;height:36px;background:linear-gradient(135deg,#009A44,#007a35);color:#fff;font-size:.8rem;font-weight:700;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}{{
                                                strtoupper(substr($user->prenom ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $user->name }} {{ $user->prenom }}</div>
                                                <div class="text-muted small">{{ $user->telephone ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-muted small">{{ $user->email }}</td>
                                    <td>
                                        @if($user->role)
                                        <span class="badge rounded-pill px-3"
                                            style="background:linear-gradient(135deg,#009A44,#007a35);">{{
                                            $user->role->nom }}</span>
                                        @else
                                        <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_active ?? true)
                                        <span class="badge bg-success">Actif</span>
                                        @else
                                        <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('role-dynamique.users.show', $user) }}"
                                                class="btn btn-sm btn-outline-info" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @if($has('edit-utilisateurs'))
<a href="{{ route('role-dynamique.users.edit', $user) }}"
                                                class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
@endif
                                            @if($has('reset-password-utilisateurs'))
                                            <form action="{{ route('role-dynamique.users.reset-password', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm('Réinitialiser le mot de passe de {{ $user->name }} ?');"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary"
                                                    title="Reset mdp">
                                                    <i class="bi bi-key"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @if($has('delete-utilisateurs') && !$user->isAdminEntreprise() && !$user->isSuperAdmin())
<form action="{{ route('role-dynamique.users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('Supprimer cet utilisateur ?');"
                                                class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
@endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-people" style="font-size:2rem;"></i>
                                        <p class="mt-2">Aucun utilisateur créé. @if($has('create-utilisateurs'))
<a
                                                href="{{ route('role-dynamique.users.create') }}">Créer le premier
                                                utilisateur</a>
@endif</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Graphique
        const canvas = document.getElementById('usersChart');
        if (canvas) {
            const rawData = JSON.parse(canvas.dataset.users || '[]');
            if (rawData.length > 0) {
                const colors = ['#009A44', '#007a35', '#009A44', '#009A44', '#3b82f6', '#6366f1', '#ef4444', '#ec4899'];
                new Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels: rawData.map(r => r.nom),
                        datasets: [{ data: rawData.map(r => r.count), backgroundColor: colors.slice(0, rawData.length), borderWidth: 2 }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                });
            }
        }

</script>
@endpush
