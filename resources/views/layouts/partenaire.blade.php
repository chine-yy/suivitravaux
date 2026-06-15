<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Partenaire') — {{ config('app.name') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-green.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>

<body data-layout="partenaire">
    @php
        $partenaire = auth()->user();
        $nomEntreprise = 'Espace Partenaire';
        $partenaireInitials = $partenaire ? strtoupper(substr($partenaire->prenom ?? '', 0, 1) . substr($partenaire->name ?? '', 0, 1)) : 'PA';
    @endphp

    <div class="app-layout" id="appLayout">
        <!-- Sidebar -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="app-sidebar-header">
                <a href="{{ route('partenaire.dashboard') }}" class="app-brand">
                    <div class="app-brand-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="app-brand-text">
                        <span class="app-brand-title">{{ $nomEntreprise }}</span>
                        <span class="app-brand-subtitle">Partenaire</span>
                    </div>
                </a>
                <button class="app-sidebar-close d-lg-none" id="sidebarClose" aria-label="Fermer">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <nav class="app-sidebar-nav app-sidebar-nav-scroll">
                <div class="app-nav-section">
                    <div class="app-nav-section-title">Principal</div>
                    <a href="{{ route('partenaire.dashboard') }}"
                        class="app-nav-item {{ request()->routeIs('partenaire.dashboard*') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Tableau de bord</span>
                    </a>
                </div>

                <div class="app-nav-section">
                    <div class="app-nav-section-title">Projet</div>
                    <a href="{{ route('partenaire.equipe') }}"
                        class="app-nav-item {{ request()->routeIs('partenaire.equipe*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Mon Équipe</span>
                    </a>
                    <a href="{{ route('partenaire.rapports') }}"
                        class="app-nav-item {{ request()->routeIs('partenaire.rapports*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Rapports</span>
                    </a>
                    <a href="{{ route('partenaire.factures') }}"
                        class="app-nav-item {{ request()->routeIs('partenaire.factures*') ? 'active' : '' }}">
                        <i class="bi bi-receipt-cutoff"></i>
                        <span>Factures</span>
                    </a>
                </div>

            </nav>

            <div class="app-sidebar-footer">
                <a href="{{ route('partenaire.parametres') }}" class="app-nav-item">
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
                    <div class="app-breadcrumb d-none d-md-flex">
                        <span class="app-breadcrumb-item">Espace Partenaire</span>
                        @hasSection('breadcrumb')
                            <span class="app-breadcrumb-separator">/</span>
                            @yield('breadcrumb')
                        @endif
                    </div>
                </div>

                <div class="app-header-right">
                    <!-- User Menu -->
                    <div class="app-user-menu dropdown">
                        <button class="app-user-btn" data-bs-toggle="dropdown">
                            <div class="app-user-avatar" id="headerAvatarWrapper">
                                @if($partenaire && $partenaire->photo_url)
                                    <img src="{{ $partenaire->photo_url }}" alt="Avatar" id="headerAvatarImg"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <span class="app-user-avatar-fallback" style="display:none;">
                                        {{ $partenaireInitials }}
                                    </span>
                                @else
                                    <span class="app-user-avatar-fallback d-flex" id="headerAvatarFallback">
                                        {{ $partenaireInitials }}
                                    </span>
                                @endif
                            </div>
                            <div class="app-user-info d-none d-md-flex">
                                @if($partenaire)
                                    <span class="app-user-name">{{ $partenaire->prenom }} {{ $partenaire->name }}</span>
                                    <span class="app-user-role">Partenaire</span>
                                @else
                                    <span class="app-user-name">Partenaire</span>
                                    <span class="app-user-role">Partenaire</span>
                                @endif
                            </div>
                            <i class="bi bi-chevron-down app-user-chevron"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('partenaire.parametres') }}">
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

                    <!-- Notifications Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    @endif

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
    <script src="{{ asset('js/dashboard/dashboard.js') }}"></script>

    <script>
        (function () {
            const STORAGE_KEYS = {
                theme: 'cp_theme_mode'
            };

            function readBool(key, defaultValue) {
                const value = localStorage.getItem(key);
                if (value === null) return defaultValue;
                return value === 'true';
            }

            function writeBool(key, value) {
                localStorage.setItem(key, value ? 'true' : 'false');
            }

            function getTheme() {
                return 'light';
            }

            function setTheme(mode) {
                const theme = mode === 'dark' ? 'dark' : 'light';
                localStorage.setItem(STORAGE_KEYS.theme, theme);
                document.body.classList.remove('cp-theme-light', 'cp-theme-dark');
                document.body.classList.add('cp-theme-' + theme);
            }

            function initSidebar() {
                const menuToggle = document.getElementById('menuToggle');
                const sidebar = document.getElementById('appSidebar');
                const sidebarOverlay = document.getElementById('sidebarOverlay');
                const sidebarClose = document.getElementById('sidebarClose');

                if (menuToggle) {
                    menuToggle.addEventListener('click', () => {
                        sidebar.classList.add('open');
                        sidebarOverlay.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    });
                }

                if (sidebarOverlay) {
                    sidebarOverlay.addEventListener('click', () => {
                        sidebar.classList.remove('open');
                        sidebarOverlay.classList.remove('active');
                        document.body.style.overflow = '';
                    });
                }

                if (sidebarClose) {
                    sidebarClose.addEventListener('click', () => {
                        sidebar.classList.remove('open');
                        sidebarOverlay.classList.remove('active');
                        document.body.style.overflow = '';
                    });
                }
            }

            setTheme(getTheme());
            initSidebar();
        })();
    </script>

    @stack('scripts')
</body>

</html>