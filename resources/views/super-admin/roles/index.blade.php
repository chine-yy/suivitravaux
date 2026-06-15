@extends('layouts.super-admin')

@section('title', 'Gestion des Rôles')
@section('breadcrumb')
    <span class="text-muted">Rôles</span>
@endsection

@section('content')
<div class="cp-dashboard">
    <div class="cp-content">
        <!-- Header -->
        <div class="cp-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="cp-page-title"><i class="bi bi-shield-check me-2"></i>Gestion des Roles</h1>
                <p class="cp-page-subtitle">Creez et gerez les roles utilisateurs de la plateforme</p>
            </div>
            <div class="d-flex gap-2">
                @if(auth()->user()->hasPermission('exporter-pdf-roles-permissions'))
                <button class="btn btn-outline-danger" onclick="exportToPdf('rolesTable', 'Liste des roles', 'roles_export')">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Exporter PDF
                </button>
                @endif
                @if(auth()->user()->hasPermission('create-roles-permissions'))
                <a href="{{ route('super-admin.roles.create') }}" class="btn btn-primary px-4">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Role
                </a>
                @endif
            </div>
        </div>



        @if(session('generated_password'))
        <div class="alert alert-success border-0 shadow-sm mb-4 p-4" id="passwordModal" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-left: 5px solid #009A44 !important;">
            <div class="d-flex align-items-start gap-3">
                <div class="fs-2">🔑</div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold text-success mb-1">Compte créé avec succès !</h5>
                    <p class="mb-2">Le compte de <strong>{{ session('new_user_name') }}</strong> ({{ session('new_user_email') }}) a été créé.</p>
                    <p class="mb-1">Mot de passe généré automatiquement :</p>
                    <div class="d-flex align-items-center gap-2">
                        <code class="fs-5 fw-bold px-3 py-2 rounded" style="background:#fff; border:2px solid #009A44; letter-spacing:2px;" id="generatedPwd">{{ session('generated_password') }}</code>
                        <button class="btn btn-sm btn-outline-success" onclick="navigator.clipboard.writeText('{{ session('generated_password') }}'); this.innerHTML='<i class=\'bi bi-check2\'></i> Copié!';">
                            <i class="bi bi-clipboard"></i> Copier
                        </button>
                    </div>
                    <p class="mt-2 text-muted small"><i class="bi bi-exclamation-triangle me-1"></i>Communiquez ce mot de passe à l'utilisateur. Il ne sera plus affiché.</p>
                </div>
                <button class="btn-close" onclick="document.getElementById('passwordModal').remove()"></button>
            </div>
        </div>
        @endif

        <!-- Stats -->
        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-green"><i class="bi bi-shield-check"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $totalRoles }}</div>
                    <div class="cp-stat-label">Total Rôles</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-people"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $totalUtilisateursAvecRole }}</div>
                    <div class="cp-stat-label">Utilisateurs avec Rôle</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-key"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $permissions->count() }}</div>
                    <div class="cp-stat-label">Permissions Disponibles</div>
                </div>
            </div>
        </div>

        <!-- Filtre -->
        <div class="cp-chart-card mb-4">
            <div class="cp-chart-header">
                <h6 class="cp-chart-title"><i class="bi bi-filter me-2"></i>Filtres de recherche</h6>
            </div>
            <div class="p-4">
                <form action="{{ route('super-admin.roles.index') }}" method="GET" class="row g-3">
                    <div class="col-md-9">
                        <input type="text" name="nom" class="form-control" placeholder="Rechercher par nom de rôle..." value="{{ request('nom') }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Rechercher
                        </button>
                        <a href="{{ route('super-admin.roles.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Chart + Table -->
        <div class="row g-4">
            <!-- Graphique -->
            <div class="col-lg-5">
                <div class="cp-chart-card h-100">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-bar-chart me-2"></i>Utilisateurs par Rôle</h6>
                    </div>
                    <div class="cp-chart-body" style="height:280px;">
                        <canvas id="rolesChart" data-roles='@json($rolesData)'></canvas>
                    </div>
                </div>
            </div>

            <!-- Liste des rôles -->
            <div class="col-lg-7">
                <div class="cp-chart-card">
                    <div class="cp-chart-header">
                        <h6 class="cp-chart-title"><i class="bi bi-list-ul me-2"></i>Liste des Rôles</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="rolesTable">
                            <thead>
                                <tr style="background: rgba(99,102,241,.08);">
                                    <th>Rôle</th>
                                    <th>Permissions</th>
                                    <th>Utilisateurs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                <tr>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2" style="background:linear-gradient(135deg,#009A44,#007a35);font-size:.85rem;">
                                            {{ $role->nom }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($role->permissions->isEmpty())
                                            <span class="text-muted small">Aucune permission accordée</span>
                                        @else
                                            @foreach($role->permissions->take(3) as $p)
                                                <span class="badge bg-light text-dark border me-1">{{ $p->nom }}</span>
                                            @endforeach
                                            @if($role->permissions->count() > 3)
                                                <span class="text-muted small">+{{ $role->permissions->count() - 3 }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">{{ $role->users_count }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if(auth()->user()->hasPermission('view-roles-permissions'))
                                            <a href="{{ route('super-admin.roles.show', $role->id) }}" class="btn btn-sm btn-outline-primary" title="Voir détails">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            @endif
                                            @if(auth()->user()->hasPermission('edit-roles-permissions'))
                                            <a href="{{ route('super-admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary" title="Modifier les permissions">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @endif
                                            @if($role->nom !== 'Administrateur Entreprise' && $role->nom !== 'Administration' && $role->nom !== 'Super Admin')
                                            @if(auth()->user()->hasPermission('delete-roles-permissions'))
                                            <form action="{{ route('super-admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Supprimer ce rôle ?');" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-shield-x" style="font-size:2rem;"></i>
                                        <p class="mt-2">Aucun rôle créé. <a href="{{ route('super-admin.roles.create') }}">Créer le premier rôle</a></p>
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
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('rolesChart');
    if (!canvas) return;
    const rawData = JSON.parse(canvas.dataset.roles || '[]');
    if (!rawData.length) return;
    const labels = rawData.map(r => r.nom);
    const values = rawData.map(r => r.users);
    const colors = ['#009A44','#007a35','#009A44','#009A44','#3b82f6','#6366f1','#ef4444','#ec4899'];
    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Utilisateurs',
                data: values,
                backgroundColor: colors.slice(0, labels.length),
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush
