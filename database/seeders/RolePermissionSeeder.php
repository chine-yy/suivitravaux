<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
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

        $actions = [
            'view' => 'Voir',
            'create' => 'Créer',
            'edit' => 'Modifier',
            'delete' => 'Supprimer',
            'exporter-pdf' => 'Exporter PDF',
        ];

        foreach ($modules as $moduleSlug => $moduleData) {
            foreach ($actions as $actionSlug => $actionLabel) {
                if (in_array($moduleSlug, ['dashboard', 'configuration']) && $actionSlug !== 'view') {
                    continue;
                }

                if (in_array($moduleSlug, ['messagerie', 'ia-chat-box'])) {
                    continue;
                }

                if ($moduleSlug === 'base-de-donnees' && in_array($actionSlug, ['create', 'edit', 'delete'])) {
                    continue;
                }

                if ($moduleSlug === 'historique' && in_array($actionSlug, ['create', 'edit', 'delete'])) {
                    continue;
                }

                if ($moduleSlug === 'logs' && in_array($actionSlug, ['create', 'edit', 'delete'])) {
                    continue;
                }

                if ($moduleSlug === 'budgets' && in_array($actionSlug, ['view', 'create', 'edit', 'delete', 'export', 'exporter-pdf'])) {
                    continue;
                }

                if (in_array($moduleSlug, ['budget-allocation-projet', 'budget-allocation-sous-traitance']) && $actionSlug === 'create') {
                    continue;
                }

                if ($moduleSlug === 'satisfaction-partenaire' && in_array($actionSlug, ['create', 'edit', 'delete'])) {
                    continue;
                }

                if ($moduleSlug === 'depenses' && in_array($actionSlug, ['view', 'export', 'exporter-pdf'])) {
                    continue;
                }

                Permission::updateOrCreate(
                    [
                        'slug' => $actionSlug . '-' . $moduleSlug,
                    ],
                    [
                        'nom' => $actionLabel . ' ' . $moduleData['nom'],
                        'module' => $moduleSlug,
                        'action' => $actionSlug,
                        'group' => $moduleData['group'],
                        'icon' => $moduleData['icon'],
                        'color' => \App\Models\Permission::$actionColors[$actionSlug] ?? 'secondary',
                    ]
                );
            }
        }

        // Add special permissions
        $specialPermissions = [
            ['nom' => 'Sauvegarder Base de données', 'slug' => 'sauvegarde-base-de-donnees', 'module' => 'base-de-donnees', 'action' => 'sauvegarde'],
            ['nom' => 'Vider Base de données', 'slug' => 'clear-base-de-donnees', 'module' => 'base-de-donnees', 'action' => 'clear'],
            ['nom' => 'Activer IA Chat Box', 'slug' => 'activer-ia-chat-box', 'module' => 'ia-chat-box', 'action' => 'activer'],
            ['nom' => 'Exporter PDF Logs', 'slug' => 'exporter-pdf-logs', 'module' => 'logs', 'action' => 'exporter-pdf'],
            ['nom' => 'Exporter PDF Rôles', 'slug' => 'exporter-pdf-roles-permissions', 'module' => 'roles-permissions', 'action' => 'exporter-pdf'],
            ['nom' => 'Exporter PDF Utilisateurs', 'slug' => 'exporter-pdf-utilisateurs', 'module' => 'utilisateurs', 'action' => 'exporter-pdf'],
            ['nom' => 'Nettoyer Logs', 'slug' => 'clear-logs', 'module' => 'logs', 'action' => 'clear'],
            ['nom' => 'Gérer Budgets', 'slug' => 'gerer-budgets', 'module' => 'budgets', 'action' => 'gerer'],
            ['nom' => 'Activer', 'slug' => 'chat-messagerie-activer', 'module' => 'messagerie', 'action' => 'activer'],
            ['nom' => 'Super Admin', 'slug' => 'chat-super-admin', 'module' => 'messagerie', 'action' => 'super-admin'],
            ['nom' => 'Envoyer au partenaire Contrats', 'slug' => 'envoyer-partenaire-contrats', 'module' => 'contrats', 'action' => 'envoyer-partenaire'],
        ];

        // Supprimer les permissions obsolètes du module base-de-donnees et messagerie
        Permission::whereIn('slug', [
            'export-base-de-donnees',
            'exporter-pdf-base-de-donnees',
            'delete-base-de-donnees',
            'view-budgets',
            'edit-budgets',
            'create-budgets',
            'delete-budgets',
            'create-satisfaction-partenaire',
            'edit-satisfaction-partenaire',
            'delete-satisfaction-partenaire',
            'upload-documents',
        ])->delete();

        foreach ($specialPermissions as $sp) {
            Permission::firstOrCreate(['slug' => $sp['slug']], $sp);
        }
  }
}
