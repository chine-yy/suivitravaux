<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Rapport;
use App\Models\Budget;
use App\Models\BudgetProjet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        // Exclure les projets créés par les super admins
        $totalProjets = Projet::count();

        $projetsEnCours = Projet::where('statut', 'en_cours')->count();

        $projetsTermines = Projet::where('statut', 'termine')->count();

        $projetsEnRetard = Projet::where('statut', 'en_retard')->count();

        $totalRapports = Rapport::count();
        $rapportsAValider = Rapport::where('statut', 'en_attente')->count();

        $currentYear = date('Y');
        $budgetAnnuel = Budget::where('annee', $currentYear)->first();
        $budgetGlobal = $budgetAnnuel ? $budgetAnnuel->budget_total : 0;
        $totalConsomme = $budgetAnnuel ? $budgetAnnuel->getTotalDepenses() : DB::table('projets')->sum('budget_consomme');

        // Stats Rôles & Utilisateurs
        // Exclure les rôles 'Administration' et 'Super Admin'
        $roles = \App\Models\Role::withCount('users')->with('permissions')
            ->where('nom', '!=', 'Administration')
            ->where('nom', '!=', 'Super Admin')
            ->where('nom', '!=', 'Partenaire')
            ->latest()->get();
        $totalRoles = $roles->count();
        // Exclure les super admins et partenaires des statistiques d'utilisateurs
        $totalUsers = \App\Models\User::whereDoesntHave('role', function($q) {
            $q->whereIn('nom', ['Super Admin', 'Partenaire']);
        })->count();
        $usersActifs = \App\Models\User::where('is_active', true)
            ->whereDoesntHave('role', function($q) {
                $q->whereIn('nom', ['Super Admin', 'Partenaire']);
            })->count();

        // Toutes permissions pour les modals de création
        $allPermissions = \App\Models\Permission::withCount('roles')->get();

        // Tous les utilisateurs pour l'attribution rapide (exclure partenaires)
        $allUsers = \App\Models\User::with('role')->whereDoesntHave('role', function($q) {
            $q->whereIn('nom', ['Super Admin', 'Partenaire']);
        })->orderBy('name')->get();

        // Base query: exclure les projets créés par les super admins
        $projetBaseQuery = Projet::query();

        // Data for charts
        $avancementProjets = $projetBaseQuery->select('nom', 'avancement')->take(5)->get()->toArray();
        foreach($avancementProjets as &$p) {
            $p['en_retard'] = false;
        }

        $repartitionStatuts = [
            'en_cours'  => $projetsEnCours,
            'termines'  => $projetsTermines,
            'en_retard' => $projetsEnRetard,
            'en_attente'=> $projetBaseQuery->clone()->where('statut', 'en_attente')->count(),
        ];

        // Utilisateurs par rôle pour graphique
        $usersParRole = $roles->map(function($r) {
            return ['nom' => $r->nom, 'count' => $r->users_count];
        });

        // Modules du dashboard (liste des fonctionnalités)
        $modules = [
            ['route' => 'taches',        'icon' => 'bi-list-task',            'title' => 'Gestion des Tâches',    'desc' => 'Créez, suivez et complétez vos tâches'],
            ['route' => 'sous-taches',   'icon' => 'bi-list-check',           'title' => 'Sous-Tâches',           'desc' => 'Décomposez les tâches en sous-tâches'],
            ['route' => 'rapports',      'icon' => 'bi-file-earmark-text',    'title' => 'Rapports',              'desc' => 'Générez et envoyez des rapports'],
            ['route' => 'incidents',     'icon' => 'bi-exclamation-triangle', 'title' => 'Incidents',             'desc' => 'Signalez et suivez les incidents'],
            ['route' => 'phases',        'icon' => 'bi-collection',           'title' => 'Phases de Projet',      'desc' => 'Organisez les phases du projet'],
            ['route' => 'chat',          'icon' => 'bi-chat-dots',            'title' => 'Messagerie',            'desc' => 'Communiquez avec votre équipe'],
            ['route' => 'equipes',       'icon' => 'bi-people',               'title' => 'Gestion Équipes',       'desc' => 'Gérez les membres et les équipes'],
            ['route' => 'partenaires',       'icon' => 'bi-person-workspace',     'title' => 'Gestion Partenaires',       'desc' => 'Gérez les partenaires et leurs projets'],
            ['route' => 'budget',        'icon' => 'bi-cash-stack',           'title' => 'Gestion Budgets',       'desc' => 'Suivez les finances et les dépenses'],
            ['route' => 'roles',         'icon' => 'bi-shield-lock',          'title' => 'Rôles & Permissions',   'desc' => 'Gérez toutes les autorisations'],
            ['route' => 'users',         'icon' => 'bi-people-fill',          'title' => 'Gestion Utilisateurs',  'desc' => 'Créez et gérez tous les comptes'],
            ['route' => 'contrats',      'icon' => 'bi-file-earmark-ruled',   'title' => 'Contrats',              'desc' => 'Gérez les contrats et avenants'],
            ['route' => 'factures',      'icon' => 'bi-receipt-cutoff',       'title' => 'Factures',              'desc' => 'Suivi de la facturation'],
            ['route' => 'interventions', 'icon' => 'bi-tools',                'title' => 'Interventions',         'desc' => 'Planifiez les interventions terrain'],
            ['route' => 'fournisseurs',  'icon' => 'bi-truck',                'title' => 'Fournisseurs',          'desc' => 'Gérez vos fournisseurs et contacts'],
            ['route' => 'stocks',        'icon' => 'bi-boxes',                'title' => 'Stocks & Matériaux',    'desc' => 'Inventaire et gestion des stocks'],
            ['route' => 'rendezvous',    'icon' => 'bi-calendar-check',       'title' => 'Rendez-vous',           'desc' => 'Agenda et planification'],
            ['route' => 'documents',     'icon' => 'bi-folder2-open',         'title' => 'Documents',             'desc' => 'Archivage et partage de fichiers'],
            ['route' => 'satisfaction',  'icon' => 'bi-emoji-smile',          'title' => 'Satisfaction Partenaire',   'desc' => 'Enquêtes et retours partenaires'],
            ['route' => 'analytics',     'icon' => 'bi-graph-up-arrow',       'title' => 'Analytics',             'desc' => 'Statistiques et indicateurs clés'],
            ['route' => 'historique',    'icon' => 'bi-clock-history',        'title' => 'Historique',             'desc' => 'Consultez toutes les données par année'],
        ];

        return view('super-admin.dashboard', compact(
            'totalProjets', 'projetsEnCours', 'projetsTermines', 'projetsEnRetard',
            'totalRapports', 'rapportsAValider', 'budgetGlobal', 'totalConsomme',
            'avancementProjets', 'repartitionStatuts',
            'totalRoles', 'totalUsers', 'usersActifs', 'usersParRole',
            'roles', 'allPermissions', 'allUsers', 'modules', 'budgetAnnuel', 'currentYear'
        ));
    }

    public function budget()
    {
        // Exclure les projets créés par les super admins
        $projets = Projet::get();

        $budgetParAnnee = Budget::select(DB::raw('annee'), DB::raw('SUM(budget_total) as total'))
            ->groupBy('annee')
            ->get();

        // Calculer le budget total et consommé basé sur les dépenses validées
        $budgetTotalGlobal = Budget::sum('budget_total');
        $budgetConsommeGlobal = Budget::get()->sum(function($b) {
            return $b->getTotalDepenses();
        });
        $budgetRestantGlobal = $budgetTotalGlobal - $budgetConsommeGlobal;

        // Alertes financières
        $alertes = [];

        // Projets dépassant le budget (based on dynamic consumption)
        $currentYearBudget = Budget::where('annee', date('Y'))->first();
        foreach ($projets as $projet) {
            $dynamicConsomme = $projet->getDynamicConsomme($currentYearBudget ? $currentYearBudget->id : null);
            $bp = $currentYearBudget ? BudgetProjet::where('budget_id', $currentYearBudget->id)->where('projet_id', $projet->id)->first() : null;
            $dynamicBudget = $bp ? $bp->montant_alloue : 0;

            if ($dynamicBudget > 0 && $dynamicConsomme > $dynamicBudget) {
                $alertes[] = [
                    'type' => 'danger',
                    'titre' => 'Budget dépassé',
                    'message' => "Le projet '{$projet->nom}' a dépassé son budget de " .
                        number_format($dynamicConsomme - $dynamicBudget, 0, ',', ' ') . " FCF",
                    'icon' => 'bi-exclamation-octagon'
                ];
            } elseif ($dynamicBudget > 0) {
                $pourcentage = round(($dynamicConsomme / $dynamicBudget) * 100);
                if ($pourcentage > 80) {
                    $alertes[] = [
                        'type' => 'warning',
                        'titre' => 'Budget à risque',
                        'message' => "Le projet '{$projet->nom}' a utilisé {$pourcentage}% de son budget",
                        'icon' => 'bi-exclamation-triangle'
                    ];
                }
            }
        }

        // Dépenses récentes
        $depensesRecentes = \App\Models\Depense::with('projet')
            ->latest()
            ->take(10)
            ->get();

        return view('super-admin.budget.index', compact(
            'projets',
            'budgetParAnnee', 'budgetTotalGlobal', 'budgetConsommeGlobal',
            'budgetRestantGlobal', 'alertes', 'depensesRecentes'
        ));
    }

    public function rapports(Request $request)
    {
        // Exclure les rapports associés aux projets créés par les super admins
        $query = Rapport::with(['projet', 'auteur']);

        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->projet_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('titre')) {
            $query->where('titre', 'like', '%' . $request->titre . '%');
        }

        // Paginated list to support view pagination helpers
        $rapports = $query->latest()->paginate(10);

        $projets = Projet::orderBy('nom')->get();


        // Statistiques pour les graphiques
        $rapportsParMois = Rapport::select(
            DB::raw('MONTH(created_at) as mois'),
            DB::raw('YEAR(created_at) as annee'),
            DB::raw('COUNT(*) as total')
        )
        ->groupBy('annee', 'mois')
        ->orderBy('annee')
        ->orderBy('mois')
        ->get();

        $rapportsParStatut = [
            'en_attente' => Rapport::where('statut', 'en_attente')->count(),
            'valide' => Rapport::where('statut', 'valide')->count(),
            'rejete' => Rapport::where('statut', 'rejete')->count(),
        ];

        $rapportsParProjet = Projet::withCount('rapports')->get()->map(function($p) {
            return ['nom' => $p->nom, 'count' => $p->rapports_count];
        });

        return view('super-admin.rapports.index', compact(
            'rapports',
            'projets',
            'rapportsParMois',
            'rapportsParStatut',
            'rapportsParProjet'
        ));
    }

    /**
     * Afficher les détails d'un rapport (redirection vers index avec modal)
     */
    public function showRapport($id)
    {
        $rapport = Rapport::with(['projet', 'auteur.role', 'auteur.entreprise', 'sousTache'])->findOrFail($id);

        return redirect()->route('super-admin.rapports.index')
            ->with('open_modal', 'viewRapportModal' . $id);
    }

    /**
     * Mettre à jour uniquement le statut d'un rapport
     */
    public function updateStatutRapport(Request $request, $id)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,valide,rejete'
        ]);

        $rapport = Rapport::findOrFail($id);
        $rapport->statut = $request->statut;
        $rapport->save();

        return redirect()->back()->with('success', 'Le statut du rapport a été modifié avec succès.');
    }

    public function envoyerRapportPartenaire(Request $request, $id)
    {
        $rapport = Rapport::with('projet')->findOrFail($id);

        if (!$rapport->projet) {
            return redirect()->back()->with('error', 'Aucun projet trouvé pour ce rapport.');
        }

        $projet = $rapport->projet;
        $partenaireIds = [];

        if ($projet->partenaire_id) {
            $partenaireIds[] = $projet->partenaire_id;
        }

        $additionalPartenaires = $projet->partenaires()->pluck('users.id')->toArray();
        $partenaireIds = array_unique(array_merge($partenaireIds, $additionalPartenaires));

        if (empty($partenaireIds)) {
            return redirect()->back()->with('error', 'Aucun partenaire trouvé pour ce projet.');
        }

        $rapport->update([
            'est_envoye' => true,
            'date_envoi' => now(),
            'envoye_par_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Le rapport a été envoyé à ' . count($partenaireIds) . ' partenaire(s) avec succès.');
    }

    public function configuration()
    {
        $canAccessDatabaseManagement = true;

        // Récupérer les paramètres système
        $configurations = [
            'app_name' => config('app.name'),
            'app_env' => config('app.env', 'production'),
            'app_debug' => config('app.debug', false),
            'db_driver' => config('database.default', 'mysql'),
            'db_host' => config('database.connections.mysql.host', 'localhost'),
            'mail_driver' => config('mail.driver', 'smtp'),
            'mail_host' => config('mail.host', ''),
            'session_driver' => config('session.driver', 'file'),
            'cache_driver' => config('cache.default', 'file'),
            'filesystems_default' => config('filesystems.default', 'local'),
            'timezone' => config('app.timezone', 'UTC'),
            'locale' => config('app.locale', 'fr'),
            'log_channel' => config('logging.default', 'stack'),
            'queue_default' => config('queue.default', 'sync'),
        ];

        return view('super-admin.configuration.index', compact('configurations', 'canAccessDatabaseManagement'));
    }

    public function toggleMaintenance(Request $request)
    {
        if (app()->isDownForMaintenance()) {
            \Illuminate\Support\Facades\Artisan::call('up');
            $message = 'Le mode maintenance a été désactivé.';
        } else {
            \Illuminate\Support\Facades\Artisan::call('down', [
                '--secret' => 'kafyka-secret-123' // Optional: allow bypass with this secret
            ]);
            $message = 'Le mode maintenance a été activé.';
        }

        return redirect()->route('super-admin.configuration.index')
            ->with('success', $message);
    }

    public function viewLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = "";

        if (file_exists($logFile)) {
            // Read last 500 lines for performance
            $file = new \SplFileObject($logFile, 'r');
            $file->seek(PHP_INT_MAX);
            $lastLine = $file->key();

            $start = max(0, $lastLine - 500);
            $file->seek($start);

            while (!$file->eof()) {
                $logs .= $file->current();
                $file->next();
            }
        } else {
            $logs = "No log file found at " . $logFile;
        }

        return view('super-admin.configuration.logs', compact('logs'));
    }

    public function clearLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            return back()->with('success', 'Le fichier de logs a été vidé avec succès.');
        }
        return back()->with('error', 'Le fichier de logs est introuvable.');
    }

    public function exportLogsPdf()
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = "";

        if (file_exists($logFile)) {
            $file = new \SplFileObject($logFile, 'r');
            $file->seek(PHP_INT_MAX);
            $lastLine = $file->key();

            $start = max(0, $lastLine - 1000);
            $file->seek($start);

            while (!$file->eof()) {
                $logs .= $file->current();
                $file->next();
            }
        } else {
            $logs = "Aucun log disponible.";
        }

        $pdf = PDF::loadView('partials.pdf-logs', compact('logs'));
        return $pdf->download('laravel-log-superadmin-' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }
}
