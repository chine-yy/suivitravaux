<?php

namespace App\Support;

final class PermissionSlugResolver
{
    /**
     * Legacy/cross-version aliases that do not follow simple patterns.
     */
    private const SPECIAL_ALIASES = [
        'activer-ia-chat-box' => ['activer-ia-chat'],
        'activer-ia-chat' => ['activer-ia-chat-box'],
        'alloc-project-budget' => ['allouer-projet-budgets'],
        'allouer-projet-budgets' => ['alloc-project-budget'],
        'manage-depenses' => ['view-depenses'],
        'view-historique' => ['view-historique-budgets'],
        'view-historique-budgets' => ['view-historique'],
        'visualiser-stocks' => ['view-stocks', 'voir-stocks'],
        'voir-stocks' => ['view-stocks', 'visualiser-stocks'],
        'visualiser-stock' => ['view-stocks', 'voir-stock'],
        'voir-stock' => ['view-stocks', 'visualiser-stock'],
    ];

    /**
     * Known module singular/plural variants used across old/new permissions.
     */
    private const MODULE_VARIANTS = [
        'projet' => ['projet', 'projets'],
        'projets' => ['projet', 'projets'],
        'phase' => ['phase', 'phases'],
        'phases' => ['phase', 'phases'],
        'tache' => ['tache', 'taches'],
        'taches' => ['tache', 'taches'],
        'sous-tache' => ['sous-tache', 'sous-taches'],
        'sous-taches' => ['sous-tache', 'sous-taches'],
        'rapport' => ['rapport', 'rapports'],
        'rapports' => ['rapport', 'rapports'],
        'incident' => ['incident', 'incidents'],
        'incidents' => ['incident', 'incidents'],
        'intervention' => ['intervention', 'interventions'],
        'interventions' => ['intervention', 'interventions'],
        'document' => ['document', 'documents'],
        'documents' => ['document', 'documents'],
        'membre' => ['membre', 'membres'],
        'membres' => ['membre', 'membres'],
        'partenaire' => ['partenaire', 'partenaires'],
        'partenaires' => ['partenaire', 'partenaires'],
        'utilisateur' => ['utilisateur', 'utilisateurs'],
        'utilisateurs' => ['utilisateur', 'utilisateurs'],
        'equipe' => ['equipe', 'equipes'],
        'equipes' => ['equipe', 'equipes'],
        'budget' => ['budget', 'budgets'],
        'budgets' => ['budget', 'budgets'],
        'depense' => ['depense', 'depenses'],
        'depenses' => ['depense', 'depenses'],
        'stock' => ['stock', 'stocks', 'stock-materiaux', 'stocks-materiaux'],
        'stocks' => ['stock', 'stocks', 'stock-materiaux', 'stocks-materiaux'],
        'stocks-materiaux' => ['stock', 'stocks', 'stock-materiaux', 'stocks-materiaux'],
        'fournisseur' => ['fournisseur', 'fournisseurs'],
        'fournisseurs' => ['fournisseur', 'fournisseurs'],
        'contrat' => ['contrat', 'contrats'],
        'contrats' => ['contrat', 'contrats'],
        'facture' => ['facture', 'factures'],
        'factures' => ['facture', 'factures'],
        'rendez-vous' => ['rendez-vous', 'rendez_vous', 'rendezvous'],
        'rendez_vous' => ['rendez-vous', 'rendez_vous', 'rendezvous'],
        'rendezvous' => ['rendez-vous', 'rendez_vous', 'rendezvous'],
        'historique' => ['historique'],
        'configuration' => ['configuration', 'configurations'],
        'logs' => ['logs'],
        'roles' => ['roles', 'roles-permissions', 'role'],
        'roles-permissions' => ['roles', 'roles-permissions'],
    ];

    /**
     * Prefixes recognized as permission actions.
     */
    private const ACTION_PREFIXES = [
        'reset-password',
        'exporter-pdf',
        'envoyer-partenaire',
        'allouer-projet',
        'reordonner',
        'archiver',
        'restaurer',
        'modifier',
        'activer',
        'manage',
        'alloc',
        'acces',
        'view',
        'create',
        'edit',
        'delete',
        'upload',
        'download',
        'export',
        'clear',
        'valider',
        'plan',
        'send',
        'payer',
        'voir',
        'visualiser',
        'generer',
        'creer',
        'modifier',
        'desactiver',
        'supprimer',
        'editer',
        'envoyer',
        'televerser',
        'telecharger',
        'exporter',
    ];

    private const ACTION_ALIASES = [
        'voir' => 'view',
        'visualiser' => 'view',
        'generer' => 'create',
        'creer' => 'create',
        'editer' => 'edit',
        'modifier' => 'edit',
        'desactiver' => 'delete',
        'supprimer' => 'delete',
        'envoyer' => 'send',
        'televerser' => 'upload',
        'telecharger' => 'download',
        'exporter' => 'export',
    ];

    public static function aliases(string $permission): array
    {
        $permission = self::normalize($permission);
        if ($permission === '') {
            return [];
        }

        $aliases = [$permission];

        foreach (self::SPECIAL_ALIASES[$permission] ?? [] as $alias) {
            $aliases[] = self::normalize($alias);
        }

        [$action, $module] = self::splitActionAndModule($permission);

        if ($action === null) {
            foreach (self::moduleVariants($permission) as $variant) {
                $aliases[] = $variant;
                $aliases[] = 'view-' . $variant;
            }
        } else {
            $normalizedAction = self::ACTION_ALIASES[$action] ?? $action;
            $moduleNormalized = $module ?? '';

            foreach (self::moduleVariants($moduleNormalized) as $variant) {
                $aliases[] = $normalizedAction . '-' . $variant;

                if ($normalizedAction === 'view') {
                    $aliases[] = $variant;
                }
            }

            if (isset(self::ACTION_ALIASES[$action])) {
                $aliases[] = self::ACTION_ALIASES[$action] . '-' . $moduleNormalized;
            }

            if ($action !== $normalizedAction) {
                $aliases[] = $normalizedAction . '-' . $moduleNormalized;
            }
        }

        return array_values(array_unique(array_filter($aliases)));
    }

    public static function matches(string $requestedPermission, array $userPermissionSlugs): bool
    {
        if (empty($userPermissionSlugs)) {
            return false;
        }

        $userSlugs = array_map([self::class, 'normalize'], $userPermissionSlugs);
        $requestedAliases = self::aliases($requestedPermission);

        $allUserAliases = [];
        foreach ($userSlugs as $slug) {
            $allUserAliases = array_merge($allUserAliases, self::aliases($slug));
        }

        return count(array_intersect($requestedAliases, $allUserAliases)) > 0;
    }

    private static function splitActionAndModule(string $permission): array
    {
        foreach (self::ACTION_PREFIXES as $action) {
            $prefix = $action . '-';
            if (str_starts_with($permission, $prefix)) {
                return [$action, substr($permission, strlen($prefix))];
            }
        }

        return [null, null];
    }

    private static function moduleVariants(string $module): array
    {
        $module = self::normalize($module);
        if ($module === '') {
            return [];
        }

        if (isset(self::MODULE_VARIANTS[$module])) {
            return self::MODULE_VARIANTS[$module];
        }

        $variants = [$module];
        if (str_ends_with($module, 's')) {
            $variants[] = substr($module, 0, -1);
        } else {
            $variants[] = $module . 's';
        }

        return array_values(array_unique(array_filter($variants)));
    }

    private static function normalize(string $permission): string
    {
        return strtolower(trim(str_replace('_', '-', $permission)));
    }
}
