<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Super Admin') — {{ config('app.name') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-green.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/super-admin/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/premium.css') }}">

    @stack('styles')
</head>

<body data-layout="super-admin">
    @php
        $currentSuperAdmin = null;
        $sessions = session('multi_sessions', []);
        $superAdminSession = collect($sessions)->where('type', 'SuperAdmin')->first();
        if ($superAdminSession && isset($superAdminSession['user_id'])) {
            $currentSuperAdmin = \App\Models\User::find($superAdminSession['user_id']);
        }
        if (!$currentSuperAdmin) {
            $currentSuperAdmin = \App\Models\User::superAdmins()->first();
        }
        $currentUserName = $currentSuperAdmin->name ?? ($currentSuperAdmin->nom ?? 'Super Admin');
        $currentUserFirstName = $currentSuperAdmin->prenom ?? '';
        $currentUserPhotoUrl = $currentSuperAdmin ? ($currentSuperAdmin->photo_url ?? null) : null;
        $currentUserInitials = strtoupper(substr($currentUserFirstName, 0, 1) . substr($currentUserName, 0, 1));
    @endphp

    @php
        $sections = [
            'principal' => [
                'title' => 'Principal',
                'items' => [
                    ['route' => 'super-admin.dashboard', 'icon' => 'bi-grid-1x2', 'label' => 'Tableau de bord'],
                ]
            ],

            'projets' => [
                'title' => 'Gestion de Projets',
                'items' => [
                    ['route' => 'super-admin.projets.index', 'icon' => 'bi-briefcase', 'label' => 'Projets'],
                    ['route' => 'super-admin.phases.index', 'icon' => 'bi-collection', 'label' => 'Phases'],
                    ['route' => 'super-admin.taches.index', 'icon' => 'bi-list-task', 'label' => 'Tâches'],
                    ['route' => 'super-admin.sous-taches.index', 'icon' => 'bi-list-check', 'label' => 'Sous-Tâches'],
                    ['route' => 'super-admin.incidents.index', 'icon' => 'bi-exclamation-triangle', 'label' => 'Incidents'],
                    ['route' => 'super-admin.rapports.index', 'icon' => 'bi-file-earmark-text', 'label' => 'Rapports'],
                    ['route' => 'super-admin.depenses.index', 'icon' => 'bi-receipt', 'label' => 'Gestion Dépenses'],
                ]
            ],

            'rh' => [
                'title' => 'Ressources Humaines',
                'items' => [
                    ['route' => 'super-admin.roles.index', 'icon' => 'bi-shield-check', 'label' => 'Rôles & Permissions'],
                    ['route' => 'super-admin.permissions.index', 'icon' => 'bi-key', 'label' => 'Permissions'],
                    ['route' => 'super-admin.users.index', 'icon' => 'bi-people', 'label' => 'Utilisateurs'],
                    ['route' => 'super-admin.equipes.index', 'icon' => 'bi-people-fill', 'label' => 'Équipes'],
                ]
            ],

            'partenaires' => [
                'title' => 'Partenaires & Contrats',
                'items' => [
                    ['route' => 'super-admin.partenaires.index', 'icon' => 'bi-person-workspace', 'label' => 'Partenaires'],
                    ['route' => 'super-admin.contrats.index', 'icon' => 'bi-file-earmark-ruled', 'label' => 'Contrats'],
                    ['route' => 'super-admin.factures.index', 'icon' => 'bi-receipt-cutoff', 'label' => 'Factures'],
                    ['route' => 'super-admin.budget.index', 'icon' => 'bi-cash-stack', 'label' => 'Budgets'],
                    ['route' => 'super-admin.satisfaction.index', 'icon' => 'bi-emoji-smile', 'label' => 'Satisfaction'],
                ]
            ],

            'operations' => [
                'title' => 'Opérations Terrain',
                'items' => [
                    ['route' => 'super-admin.interventions.index', 'icon' => 'bi-tools', 'label' => 'Interventions'],
                    ['route' => 'super-admin.rendezvous.index', 'icon' => 'bi-calendar-check', 'label' => 'Rendez-vous'],
                ]
            ],

            'sous_traitance' => [
                'title' => 'Sous-Traitance',
                'items' => [
                    ['route' => 'super-admin.sous-traitances.index', 'icon' => 'bi-tools', 'label' => 'Sous-Traitances'],
                ]
            ],

            'allocations' => [
                'title' => 'Allocations',
                'items' => [
                    ['route' => 'super-admin.allocation-projet.index', 'icon' => 'bi-diagram-3', 'label' => 'Projet'],
                    ['route' => 'super-admin.allocation-sous-traitance.index', 'icon' => 'bi-people', 'label' => 'Sous-Traitance'],
                ]
            ],

            'stocks' => [
                'title' => 'Stocks & Fournisseurs',
                'items' => [
                    ['route' => 'super-admin.stocks.index', 'icon' => 'bi-boxes', 'label' => 'Stocks & Matériaux'],
                    ['route' => 'super-admin.fournisseurs.index', 'icon' => 'bi-truck', 'label' => 'Fournisseurs'],
                ]
            ],

            'docs' => [
                'title' => 'Documents & Communication',
                'items' => [
                    ['route' => 'super-admin.documents.index', 'icon' => 'bi-folder2-open', 'label' => 'Documents'],
                    ['route' => 'super-admin.chat.index', 'icon' => 'bi-chat-dots', 'label' => 'Messagerie'],
                    ['route' => 'super-admin.ia-chat.index', 'icon' => 'bi-chat-square-quote', 'label' => 'IA Chat Box'],
                    ['route' => 'super-admin.historique.index', 'icon' => 'bi-clock-history', 'label' => 'Historique'],
                ]
            ],

            'systeme' => [
                'title' => 'Système',
                'items' => [
                    ['route' => 'super-admin.configuration.index', 'icon' => 'bi-gear', 'label' => 'Configuration'],
                    ['route' => 'super-admin.database.index', 'icon' => 'bi-database', 'label' => 'Base de données'],
                ]
            ],
        ];
    @endphp

    <div class="app-layout" id="appLayout">
        <!-- Sidebar -->
        <aside class="app-sidebar app-sidebar-super" id="appSidebar">
            <div class="app-sidebar-header">
                <a href="{{ route('super-admin.dashboard') }}" class="app-brand">
                    <div class="app-brand-icon app-brand-icon-super">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div class="app-brand-text">
                        <span class="app-brand-title">{{ config('app.name') }}</span>
                        <span class="app-brand-subtitle">Super Admin</span>
                    </div>
                </a>
                <button class="app-sidebar-close d-lg-none" id="sidebarClose" aria-label="Fermer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <nav class="app-sidebar-nav app-sidebar-nav-scroll">
                @foreach($sections as $sectionKey => $section)
                    <div class="app-nav-section">
                        <div class="app-nav-section-title">{{ $section['title'] }}</div>
                        @foreach($section['items'] as $item)
                            @php
                            $isActive = request()->routeIs($item['route'] . '*');
                            if (!$isActive && str_ends_with($item['route'], '.index')) {
                                $resourcePrefix = substr($item['route'], 0, -5);
                                $isActive = request()->routeIs($resourcePrefix . '*') &&
                                            !collect($section['items'])->contains(function($sibling) use ($item) {
                                                return $sibling['route'] !== $item['route'] && request()->routeIs($sibling['route'] . '*');
                                            });
                            }
                            @endphp
                            <a href="{{ str_contains($item['route'], '#') ? route(explode('#', $item['route'])[0]) . '#' . explode('#', $item['route'])[1] : route($item['route']) }}"
                                class="app-nav-item {{ $isActive ? 'active' : '' }}">
                                <i class="bi {{ $item['icon'] }}"></i>
                                <span>{{ $item['label'] }}</span>
                                @if($isActive)
                                    <span class="app-nav-indicator"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </nav>

            <div class="app-sidebar-footer">
                <a href="{{ route('super-admin.parametres') }}"
                    class="app-nav-item {{ request()->routeIs('super-admin.parametres') ? 'active' : '' }}">
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
                        onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='{{ route('super-admin.dashboard') }}'; }"
                        aria-label="Retour">
                        <i class="bi bi-arrow-left"></i>
                        <span class="d-none d-md-inline">Retour</span>
                    </button>
                    <nav class="app-breadcrumb d-none d-md-flex" aria-label="Fil d'Ariane">
                        <span class="app-breadcrumb-item">Super Admin</span>
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
                                 @if($currentUserPhotoUrl)
                                     <img src="{{ $currentUserPhotoUrl }}" alt="Photo de profil"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span class="app-user-avatar-fallback" style="display:none;">
                                        {{ $currentUserInitials }}
                                    </span>
                                @else
                                    <span class="app-user-avatar-fallback d-flex">
                                        {{ $currentUserInitials }}
                                    </span>
                                @endif
                            </div>
                             <div class="app-user-info d-none d-md-flex">
                                 <span class="app-user-name">{{ $currentUserName }}</span>
                                 <span class="app-user-role">Super Administrateur</span>
                             </div>
                            <i class="bi bi-chevron-down app-user-chevron"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('super-admin.parametres') }}">
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

            // Notifications management
            const STORAGE_KEY = 'sa_notifications';
            let notifications = [];

            function getStoredNotifications() {
                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    return raw ? JSON.parse(raw) : [];
                } catch (e) {
                    return [];
                }
            }

            function saveNotifications() {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(notifications.slice(0, 30)));
            }

            function renderNotifications() {
                const list = document.getElementById('notifList');
                if (!list) return;

                if (notifications.length === 0) {
                    list.innerHTML = `
                        <div class="app-notif-empty">
                            <i class="bi bi-bell-slash"></i>
                            <p>Aucune notification</p>
                        </div>
                    `;
                    return;
                }

                list.innerHTML = notifications.slice(0, 10).map(n => `
                    <div class="app-notif-item ${n.read ? '' : 'unread'}" data-id="${n.id}">
                        <div class="app-notif-icon ${n.type}">
                            <i class="bi bi-${n.type === 'success' ? 'check-circle' : n.type === 'warning' ? 'exclamation-triangle' : n.type === 'danger' ? 'exclamation-octagon' : 'info-circle'}"></i>
                        </div>
                        <div class="app-notif-content">
                            <div class="app-notif-title">${n.title}</div>
                            <div class="app-notif-message">${n.message}</div>
                            <div class="app-notif-time">${n.time}</div>
                        </div>
                    </div>
                `).join('');
            }

            function pushNotification(title, message, type = 'info') {
                notifications.unshift({
                    id: Date.now().toString(),
                    title,
                    message,
                    type,
                    time: new Date().toLocaleString('fr-FR'),
                    read: false
                });
                renderNotifications();
                saveNotifications();
            }

            // Initialize
            notifications = getStoredNotifications();
            renderNotifications();

            // Mark all as read
            document.getElementById('markAllRead')?.addEventListener('click', function () {
                notifications = notifications.map(n => ({ ...n, read: true }));
                renderNotifications();
                saveNotifications();
            });

            // Clear all
            document.getElementById('clearAll')?.addEventListener('click', function () {
                notifications = [];
                renderNotifications();
                saveNotifications();
            });

            // Flash notifications from session
            @if(session('success'))
                pushNotification('Succès', '{{ session('success') }}', 'success');
            @endif
            @if(session('error'))
                pushNotification('Erreur', '{{ session('error') }}', 'danger');
            @endif
            @if(session('warning'))
                pushNotification('Attention', '{{ session('warning') }}', 'warning');
            @endif

            // Expose globally
            window.pushNotification = pushNotification;
        });
    </script>

    @include('partials.export-excel')
    @include('partials.export-pdf')
    @stack('scripts')
    @stack('modals')

</html>
