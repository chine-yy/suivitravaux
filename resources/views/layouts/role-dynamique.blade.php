<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de Bord') — {{ config('app.name') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-green.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>

<body data-layout="role-dynamique">
    @php
        $user = Auth::user();
        $roleName = $user->role ? $user->role->nom : 'Collaborateur';
        $userName = $user->name ?? 'Utilisateur';
        $userFirstName = $user->prenom ?? '';
        $initials = strtoupper(substr($userFirstName, 0, 1) . substr($userName, 0, 1));

        // Helper global: utilise l'utilisateur authentifié pour tester les permissions.
        $has = function (?string $perm) use ($user) {
            if (!$user) {
                return false;
            }

            // Super Admin a tous les droits
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }

            if ($perm === null || $perm === '') {
                return false;
            }

            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($perm);
            }

            return false;
        };

        // Build sections with permissions filtering (same as super-admin)
        $sections = [
            'principal' => [
                'title' => 'Principal',
                'items' => [
                    ['route' => 'role-dynamique.dashboard', 'icon' => 'bi-grid-1x2', 'label' => 'Tableau de bord', 'permissions' => []],
                ]
            ],
            'projets' => [
                'title' => 'Gestion de Projets',
                'items' => [
                    ['route' => 'role-dynamique.projets.index', 'icon' => 'bi-briefcase', 'label' => 'Projets', 'permissions' => ['view-projets']],
                    ['route' => 'role-dynamique.phases.index', 'icon' => 'bi-collection', 'label' => 'Phases', 'permissions' => ['view-phases']],
                    ['route' => 'role-dynamique.taches.index', 'icon' => 'bi-list-task', 'label' => 'Tâches', 'permissions' => ['view-taches']],
                    ['route' => 'role-dynamique.sous-taches.index', 'icon' => 'bi-list-check', 'label' => 'Sous-Tâches', 'permissions' => ['view-sous-taches']],
                    ['route' => 'role-dynamique.incidents.index', 'icon' => 'bi-exclamation-triangle', 'label' => 'Incidents', 'permissions' => ['view-incidents']],
                    ['route' => 'role-dynamique.rapports.index', 'icon' => 'bi-file-earmark-text', 'label' => 'Rapports', 'permissions' => ['view-rapports']],
                    ['route' => 'role-dynamique.depenses.index', 'icon' => 'bi-receipt', 'label' => 'Gestion Dépenses', 'permissions' => ['view-depenses']],
                ]
            ],
            'rh' => [
                'title' => 'Ressources Humaines',
                'items' => [
                    ['route' => 'role-dynamique.roles.index', 'icon' => 'bi-shield-check', 'label' => 'Rôles & Permissions', 'permissions' => ['view-roles-permissions']],
                    ['route' => 'role-dynamique.users.index', 'icon' => 'bi-people', 'label' => 'Utilisateurs', 'permissions' => ['view-utilisateurs']],
                    ['route' => 'role-dynamique.equipes.index', 'icon' => 'bi-people-fill', 'label' => 'Équipes', 'permissions' => ['view-equipes']],
                ]
            ],
            'partenaires' => [
                'title' => 'Partenaires & Contrats',
                'items' => [
                    ['route' => 'role-dynamique.partenaires.index', 'icon' => 'bi-person-workspace', 'label' => 'Partenaires', 'permissions' => ['view-partenaires']],
                    ['route' => 'role-dynamique.contrats.index', 'icon' => 'bi-file-earmark-ruled', 'label' => 'Contrats', 'permissions' => ['view-contrats']],
                    ['route' => 'role-dynamique.factures.index', 'icon' => 'bi-receipt', 'label' => 'Factures', 'permissions' => ['view-factures']],
                    ['route' => 'role-dynamique.satisfaction.index', 'icon' => 'bi-emoji-smile', 'label' => 'Satisfactions', 'permissions' => ['view-satisfaction-partenaire']],
                    ['route' => 'role-dynamique.budget.index', 'icon' => 'bi-cash-stack', 'label' => 'Budgets', 'permissions' => ['gerer-budgets']],
                ]
            ],
            'allocations' => [
                'title' => 'Allocations',
                'items' => [
                    ['route' => 'role-dynamique.allocation-projet.index', 'icon' => 'bi-diagram-3', 'label' => 'Projet', 'permissions' => ['view-budget-allocation-projet']],
                    ['route' => 'role-dynamique.allocation-sous-traitance.index', 'icon' => 'bi-people', 'label' => 'Sous-Traitance', 'permissions' => ['view-budget-allocation-sous-traitance']],
                ]
            ],
            'operations' => [
                'title' => 'Opérations Terrain',
                'items' => [
                    ['route' => 'role-dynamique.interventions.index', 'icon' => 'bi-tools', 'label' => 'Interventions', 'permissions' => ['view-interventions']],
                    ['route' => 'role-dynamique.rendezvous.index', 'icon' => 'bi-calendar-event', 'label' => 'Rendez-vous', 'permissions' => ['view-rendez-vous']],
                ]
            ],
            'stocks' => [
                'title' => 'Stocks & Fournisseurs',
                'items' => [
                    ['route' => 'role-dynamique.stocks.index', 'icon' => 'bi-boxes', 'label' => 'Stocks', 'permissions' => ['view-stocks-materiaux']],
                    ['route' => 'role-dynamique.fournisseurs.index', 'icon' => 'bi-truck', 'label' => 'Fournisseurs', 'permissions' => ['view-fournisseurs']],
                    ['route' => 'role-dynamique.sous-traitances.index', 'icon' => 'bi-briefcase', 'label' => 'Sous-traitances', 'permissions' => ['view-sous-traitances']],
                ]
            ],
            'docs' => [
                'title' => 'Documents & Communication',
                'items' => [
                    ['route' => 'role-dynamique.documents.index', 'icon' => 'bi-folder2-open', 'label' => 'Documents', 'permissions' => ['view-documents']],
                    ['route' => 'role-dynamique.chat.index', 'icon' => 'bi-chat-dots', 'label' => 'Messagerie', 'permissions' => ['chat-messagerie-activer']],
                    ['route' => 'role-dynamique.ia-chat.index', 'icon' => 'bi-chat-square-quote', 'label' => 'IA Chat Box', 'permissions' => ['activer-ia-chat-box']],
                    ['route' => 'role-dynamique.historique.index', 'icon' => 'bi-clock-history', 'label' => 'Historique', 'permissions' => ['view-historique']],
                ]
            ],
            'systeme' => [
                'title' => 'Système',
                'items' => [
                    ['route' => 'role-dynamique.configuration.index', 'icon' => 'bi-gear', 'label' => 'Configuration', 'permissions' => []],
                    ['route' => 'role-dynamique.configuration.logs', 'icon' => 'bi-file-text', 'label' => 'Logs', 'permissions' => ['view-logs', 'clear-logs', 'exporter-pdf-logs']],
                    ['route' => 'role-dynamique.database.index', 'icon' => 'bi-database', 'label' => 'Base de données', 'permissions' => ['view-base-de-donnees']],
                ]
            ],
        ];

        // Filter sections based on permissions
        foreach ($sections as $sectionKey => $section) {
            $filteredItems = [];
            foreach ($section['items'] as $item) {
                $itemPermissions = $item['permissions'] ?? [];
                $hasAnyPermission = empty($itemPermissions) || collect($itemPermissions)->contains(fn($p) => $has($p));
                if ($hasAnyPermission) {
                    unset($item['permissions']);
                    $filteredItems[] = $item;
                }
            }
            $sections[$sectionKey]['items'] = $filteredItems;
        }



    @endphp

    <div class="app-layout" id="appLayout">
        <!-- Sidebar -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="app-sidebar-header">
                <a href="{{ route('role-dynamique.dashboard') }}" class="app-brand">
                    <div class="app-brand-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="app-brand-text">
                        <span class="app-brand-title">{{ config('app.name') }}</span>
                        <span class="app-brand-subtitle">{{ $roleName }}</span>
                    </div>
                </a>
                <button class="app-sidebar-close d-lg-none" id="sidebarClose" aria-label="Fermer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <nav class="app-sidebar-nav">
                @foreach($sections as $sectionKey => $section)
                    @if(count($section['items']) > 0)
                        <div class="app-nav-section">
                            <div class="app-nav-section-title">{{ $section['title'] }}</div>
                            @foreach($section['items'] as $item)
                                @php
                                    $isActive = request()->routeIs($item['route'] . '*');
                                    if (!$isActive && str_ends_with($item['route'], '.index')) {
                                        $resourcePrefix = substr($item['route'], 0, -5);
                                        $isActive = request()->routeIs($resourcePrefix . '*') &&
                                            !collect($section['items'])->contains(function ($sibling) use ($item) {
                                                return $sibling['route'] !== $item['route'] && request()->routeIs($sibling['route'] . '*');
                                            });
                                    }
                                    $url = Route::has($item['route']) ? route($item['route']) : '#';
                                @endphp
                                <a href="{{ $url }}"
                                    class="app-nav-item {{ $isActive ? 'active' : '' }} {{ $url === '#' ? 'disabled' : '' }}">
                                    <i class="bi {{ $item['icon'] }}"></i>
                                    <span>{{ $item['label'] }}</span>
                                    @if($isActive)
                                        <span class="app-nav-indicator"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </nav>

            <div class="app-sidebar-footer">
                <a href="{{ route('role-dynamique.parametres') }}" class="app-nav-item">
                    <i class="bi bi-person"></i>
                    <span>Mon profil</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="app-nav-item app-nav-logout">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="app-main">
            <!-- Top Navigation -->
            <header class="app-header">
                <div class="app-header-left">
                    <button class="app-menu-toggle d-lg-none" id="menuToggle" aria-label="Menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <button type="button" class="app-back-btn"
                        onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='{{ route('role-dynamique.dashboard') }}'; }"
                        aria-label="Retour">
                        <i class="bi bi-arrow-left"></i>
                        <span class="d-none d-md-inline">Retour</span>
                    </button>
                    <nav class="app-breadcrumb d-none d-md-flex" aria-label="Fil d'Ariane">
                        <span class="app-breadcrumb-item">{{ $roleName }}</span>
                        @hasSection('breadcrumb')
                            <span class="app-breadcrumb-separator">/</span>
                            @yield('breadcrumb')
                        @endif
                    </nav>
                </div>

                <div class="app-header-right">
                    <!-- User Menu -->
                    <div class="app-user-menu dropdown">
                        <button class="app-user-btn" data-bs-toggle="dropdown">
                            <div class="app-user-avatar">
                                @if($user->photo_url)
                                    <img src="{{ $user->photo_url }}" alt=""
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span class="app-user-avatar-fallback" style="display:none;">
                                        {{ $initials }}
                                    </span>
                                @else
                                    <span class="app-user-avatar-fallback d-flex">
                                        {{ $initials }}
                                    </span>
                                @endif
                            </div>
                            <div class="app-user-info d-none d-md-flex">
                                <span class="app-user-name">{{ $userName }}</span>
                                <span class="app-user-role">{{ $roleName }}</span>
                            </div>
                            <i class="bi bi-chevron-down app-user-chevron"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('role-dynamique.parametres') }}">
                                <i class="bi bi-person me-2"></i> Mon profil
                            </a>
                            <hr class="dropdown-divider">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="app-content">
                <div class="app-content-wrapper">
                    @include('partials.alerts')
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div class="app-sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Scripts -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function () {
            const menuToggle = document.getElementById('menuToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebar = document.getElementById('appSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function openSidebar() {
                sidebar?.classList.add('open');
                overlay?.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar?.classList.remove('open');
                overlay?.classList.remove('active');
                document.body.style.overflow = '';
            }

            menuToggle?.addEventListener('click', openSidebar);
            sidebarClose?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);
        });
    </script>

    @include('partials.export-excel')
    @include('partials.export-pdf')
    @stack('scripts')
    @stack('modals')
</body>

</html>