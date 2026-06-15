<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'slug',
        'module',
        'action',
        'group',
        'icon',
        'color',
    ];

    public static $actionLabels = [
        'view' => 'Voir',
        'create' => 'Créer',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'export' => 'Exporter',
        'reordonner' => 'Réordonner',
        'toggle' => 'Activer/Désactiver',
        'allouer-projet' => 'Allouer Projet',
        'allouer-sous-traitance' => 'Allouer sous-traitance',
        'archiver' => 'Archiver',
        'restaurer' => 'Restaurer',
        'envoyer-partenaire' => 'Envoyer Partenaire',
        'payer' => 'Payer',
        'upload' => 'Téléverser',
        'download' => 'Télécharger',
        'activer' => 'Activer',
        'valider' => 'Valider',
        'resolve' => 'Résoudre',
        'plan' => 'Planifier',
        'send' => 'Envoyer',
        'clear' => 'Vider',
        'sauvegarde' => 'Sauvegarder',
        'reset-password' => 'Reset Mot de passe',
        'exporter-pdf' => 'Exporter PDF',
        'manage' => 'Manage',
    ];

    public static $actionColors = [
        'view' => 'info',
        'create' => 'success',
        'edit' => 'warning',
        'delete' => 'danger',
        'export' => 'secondary',
        'reordonner' => 'info',
        'toggle' => 'warning',
        'allouer-projet' => 'success',
        'allouer-sous-traitance' => 'success',
        'archiver' => 'secondary',
        'restaurer' => 'success',
        'envoyer-partenaire' => 'info',
        'payer' => 'success',
        'upload' => 'primary',
        'download' => 'info',
        'activer' => 'success',
        'valider' => 'success',
        'resolve' => 'success',
        'plan' => 'warning',
        'send' => 'info',
        'clear' => 'danger',
        'sauvegarde' => 'primary',
        'reset-password' => 'warning',
        'exporter-pdf' => 'danger',
    ];

    private const LEGACY_ALIAS_TO_CANONICAL = [
        'activer-ia-chat' => 'activer-ia-chat-box',
        'view-historique-budgets' => 'view-historique',
        'allouer-projet-budgets' => 'alloc-project-budget',
    ];

    private const ACTION_PREFIXES = [
        'reset-password',
        'exporter-pdf',
        'allouer-sous-traitance',
        'envoyer-partenaire',
        'allouer-projet',
        'reordonner',
        'download',
        'sauvegarde',
        'export',
        'activer',
        'modifier',
        'effacer',
        'valider',
        'create',
        'delete',
        'upload',
        'manage',
        'view',
        'edit',
        'plan',
        'send',
        'voir',
        'alloc',
        'clear',
        'payer',
    ];

    private const ACTION_ALIASES = [
        'modifier' => 'edit',
        'voir' => 'view',
        'effacer' => 'clear',
        'alloc' => 'allouer-projet',
        'ai-chat' => 'activer',
    ];

    private const PERMISSION_OVERRIDES = [
        'activer-messagerie' => ['action' => 'activer', 'module' => 'messagerie'],
        'activer-ia-chat' => ['action' => 'activer', 'module' => 'ia-chat-box'],
        'activer-ia-chat-box' => ['action' => 'activer', 'module' => 'ia-chat-box'],
        'allouer-projet-budgets' => ['action' => 'allouer-projet', 'module' => 'budget-allocation-projet'],
        'alloc-project-budget' => ['action' => 'allouer-projet', 'module' => 'budget-allocation-projet'],
        'alloc-st-budget' => ['action' => 'allouer-sous-traitance', 'module' => 'budget-allocation-sous-traitance'],
        'chat-messagerie-activer' => ['action' => 'activer', 'module' => 'messagerie'],
        'allouer-sous-traitance-st' => ['action' => 'allouer-sous-traitance', 'module' => 'budget-allocation-sous-traitance'],
        'manage-depenses' => ['action' => 'manage', 'module' => 'depenses'],
        'view-historique-budgets' => ['action' => 'view', 'module' => 'historique'],
        'modifier-parametres' => ['action' => 'edit', 'module' => 'parametres'],
        'voir-logs' => ['action' => 'view', 'module' => 'logs'],
        'effacer-logs' => ['action' => 'clear', 'module' => 'logs'],
        'exporter-logs' => ['action' => 'export', 'module' => 'logs'],
        'exporter-interventions' => ['action' => 'export', 'module' => 'interventions'],
        'exporter-rendezvous' => ['action' => 'export', 'module' => 'rendez-vous'],
        'exporter-pdf-rendezvous' => ['action' => 'exporter-pdf', 'module' => 'rendez-vous'],
        'exporter-pdf-projets' => ['action' => 'exporter-pdf', 'module' => 'projets'],
        'exporter-pdf-taches' => ['action' => 'exporter-pdf', 'module' => 'taches'],
        'exporter-pdf-sous-taches' => ['action' => 'exporter-pdf', 'module' => 'sous-taches'],
        'exporter-pdf-incidents' => ['action' => 'exporter-pdf', 'module' => 'incidents'],
        'exporter-pdf-phases' => ['action' => 'exporter-pdf', 'module' => 'phases'],
    ];

    private const MODULE_ALIASES = [
        'ia-chat' => 'ia-chat-box',
        'historique-budgets' => 'historique',
        'st-budget' => 'budget-allocation-sous-traitance',
        'project-budget' => 'budget-allocation-projet',
        'sous-traitance' => 'sous-traitances',
        'sous-traitances' => 'sous-traitances',
        'soustaches' => 'sous-taches',
        'sous-tache' => 'sous-taches',
        'stock' => 'stocks-materiaux',
        'stocks' => 'stocks-materiaux',
        'rendezvous' => 'rendez-vous',
    ];

    private const MODULE_CATALOG = [
        'dashboard' => ['nom' => 'Dashboard', 'group' => 'Gestion Globale', 'icon' => 'speedometer2'],
        'base-de-donnees' => ['nom' => 'Base de donnees', 'group' => 'Gestion Globale', 'icon' => 'database'],
        'historique' => ['nom' => 'Historique', 'group' => 'Gestion Globale', 'icon' => 'clock-history'],
        'configuration' => ['nom' => 'Configuration', 'group' => 'Gestion Globale', 'icon' => 'gear'],
        'logs' => ['nom' => 'Logs', 'group' => 'Gestion Globale', 'icon' => 'file-text'],
        'parametres' => ['nom' => 'Parametres', 'group' => 'Gestion Globale', 'icon' => 'sliders'],
        'projets' => ['nom' => 'Projets', 'group' => 'Projets & Execution', 'icon' => 'kanban'],
        'phases' => ['nom' => 'Phases', 'group' => 'Projets & Execution', 'icon' => 'collection'],
        'taches' => ['nom' => 'Taches', 'group' => 'Projets & Execution', 'icon' => 'check2-square'],
        'sous-taches' => ['nom' => 'Sous-Taches', 'group' => 'Projets & Execution', 'icon' => 'list-check'],
        'incidents' => ['nom' => 'Incidents', 'group' => 'Projets & Execution', 'icon' => 'exclamation-triangle'],
        'budgets' => ['nom' => 'Budgets', 'group' => 'Projets & Execution', 'icon' => 'cash-stack'],
        'budget-allocation-projet' => ['nom' => 'Budget Allocation Projet', 'group' => 'Projets & Execution', 'icon' => 'diagram-3'],
        'budget-allocation-sous-traitance' => ['nom' => 'Budget Allocation Sous-Traitance', 'group' => 'Projets & Execution', 'icon' => 'people'],
        'depenses' => ['nom' => 'Depenses', 'group' => 'Projets & Execution', 'icon' => 'receipt'],
        'rapports' => ['nom' => 'Rapports', 'group' => 'Projets & Execution', 'icon' => 'file-earmark-bar-graph'],
        'utilisateurs' => ['nom' => 'Utilisateurs', 'group' => 'Ressources Humaines', 'icon' => 'people'],
        'roles-permissions' => ['nom' => 'Roles & Permissions', 'group' => 'Ressources Humaines', 'icon' => 'shield-lock'],
        'equipes' => ['nom' => 'Equipes', 'group' => 'Ressources Humaines', 'icon' => 'person-lines-fill'],
        'partenaires' => ['nom' => 'Partenaires', 'group' => 'Partenaires & Partenaires', 'icon' => 'person-badge'],
        'contrats' => ['nom' => 'Contrats', 'group' => 'Partenaires & Partenaires', 'icon' => 'file-earmark-text'],
        'factures' => ['nom' => 'Factures', 'group' => 'Partenaires & Partenaires', 'icon' => 'receipt'],
        'satisfaction-partenaire' => ['nom' => 'Satisfaction Partenaire', 'group' => 'Partenaires & Partenaires', 'icon' => 'emoji-smile'],
        'interventions' => ['nom' => 'Interventions', 'group' => 'Interventions', 'icon' => 'tools'],
        'fournisseurs' => ['nom' => 'Fournisseurs', 'group' => 'Fournisseurs & Stocks', 'icon' => 'truck'],
        'stocks-materiaux' => ['nom' => 'Stocks Materiaux', 'group' => 'Fournisseurs & Stocks', 'icon' => 'box-seam'],
        'sous-traitances' => ['nom' => 'Sous-Traitances', 'group' => 'Fournisseurs & Stocks', 'icon' => 'briefcase'],
        'rendez-vous' => ['nom' => 'Rendez-vous', 'group' => 'Rendez-vous', 'icon' => 'calendar-event'],
        'documents' => ['nom' => 'Documents', 'group' => 'Documents', 'icon' => 'folder2-open'],
        'messagerie' => ['nom' => 'Messagerie', 'group' => 'Communication', 'icon' => 'chat-dots'],
        'ia-chat-box' => ['nom' => 'IA Chat Box', 'group' => 'Communication', 'icon' => 'chat-square-quote'],
    ];

    private const GROUP_ORDER = [
        'Gestion Globale',
        'Projets & Execution',
        'Ressources Humaines',
        'Partenaires & Partenaires',
        'Interventions',
        'Fournisseurs & Stocks',
        'Rendez-vous',
        'Documents',
        'Communication',
        'Configuration',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_permissions');
    }

    /**
     * Valide les permissions: view obligatoire pour toute autre action par module
     * @param array $permissionIds IDs des permissions sélectionnées
     * @return array ['valid' => bool, 'errors' => []]
     */
    public static function validatePermissions(array $permissionIds): array
    {
        if (empty($permissionIds)) {
            return ['valid' => true, 'errors' => []];
        }

        $permissions = self::whereIn('id', $permissionIds)->get()->keyBy('id');
        $errors = [];

        // Grouper par module normalise.
        $modulePermissions = [];
        foreach ($permissions as $id => $perm) {
            $resolved = self::resolvePermissionAttributesFromModel($perm);
            $moduleSlug = $resolved['module'];
            $action = $resolved['action'];

            if (!isset($modulePermissions[$moduleSlug])) {
                $modulePermissions[$moduleSlug] = ['view' => false, 'others' => []];
            }

            if ($action === 'view') {
                $modulePermissions[$moduleSlug]['view'] = true;
            } else {
                $modulePermissions[$moduleSlug]['others'][] = $resolved['nom'];
            }
        }

        foreach ($modulePermissions as $moduleSlug => $moduleData) {
            if ($moduleData['others'] && !$moduleData['view']) {
                $moduleName = self::moduleDisplayName($moduleSlug);
                $viewId = 'inconnue';
                $errors[] = "Module '{$moduleName}': 'view' (ID: {$viewId}) obligatoire pour: " . implode(', ', $moduleData['others']);
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    public static function getGroupedPermissions($permissions = null): array
    {
        $permissions = match (true) {
            $permissions instanceof Collection => $permissions,
            is_array($permissions) => collect($permissions),
            default => self::query()->get(),
        };

        $permissions = $permissions
            ->filter(fn($permission) => $permission instanceof self)
            ->reject(fn(self $permission) => self::isRolePermission($permission))
            ->reject(fn(self $permission) => $permission->module === 'dashboard')
            ->values();

        $availableSlugs = $permissions
            ->map(fn(self $permission) => self::normalizeSlug($permission->slug))
            ->filter()
            ->all();

        $grouped = [];

        foreach ($permissions as $permission) {
            $slug = self::normalizeSlug($permission->slug);
            if (self::shouldHideLegacyAlias($slug, $availableSlugs)) {
                continue;
            }

            $resolved = self::resolvePermissionAttributesFromModel($permission);
            $group = $resolved['group'];
            $module = $resolved['module'];

            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }

            if (!isset($grouped[$group][$module])) {
                $grouped[$group][$module] = [
                    'nom' => $resolved['module_name'],
                    'icon' => $resolved['icon'],
                    'permissions' => [],
                    '_seen_actions' => [],
                ];
            }

            $actionKey = $resolved['action'] ?: $resolved['slug'];
            if (in_array($actionKey, $grouped[$group][$module]['_seen_actions'], true)) {
                continue;
            }

            $displayPermission = clone $permission;
            $displayPermission->setAttribute('slug', $resolved['slug']);
            $displayPermission->setAttribute('nom', $resolved['nom']);
            $displayPermission->setAttribute('module', $resolved['module']);
            $displayPermission->setAttribute('action', $resolved['action']);
            $displayPermission->setAttribute('group', $resolved['group']);
            $displayPermission->setAttribute('icon', $resolved['icon']);
            $displayPermission->setAttribute('color', $resolved['color']);

            $grouped[$group][$module]['permissions'][] = $displayPermission;
            $grouped[$group][$module]['_seen_actions'][] = $actionKey;
        }

        $groupOrder = array_flip(self::GROUP_ORDER);
        uksort($grouped, function (string $a, string $b) use ($groupOrder): int {
            $aPos = $groupOrder[$a] ?? 999;
            $bPos = $groupOrder[$b] ?? 999;
            if ($aPos === $bPos) {
                return strcmp($a, $b);
            }

            return $aPos <=> $bPos;
        });

        foreach ($grouped as $groupName => &$modules) {
            uasort($modules, fn(array $left, array $right) => strcmp($left['nom'], $right['nom']));

            foreach ($modules as &$moduleData) {
                usort($moduleData['permissions'], function (self $left, self $right): int {
                    return self::actionSortOrder((string) $left->action) <=> self::actionSortOrder((string) $right->action);
                });

                unset($moduleData['_seen_actions']);
            }
        }
        unset($modules, $moduleData);

        return $grouped;
    }

    public static function resolvePermissionAttributesFromSlug(string $slug, array $fallback = []): array
    {
        $normalizedSlug = self::normalizeSlug($slug);
        $override = self::PERMISSION_OVERRIDES[$normalizedSlug] ?? [];

        [$parsedAction, $parsedModule] = self::splitActionAndModule($normalizedSlug);

        $action = self::normalizeAction(
            $override['action']
            ?? ($fallback['action'] ?? $parsedAction)
        );

        $module = self::normalizeModuleSlug(
            $override['module']
            ?? ($fallback['module'] ?? $parsedModule)
        );

        if ($module === '') {
            $module = 'configuration';
        }

        $moduleMeta = self::MODULE_CATALOG[$module] ?? null;

        $rawGroup = trim((string) ($override['group'] ?? ($fallback['group'] ?? '')));
        $rawGroupNormalized = self::normalizeSearchValue($rawGroup);
        $group = ($rawGroup !== '' && !in_array($rawGroupNormalized, ['autre', 'autres'], true))
            ? $rawGroup
            : ($moduleMeta['group'] ?? 'Configuration');

        $icon = $override['icon']
            ?? ($fallback['icon'] ?? ($moduleMeta['icon'] ?? 'circle'));

        $color = $override['color']
            ?? ($fallback['color'] ?? (self::$actionColors[$action] ?? 'secondary'));

        $moduleName = $override['module_name']
            ?? ($moduleMeta['nom'] ?? self::moduleDisplayName($module));

        $label = self::$actionLabels[$action] ?? ucfirst(str_replace('-', ' ', $action));
        $nom = $override['nom'] ?? trim($label . ' ' . $moduleName);

        return [
            'slug' => $normalizedSlug,
            'nom' => $nom,
            'module' => $module,
            'action' => $action,
            'group' => $group,
            'icon' => $icon,
            'color' => $color,
            'module_name' => $moduleName,
        ];
    }

    private static function resolvePermissionAttributesFromModel(self $permission): array
    {
        return self::resolvePermissionAttributesFromSlug((string) $permission->slug, [
            'nom' => $permission->nom,
            'module' => $permission->module,
            'action' => $permission->action,
            'group' => $permission->group,
            'icon' => $permission->icon,
            'color' => $permission->color,
        ]);
    }

    private static function shouldHideLegacyAlias(string $slug, array $availableSlugs): bool
    {
        if (!isset(self::LEGACY_ALIAS_TO_CANONICAL[$slug])) {
            return false;
        }

        return in_array(self::LEGACY_ALIAS_TO_CANONICAL[$slug], $availableSlugs, true);
    }

    private static function splitActionAndModule(string $slug): array
    {
        foreach (self::ACTION_PREFIXES as $prefix) {
            $token = $prefix . '-';
            if (str_starts_with($slug, $token)) {
                return [$prefix, substr($slug, strlen($token))];
            }
        }

        return [null, $slug];
    }

    private static function normalizeAction(?string $action): string
    {
        $action = self::normalizeSlug((string) $action);
        if ($action === '') {
            return 'view';
        }

        return self::ACTION_ALIASES[$action] ?? $action;
    }

    private static function normalizeModuleSlug(?string $module): string
    {
        $module = self::normalizeSlug((string) $module);
        if ($module === '') {
            return '';
        }

        return self::MODULE_ALIASES[$module] ?? $module;
    }

    private static function moduleDisplayName(string $module): string
    {
        $module = self::normalizeModuleSlug($module);
        if (isset(self::MODULE_CATALOG[$module])) {
            return self::MODULE_CATALOG[$module]['nom'];
        }

        $name = str_replace('-', ' ', $module);
        $name = preg_replace_callback('/\b(ia|ai|chat|box)\b/i', static function (array $matches): string {
            return strtoupper($matches[0]);
        }, $name);

        return ucfirst((string) $name);
    }

    private static function actionSortOrder(string $action): int
    {
        static $order = [
        'view' => 20,
        'create' => 30,
        'edit' => 40,
        'delete' => 50,
        'upload' => 60,
        'download' => 70,
        'allouer-projet' => 80,
        'activer' => 90,
        'plan' => 100,
        'valider' => 110,
        'payer' => 120,
        'envoyer-partenaire' => 130,
        'reset-password' => 140,
        'clear' => 150,
        'exporter-pdf' => 160,
        'export' => 170,
        'sauvegarde' => 180,
        'reordonner' => 190,
        'manage' => 15,
        ];

        return $order[$action] ?? 999;
    }

    private static function normalizeSlug(string $value): string
    {
        return Str::of($value)
            ->trim()
            ->lower()
            ->replace('_', '-')
            ->__toString();
    }

    /**
     * Exclut les permissions liées à la gestion des rôles depuis la matrice UI.
     */
    private static function isRolePermission(self $permission): bool
    {
        $normalizedModule = self::normalizeModuleSlug($permission->module);
        if ($normalizedModule === 'roles-permissions') {
            return false;
        }

        $fields = [
            $permission->module,
            $permission->slug,
            $permission->nom,
        ];

        foreach ($fields as $field) {
            $normalized = self::normalizeSearchValue($field);
            if (preg_match('/(^|[^a-z0-9])(roles?|parametres?)([^a-z0-9]|$)/', $normalized)) {
                return true;
            }
        }

        return false;
    }

    private static function normalizeSearchValue(?string $value): string
    {
        $value = Str::lower((string) $value);

        return strtr($value, [
            'à' => 'a',
            'â' => 'a',
            'ä' => 'a',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'î' => 'i',
            'ï' => 'i',
            'ô' => 'o',
            'ö' => 'o',
            'ù' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ç' => 'c',
        ]);
    }

    public static function getAllSlugs()
    {
        return self::pluck('slug')->toArray();
    }

    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
}
