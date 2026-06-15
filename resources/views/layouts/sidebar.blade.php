{{--
Sidebar Component - Usage:
@include('layouts.sidebar', ['type' => 'admin|super-admin|role-dynamique'])
--}}
@php
    $sidebarType = $type ?? 'admin';
    $user = auth()->user();
    $isSuperAdmin = $user && ($user->isSuperAdmin() || ($user->type_compte ?? null) === 'super_admin');
    $isAdmin = $user && ($user->isAdminEntreprise() || ($user->type_compte ?? null) === 'admin');
    $isPartenaire = $user && ($user->isPartenaire() || ($user->type_compte ?? null) === 'partenaire');
    $isFullAdmin = $isSuperAdmin || $isAdmin || $sidebarType === 'super-admin';

    // Get user permissions
    $userPermissions = [];
    if ($user && method_exists($user, 'permissions')) {
        $permissionsRelation = $user->permissions();
        $userPermissions = $permissionsRelation instanceof \Illuminate\Support\Collection
            ? $permissionsRelation->pluck('slug')->toArray()
            : $permissionsRelation->pluck('slug')->toArray();
    }

    $databasePermissions = [
        'view-base-de-donnees',
        'clear-base-de-donnees',
        'sauvegarde-base-de-donnees',
        'delete-base-de-donnees',
    ];

    // Common menu items
    $menuItems = [];

    if ($sidebarType === 'super-admin' || $isSuperAdmin) {
        $menuItems = [
            'dashboard' => ['route' => 'super-admin.dashboard', 'icon' => 'bi-grid-1x2', 'label' => 'Tableau de bord', 'permission' => null, 'section' => 'principal'],
            'projets' => ['route' => 'super-admin.projets.index', 'icon' => 'bi-briefcase', 'label' => 'Projets', 'permission' => null, 'section' => 'projets'],
            'phases' => ['route' => 'super-admin.phases.index', 'icon' => 'bi-collection', 'label' => 'Phases', 'permission' => null, 'section' => 'projets'],
            'taches' => ['route' => 'super-admin.taches.index', 'icon' => 'bi-list-task', 'label' => 'Tâches', 'permission' => null, 'section' => 'projets'],
            'sous-taches' => ['route' => 'super-admin.sous-taches.index', 'icon' => 'bi-list-check', 'label' => 'Sous-Tâches', 'permission' => null, 'section' => 'projets'],
            'incidents' => ['route' => 'super-admin.incidents.index', 'icon' => 'bi-exclamation-triangle', 'label' => 'Incidents', 'permission' => null, 'section' => 'projets'],
            'rapports' => ['route' => 'super-admin.rapports.index', 'icon' => 'bi-file-earmark-text', 'label' => 'Rapports', 'permission' => null, 'section' => 'projets'],
            'roles' => ['route' => 'super-admin.roles.index', 'icon' => 'bi-shield-check', 'label' => 'Rôles', 'permission' => null, 'section' => 'rh'],
            'permissions' => ['route' => 'super-admin.permissions.index', 'icon' => 'bi-shield-lock', 'label' => 'Permissions', 'permission' => null, 'section' => 'rh'],
            'users' => ['route' => 'super-admin.users.index', 'icon' => 'bi-people', 'label' => 'Utilisateurs', 'permission' => null, 'section' => 'rh'],
            'equipes' => ['route' => 'super-admin.equipes.index', 'icon' => 'bi-people-fill', 'label' => 'Équipes', 'permission' => null, 'section' => 'rh'],
            'partenaires' => ['route' => 'super-admin.partenaires.index', 'icon' => 'bi-person-workspace', 'label' => 'Partenaires', 'permission' => null, 'section' => 'partenaires'],
            'contrats' => ['route' => 'super-admin.contrats.index', 'icon' => 'bi-file-earmark-ruled', 'label' => 'Contrats', 'permission' => null, 'section' => 'partenaires'],
            'factures' => ['route' => 'super-admin.factures.index', 'icon' => 'bi-receipt-cutoff', 'label' => 'Factures', 'permission' => null, 'section' => 'partenaires'],
            'budget' => ['route' => 'super-admin.budget.index', 'icon' => 'bi-cash-stack', 'label' => 'Budgets', 'permission' => null, 'section' => 'budget'],
            'satisfaction' => ['route' => 'super-admin.satisfaction.index', 'icon' => 'bi-emoji-smile', 'label' => 'Satisfaction', 'permission' => null, 'section' => 'budget'],
            'interventions' => ['route' => 'super-admin.interventions.index', 'icon' => 'bi-tools', 'label' => 'Interventions', 'permission' => null, 'section' => 'operations'],
            'rendezvous' => ['route' => 'super-admin.rendezvous.index', 'icon' => 'bi-calendar-check', 'label' => 'Rendez-vous', 'permission' => null, 'section' => 'operations'],
            'sous-traitances' => ['route' => 'super-admin.sous-traitances.index', 'icon' => 'bi-arrow-repeat', 'label' => 'Sous-Traitances', 'permission' => null, 'section' => 'operations'],
            'allocations' => ['route' => 'super-admin.allocations.index', 'icon' => 'bi-diagram-3', 'label' => 'Allocations', 'permission' => null, 'section' => 'operations'],
            'stocks' => ['route' => 'super-admin.stocks.index', 'icon' => 'bi-boxes', 'label' => 'Stocks', 'permission' => null, 'section' => 'stocks'],
            'fournisseurs' => ['route' => 'super-admin.fournisseurs.index', 'icon' => 'bi-truck', 'label' => 'Fournisseurs', 'permission' => null, 'section' => 'stocks'],
            'documents' => ['route' => 'super-admin.documents.index', 'icon' => 'bi-folder2-open', 'label' => 'Documents', 'permission' => null, 'section' => 'docs'],
            'chat' => ['route' => 'super-admin.chat.index', 'icon' => 'bi-chat-dots', 'label' => 'Messagerie', 'permission' => null, 'section' => 'docs'],
            'ia-chat' => ['route' => 'super-admin.ia-chat.index', 'icon' => 'bi-robot', 'label' => 'IA Chat Box', 'permission' => null, 'section' => 'docs'],
            'historique' => ['route' => 'super-admin.historique.index', 'icon' => 'bi-clock-history', 'label' => 'Historique', 'permission' => null, 'section' => 'docs'],
            'configuration' => ['route' => 'super-admin.configuration.index', 'icon' => 'bi-gear', 'label' => 'Configuration', 'permission' => null, 'section' => 'systeme'],
            'database' => ['route' => 'super-admin.database.index', 'icon' => 'bi-database', 'label' => 'Base de données', 'permission' => null, 'section' => 'systeme'],
        ];
    } elseif ($sidebarType === 'role-dynamique') {
        $menuItems = [
            'dashboard' => ['route' => 'role-dynamique.dashboard', 'icon' => 'bi-grid-1x2', 'label' => 'Tableau de bord', 'permission' => null, 'section' => 'principal'],
        ];
        // Dynamic menu based on permissions (slug format: action-module)
        $dynamicRoutes = [
            'view-projets' => ['route' => 'role-dynamique.projets.index', 'icon' => 'bi-briefcase', 'label' => 'Mes Projets'],
            'view-taches' => ['route' => 'role-dynamique.taches.index', 'icon' => 'bi-list-task', 'label' => 'Mes Tâches'],
            'view-sous-taches' => ['route' => 'role-dynamique.sous-taches.index', 'icon' => 'bi-list-check', 'label' => 'Sous-Tâches'],
            'view-rapports' => ['route' => 'role-dynamique.rapports.index', 'icon' => 'bi-file-earmark-text', 'label' => 'Mes Rapports'],
            'view-incidents' => ['route' => 'role-dynamique.incidents.index', 'icon' => 'bi-exclamation-triangle', 'label' => 'Incidents'],
            'view-phases' => ['route' => 'role-dynamique.phases.index', 'icon' => 'bi-collection', 'label' => 'Phases de Projet'],
            'chat-messagerie' => ['route' => 'role-dynamique.chat.index', 'icon' => 'bi-chat-dots', 'label' => 'Messagerie', 'permission' => 'chat-messagerie-activer'],
            'view-equipes' => ['route' => 'role-dynamique.equipes.index', 'icon' => 'bi-people', 'label' => 'Gestion des Équipes'],
            'view-partenaires' => ['route' => 'role-dynamique.partenaires.index', 'icon' => 'bi-person-workspace', 'label' => 'Gestion Partenaires'],
            'gerer-budgets' => ['route' => 'role-dynamique.budget.index', 'icon' => 'bi-cash-stack', 'label' => 'Gestion Budgets'],
            'view-roles-permissions' => ['route' => 'role-dynamique.roles.index', 'icon' => 'bi-shield-lock', 'label' => 'Rôles & Permissions'],
            'view-utilisateurs' => ['route' => 'role-dynamique.users.index', 'icon' => 'bi-people-fill', 'label' => 'Gestion Utilisateurs'],
            'view-contrats' => ['route' => 'role-dynamique.contrats.index', 'icon' => 'bi-file-earmark-medical', 'label' => 'Gestion des Contrats'],
            'view-factures' => ['route' => 'role-dynamique.factures.index', 'icon' => 'bi-receipt', 'label' => 'Facturation & Paiements'],
            'view-interventions' => ['route' => 'role-dynamique.interventions.index', 'icon' => 'bi-tools', 'label' => 'Suivi des Interventions'],
            'view-fournisseurs' => ['route' => 'role-dynamique.fournisseurs.index', 'icon' => 'bi-truck', 'label' => 'Gestion des Fournisseurs'],
            'view-stocks-materiaux' => ['route' => 'role-dynamique.stocks.index', 'icon' => 'bi-box-seam', 'label' => 'Gestion des Stocks'],
            'view-rendez-vous' => ['route' => 'role-dynamique.rendezvous.index', 'icon' => 'bi-calendar-event', 'label' => 'Planification'],
            'view-documents' => ['route' => 'role-dynamique.documents.index', 'icon' => 'bi-file-earmark', 'label' => 'Documents'],
            'view-historique' => ['route' => 'role-dynamique.historique.index', 'icon' => 'bi-clock-history', 'label' => 'Historique'],
        ];
        foreach ($dynamicRoutes as $slug => $config) {
            $menuItems[$slug] = array_merge($config, ['permission' => $slug, 'section' => 'dynamic']);
        }
    } else {
        // Admin sidebar
        $menuItems = [
            'dashboard' => ['route' => 'admin.dashboard', 'icon' => 'bi-grid-1x2', 'label' => 'Dashboard', 'permission' => null, 'section' => 'principal'],
            'view-projets' => ['route' => 'admin.projet.index', 'icon' => 'bi-kanban', 'label' => 'Projets', 'permission' => 'view-projets', 'section' => 'projets'],
            'view-taches' => ['route' => 'admin.taches.index', 'icon' => 'bi-check2-square', 'label' => 'Tâches', 'permission' => 'view-taches', 'section' => 'projets'],
            'view-sous-taches' => ['route' => 'admin.sous-taches.index', 'icon' => 'bi-check2-all', 'label' => 'Sous-Tâches', 'permission' => 'view-sous-taches', 'section' => 'projets'],
            'view-phases' => ['route' => 'admin.phases.index', 'icon' => 'bi-list-check', 'label' => 'Phases', 'permission' => 'view-phases', 'section' => 'projets'],
            'view-incidents' => ['route' => 'admin.incidents.index', 'icon' => 'bi-exclamation-triangle', 'label' => 'Incidents', 'permission' => 'view-incidents', 'section' => 'projets'],
            'budgets-index' => ['route' => 'admin.budget.index', 'icon' => 'bi-cash-stack', 'label' => 'Budgets', 'permission' => 'gerer-budgets', 'section' => 'projets'],
            'budgets-create' => ['route' => 'admin.budget.create', 'icon' => 'bi-calendar-plus', 'label' => 'Définir Budget Annuel', 'permission' => 'gerer-budgets', 'section' => 'projets'],
            'alloc-project-budget' => ['route' => 'admin.budget.index', 'icon' => 'bi-briefcase', 'label' => 'Allocation par Projet', 'permission' => 'alloc-project-budget', 'section' => 'projets'],
            'alloc-st-budget' => ['route' => 'admin.budget.index', 'icon' => 'bi-arrow-repeat', 'label' => 'Allocation Sous-Traitance', 'permission' => 'alloc-st-budget', 'section' => 'projets'],
            'manage-depenses' => ['route' => 'admin.budget.index', 'icon' => 'bi-receipt', 'label' => 'Gestion Dépenses', 'permission' => 'manage-depenses', 'section' => 'projets'],
            'view-budget-history' => ['route' => 'admin.historique.index', 'icon' => 'bi-clock-history', 'label' => 'Historique Budgets', 'permission' => 'gerer-budgets', 'section' => 'projets'],
            'view-rapports' => ['route' => 'admin.rapports.index', 'icon' => 'bi-file-earmark-bar-graph', 'label' => 'Rapports', 'permission' => 'view-rapports', 'section' => 'projets'],
            'view-utilisateurs' => ['route' => 'admin.users.index', 'icon' => 'bi-people', 'label' => 'Utilisateurs', 'permission' => 'view-utilisateurs', 'section' => 'rh'],
            'view-roles-permissions' => ['route' => 'admin.roles.index', 'icon' => 'bi-shield-check', 'label' => 'Rôles & Permissions', 'permission' => 'view-roles-permissions', 'section' => 'rh'],
            'view-equipes' => ['route' => 'admin.equipes.index', 'icon' => 'bi-people-fill', 'label' => 'Équipes', 'permission' => 'view-equipes', 'section' => 'rh'],
            'view-partenaires' => ['route' => 'admin.partenaires.index', 'icon' => 'bi-person', 'label' => 'Partenaires', 'permission' => 'view-partenaires', 'section' => 'partenaires'],
            'view-contrats' => ['route' => 'admin.contrats.index', 'icon' => 'bi-file-earmark-text', 'label' => 'Contrats', 'permission' => 'view-contrats', 'section' => 'partenaires'],
            'view-factures' => ['route' => 'admin.factures.index', 'icon' => 'bi-receipt', 'label' => 'Factures', 'permission' => 'view-factures', 'section' => 'partenaires'],
            'view-interventions' => ['route' => 'admin.interventions.index', 'icon' => 'bi-wrench', 'label' => 'Interventions', 'permission' => 'view-interventions', 'section' => 'operations'],
            'view-sous-traitances' => ['route' => 'admin.sous-traitance.index', 'icon' => 'bi-arrow-repeat', 'label' => 'Sous-Traitances', 'permission' => 'view-sous-traitances', 'section' => 'operations'],
            'view-rendez-vous' => ['route' => 'admin.rendezvous.index', 'icon' => 'bi-calendar-event', 'label' => 'Rendez-vous', 'permission' => 'view-rendez-vous', 'section' => 'docs'],
            'view-documents' => ['route' => 'admin.documents.index', 'icon' => 'bi-file-earmark', 'label' => 'Documents', 'permission' => 'view-documents', 'section' => 'docs'],
            'chat-messagerie' => ['route' => 'admin.chat.index', 'icon' => 'bi-chat-dots', 'label' => 'Messagerie', 'permission' => 'chat-messagerie-activer', 'section' => 'docs'],
            'view-historique' => ['route' => 'admin.historique.index', 'icon' => 'bi-clock-history', 'label' => 'Historique', 'permission' => 'view-historique', 'section' => 'docs'],
            'view-base-de-donnees' => ['route' => 'admin.database.index', 'icon' => 'bi-database', 'label' => 'Base de données', 'permission' => 'view-base-de-donnees', 'section' => 'systeme'],
        ];
    }

    // Filter based on permissions
    $visibleItems = [];
    $hasDatabaseAccess = count(array_intersect($databasePermissions, $userPermissions)) > 0;

    $rolesPermissions = ['view-roles-permissions', 'create-roles-permissions', 'edit-roles-permissions', 'delete-roles-permissions', 'export-roles-permissions'];
    $hasRolesAccess = count(array_intersect($rolesPermissions, $userPermissions)) > 0;

    foreach ($menuItems as $key => $item) {
        $isDatabaseItem = $key === 'view-base-de-donnees' || $key === 'database';
        $isRolesItem = $key === 'view-roles-permissions' || $key === 'roles-permissions';
        $isDashboard = $key === 'dashboard';

        $permCheck = $item['permission'] ?? null;
        $hasPerm = $permCheck === null
            || (is_array($permCheck) && count(array_intersect($permCheck, $userPermissions)) > 0)
            || (is_string($permCheck) && in_array($permCheck, $userPermissions));

        if (
            $sidebarType === 'super-admin'
            || $isDashboard
            || $hasPerm
            || ($isDatabaseItem && $hasDatabaseAccess)
            || ($isRolesItem && $hasRolesAccess)
        ) {
            $visibleItems[$key] = $item;
        }
    }

    // Group by sections
    $sections = [
        'principal' => ['title' => 'Principal', 'icon' => 'bi-house'],
        'projets' => ['title' => 'Gestion de Projets', 'icon' => 'bi-briefcase'],
        'rh' => ['title' => 'Ressources Humaines', 'icon' => 'bi-people'],
        'partenaires' => ['title' => 'Partenaires & Contrats', 'icon' => 'bi-person-workspace'],
        'budget' => ['title' => 'Budgets', 'icon' => 'bi-cash-stack'],
        'operations' => ['title' => 'Opérations Terrain', 'icon' => 'bi-tools'],
        'stocks' => ['title' => 'Stocks & Fournisseurs', 'icon' => 'bi-boxes'],
        'docs' => ['title' => 'Documents & Communication', 'icon' => 'bi-folder2-open'],
        'systeme' => ['title' => 'Système', 'icon' => 'bi-gear'],
        'gestion' => ['title' => 'Gestion', 'icon' => 'bi-kanban'],
        'espace' => ['title' => 'Espace', 'icon' => 'bi-grid'],
        'dynamic' => ['title' => 'Mes Fonctionnalités', 'icon' => 'bi-grid-3x3-gap'],
    ];
@endphp

<nav class="app-sidebar-nav">
    @foreach($sections as $sectionKey => $sectionConfig)
        @php
            $sectionItems = array_filter($visibleItems, fn($item) => ($item['section'] ?? '') === $sectionKey);
        @endphp
        @if(count($sectionItems) > 0)
            <div class="app-nav-section">
                <div class="app-nav-section-title">
                    <i class="bi {{ $sectionConfig['icon'] }}"></i>
                    <span>{{ $sectionConfig['title'] }}</span>
                </div>
                @foreach($sectionItems as $key => $item)
                    @php
                        $baseRoute = $item['route'];
                        $routeParts = explode('.', $baseRoute);
                        if (count($routeParts) >= 2) {
                            $routePrefix = $routeParts[0] . '.' . $routeParts[1];
                        } else {
                            $routePrefix = $baseRoute;
                        }
                        $routePattern = $routePrefix . '*';
                        $isActive = request()->routeIs($routePattern);
                        $url = route($item['route']);
                    @endphp
                    <a href="{{ $url }}" class="app-nav-item {{ $isActive ? 'active' : '' }}">
                        <i class="bi {{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                        @if($isActive)
                            <span class="app-nav-active-indicator"></span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    @endforeach
</nav>