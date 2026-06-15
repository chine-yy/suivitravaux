<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom');
            $table->string('slug')->unique();
            $table->string('module')->nullable();
            $table->string('action')->nullable();
            $table->string('group')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        $actionLabels = [
            'view' => 'Voir',
            'create' => 'Creer',
            'edit' => 'Modifier',
            'delete' => 'Supprimer',
            'export' => 'Exporter',
            'toggle' => 'Activer/Desactiver',
            'envoyer-partenaire' => 'Envoyer au partenaire',
            'upload' => 'Telecharger',
            'download' => 'Telecharger',
            'activer' => 'Activer',
            'valider' => 'Valider',
            'resolve' => 'Resoudre',
            'plan' => 'Planifier',
            'send' => 'Envoyer',
            'clear' => 'Vider',
            'sauvegarde' => 'Sauvegarder',
            'reset-password' => 'Reset Mot de passe',
            'exporter-pdf' => 'Exporter PDF',
            'acces' => 'Acces',
            'ia-chat-box' => 'IA Chat Box',
            'gerer' => 'Gérer',
            'chat-partenaire' => 'Partenaire',
            'chat-super-admin' => 'Super Admin',
            'chat-admin-entreprise' => 'Administrateur Entreprise',
            'chat-acteur-projet' => 'Acteur projet',
        ];

        $actionColors = [
            'view' => 'info',
            'create' => 'success',
            'edit' => 'warning',
            'delete' => 'danger',
            'export' => 'secondary',
            'toggle' => 'warning',
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
            'acces' => 'dark',
            'gerer' => 'primary',
            'chat-partenaire' => 'info',
            'chat-super-admin' => 'dark',
            'chat-admin-entreprise' => 'primary',
            'chat-acteur-projet' => 'success',
        ];

        $hierarchie = [
            'Gestion Globale' => [
                'Dashboard' => [
                    'icon' => 'speedometer2',
                    'actions' => ['view']
                ],
                'Base de donnees' => [
                    'icon' => 'database',
                    'actions' => ['view', 'clear', 'sauvegarde', 'exporter-pdf']
                ],
                'Historique' => [
                    'icon' => 'clock-history',
                    'actions' => ['view', 'exporter-pdf']
                ],
                'Configuration' => [
                    'icon' => 'gear',
                    'actions' => ['view']
                ],
                'Logs' => [
                    'icon' => 'file-text',
                    'actions' => ['view', 'clear', 'exporter-pdf']
                ],
            ],
            'Projets & Execution' => [
                'Projets' => [
                    'icon' => 'kanban',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
                'Phases' => [
                    'icon' => 'list-check',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
                'Taches' => [
                    'icon' => 'check2-square',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
                'Sous-Taches' => [
                    'icon' => 'check2-all',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
                'Incidents' => [
                    'icon' => 'exclamation-triangle',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
                'Budgets' => [
                    'icon' => 'cash-stack',
                    'actions' => ['gerer']
                ],
                'Budget Allocation Projet' => [
                    'icon' => 'diagram-3',
                    'actions' => ['view', 'edit', 'delete','exporter-pdf']
                ],
                'Budget Allocation Sous-Traitance' => [
                    'icon' => 'people',
                    'actions' => ['view', 'edit', 'delete','exporter-pdf']
                ],
                'Depenses' => [
                    'icon' => 'receipt',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
                'Rapports' => [
                    'icon' => 'file-earmark-bar-graph',
                    'actions' => ['view', 'create', 'edit', 'delete', 'valider', 'envoyer-partenaire', 'exporter-pdf']
                ],
            ],
            'Ressources Humaines' => [
                'Utilisateurs' => [
                    'icon' => 'people',
                    'actions' => ['view', 'create', 'edit', 'delete', 'reset-password','exporter-pdf']
                ],
                'Roles & Permissions' => [
                    'icon' => 'shield-lock',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
                'Equipes' => [
                    'icon' => 'person-lines-fill',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],

            ],
            'Partenaires & Partenaires' => [
                'Partenaires' => [
                    'icon' => 'person-badge',
                    'actions' => ['view', 'create', 'edit', 'delete', 'reset-password', 'exporter-pdf']
                ],
                'Contrats' => [
                    'icon' => 'file-earmark-text',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
                'Factures' => [
                    'icon' => 'receipt',
                    'actions' => ['view', 'create', 'edit', 'delete', 'envoyer-partenaire', 'exporter-pdf']
                ],
                'Satisfaction Partenaire' => [
                    'icon' => 'emoji-smile',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
            ],
            'Interventions' => [
                'Interventions' => [
                    'icon' => 'tools',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
            ],
            'Fournisseurs & Stocks' => [
                'Fournisseurs' => [
                    'icon' => 'truck',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
                'Stocks Materiaux' => [
                    'icon' => 'box-seam',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
                'Sous-traitances' => [
                    'icon' => 'briefcase',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
            ],
            'Rendez-vous' => [
                'Rendez-vous' => [
                    'icon' => 'calendar-event',
                    'actions' => ['view', 'create', 'edit', 'delete', 'exporter-pdf']
                ],
            ],
            'Documents' => [
                'Documents' => [
                    'icon' => 'folder2-open',
                    'actions' => ['view', 'edit', 'delete', 'download']
                ],
            ],
            'Communication' => [
                'Messagerie' => [
                    'icon' => 'chat-dots',
                    'actions' => ['activer']
                ],
                'IA Chat Box' => [
                    'icon' => 'chat-square-quote',
                    'actions' => ['activer']
                ],
            ],
        ];

        foreach ($hierarchie as $groupName => $modules) {
            foreach ($modules as $moduleName => $moduleData) {
                $moduleSlug = Str::slug($moduleName);
                $icon = $moduleData['icon'] ?? 'circle';
                $actions = $moduleData['actions'] ?? ['view', 'create', 'edit', 'delete'];

                foreach ($actions as $action) {
                    $slug = $action . '-' . $moduleSlug;
                    $label = $actionLabels[$action] ?? ucfirst($action);
                    $color = $actionColors[$action] ?? 'secondary';

                    DB::table('permissions')->insert([
                        'nom' => $label . ' ' . $moduleName,
                        'slug' => $slug,
                        'module' => $moduleSlug,
                        'action' => $action,
                        'group' => $groupName,
                        'icon' => $icon,
                        'color' => $color,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
