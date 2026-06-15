<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Tache;
use App\Models\Incident;
use App\Models\Rapport;
use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\Depense;
use App\Models\Satisfaction;
use App\Models\Stock;
use App\Models\Fournisseur;
use App\Models\SousTraitance;
use App\Models\Rendezvous;
use App\Models\Document;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class RoleDashboardController extends Controller
{
    private function canViewAll(): bool
    {
        $type = \App\Helpers\SessionHelper::getCurrentType();
        return in_array($type, ['SuperAdmin', 'Admin']);
    }

    private function entrepriseId(): ?int
    {
        $activeData = \App\Helpers\SessionHelper::getActiveSessionData();
        if (!empty($activeData['entreprise_id'])) {
            return (int) $activeData['entreprise_id'];
        }

        $actor = auth()->user();
        return $actor?->entreprise_id ?? $actor?->id_entreprise ?? null;
    }

    private function applyFilters($query, ?int $entrepriseId, $user)
    {
        if ($this->canViewAll()) {
            return;
        }

        // If the query is for the Projet model, apply filters directly on Projet.
        if ($query->getModel() instanceof \App\Models\Projet) {
            $this->applyProjectVisibility($query, $entrepriseId, $user);
            return;
        }

        // For other models that reference a projet relation.
        $query->whereHas('projet', function (Builder $projectQuery) use ($entrepriseId, $user) {
            $this->applyProjectVisibility($projectQuery, $entrepriseId, $user);
        });
    }

    private function applyProjectVisibility(Builder $query, ?int $entrepriseId, $user): void
    {
        if ($this->canViewAll()) {
            return;
        }

        if ($entrepriseId && Schema::hasColumn('projets', 'entreprise_id')) {
            $query->where('entreprise_id', $entrepriseId);
            return;
        }

        // Legacy fallback for old schemas.
        if ($entrepriseId && Schema::hasColumn('projets', 'user_id')) {
            $query->where('user_id', $entrepriseId);
        }
    }

    public function index()
    {
        $user = auth()->user();
        $entrepriseId = $this->entrepriseId();
        $canViewAll = $this->canViewAll();
        $role = $user->role;
        $has = fn(string $permission) => method_exists($user, 'hasPermission') && $user->hasPermission($permission);

        $data = [
            'hasChat' => $has('chat-messagerie-activer'),
            'hasTache' => $has('view-taches'),
            'hasSousTache' => $has('view-sous-taches'),
            'hasRapport' => $has('view-rapports'),
            'hasBudget' => $has('gerer-budgets'),
            'hasIncident' => $has('view-incidents'),
            'hasPhase' => $has('view-phases'),
            'hasMembre' => $has('view-membres'),
            'hasEquipes' => $has('view-equipes'),
            'hasProjet' => $has('view-projets'),
        ];

        $stats = [];
        if ($data['hasTache']) {
            $tacheQuery = Tache::query();
            $this->applyFilters($tacheQuery, $entrepriseId, $user);

            $stats['totalTaches'] = (clone $tacheQuery)->count();
            $stats['tachesTerminees'] = (clone $tacheQuery)->whereIn('statut', ['terminee', 'termine'])->count();
            $stats['tachesEnCours'] = (clone $tacheQuery)->where('statut', 'en_cours')->count();
            $stats['tachesEnRetard'] = (clone $tacheQuery)
                ->whereNotIn('statut', ['terminee', 'termine'])
                ->where('date_fin_prevue', '<', now())
                ->count();
        }
        if ($data['hasTache'] || $data['hasProjet']) {
            $projectQuery = Projet::query();
            $this->applyFilters($projectQuery, $entrepriseId, $user);

            $stats['totalProjets'] = (clone $projectQuery)->count();
            $stats['projetsEnCours'] = (clone $projectQuery)->where('statut', 'en_cours')->count();
            $stats['projetsTermines'] = (clone $projectQuery)->where('statut', 'termine')->count();
            $stats['projetsEnRetard'] = (clone $projectQuery)->where('statut', 'en_retard')->count();
            $stats['projetsEnAttente'] = (clone $projectQuery)->where('statut', 'en_attente')->count();
        }
        if ($data['hasIncident']) {
            $incidentQuery = Incident::query()->where('statut', 'ouvert');
            $this->applyFilters($incidentQuery, $entrepriseId, $user);
            $stats['incidentsOuverts'] = (clone $incidentQuery)->count();
        }
        if ($has('view-satisfaction-partenaire')) {
            $satisfactionQuery = Satisfaction::query();
            $this->applyFilters($satisfactionQuery, $entrepriseId, $user);
            $stats['totalSatisfactions'] = (clone $satisfactionQuery)->count();
            $stats['satisfactionsRepondues'] = (clone $satisfactionQuery)->where('statut', 'repondu')->count();
        }
        if ($has('view-stocks-materiaux')) {
            $stats['totalStocks'] = Stock::count();
        }
        if ($has('view-fournisseurs')) {
            $stats['totalFournisseurs'] = Fournisseur::count();
        }
        if ($has('view-documents')) {
            $stats['totalDocuments'] = Document::count();
        }
        if ($has('view-sous-traitances')) {
            $stats['totalSousTraitances'] = SousTraitance::count();
        }
        if ($has('view-rendez-vous')) {
            $stats['totalRendezvous'] = Rendezvous::count();
        }

        // Liste des tâches en retard
        $tachesEnRetardList = collect([]);
        if ($data['hasTache']) {
            $tacheQuery = Tache::query()
                ->whereNotIn('statut', ['terminee', 'termine'])
                ->where('date_fin_prevue', '<', now());
            $this->applyFilters($tacheQuery, $entrepriseId, $user);

            $tachesEnRetardList = $tacheQuery->with('projet')->orderBy('date_fin_prevue', 'asc')->get();
        }

        // Liste des rapports en attente
        $rapportsEnAttente = collect([]);
        if ($data['hasRapport']) {
            // Regular users should only see reports they authored.
            if (!($user->isSuperAdmin() || $user->isAdminEntreprise())) {
                $rapportsEnAttente = Rapport::where('auteur_id', $user->id)
                    ->where('statut', 'soumis')
                    ->with(['projet', 'auteur'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $rapportQuery = Rapport::where('statut', 'soumis');

                if ($canViewAll) {
                    $rapportsEnAttente = $rapportQuery->with(['projet', 'auteur'])->orderBy('created_at', 'desc')->get();
                } else {
                    $this->applyFilters($rapportQuery, $entrepriseId, $user);
                    $rapportsEnAttente = $rapportQuery->with(['projet', 'auteur'])->orderBy('created_at', 'desc')->get();
                }
            }
        }

        $avancementProjets = [];
        if ($data['hasTache'] || $data['hasProjet']) {
            $avancementQuery = Projet::select('nom', 'avancement');
            $this->applyFilters($avancementQuery, $entrepriseId, $user);

            $avancementProjets = $avancementQuery->take(5)->get()->toArray();
        }

        $grantedPermissions = $user->role ? $user->role->permissions()->get() : collect();

        // Variables for stats cards
        $totalProjets = $stats['totalProjets'] ?? 0;
        $projetsEnCours = $stats['projetsEnCours'] ?? 0;
        $totalTaches = $stats['totalTaches'] ?? 0;
        $tachesTerminees = $stats['tachesTerminees'] ?? 0;
        $tachesEnCours = $stats['tachesEnCours'] ?? 0;
        $tachesEnRetard = $stats['tachesEnRetard'] ?? 0;
        $incidentsOuverts = $stats['incidentsOuverts'] ?? 0;
        $totalSatisfactions = $stats['totalSatisfactions'] ?? 0;
        $satisfactionsRepondues = $stats['satisfactionsRepondues'] ?? 0;
        $totalStocks = $stats['totalStocks'] ?? 0;
        $totalFournisseurs = $stats['totalFournisseurs'] ?? 0;
        $totalDocuments = $stats['totalDocuments'] ?? 0;
        $totalSousTraitances = $stats['totalSousTraitances'] ?? 0;
        $totalRendezvous = $stats['totalRendezvous'] ?? 0;
        $rapportsAValider = 0;
        if ($data['hasRapport']) {
            // Regular users: count only their own submitted reports.
            if (!($user->isSuperAdmin() || $user->isAdminEntreprise())) {
                $rapportsAValider = Rapport::where('auteur_id', $user->id)->where('statut', 'soumis')->count();
            } else {
                $rapportQuery = Rapport::where('statut', 'soumis');
                if (!$canViewAll) {
                    $this->applyFilters($rapportQuery, $entrepriseId, $user);
                }
                $rapportsAValider = $rapportQuery->count();
            }
        }

        // Budget variables
        $budgetGlobal = 0;
        $totalAlloue = 0;
        $totalConsomme = 0;
        if ($data['hasBudget']) {
            $budgetQuery = Budget::query();
            $budgetGlobal = $budgetQuery->sum('budget_total');

            $budgetProjQuery = BudgetProjet::query();
            if (!$canViewAll) {
                $this->applyFilters($budgetProjQuery, $entrepriseId, $user);
            }
            $totalAlloue = $budgetProjQuery->sum('montant_alloue');

            $depenseQuery = Depense::query();
            if (!$canViewAll) {
                $this->applyFilters($depenseQuery, $entrepriseId, $user);
            }
            $totalConsomme = $depenseQuery->sum('montant');
        }

        // Other variables with defaults
        $alertes = collect([]);
        $projetsTermines = $stats['projetsTermines'] ?? 0;
        $projetsEnRetard = $stats['projetsEnRetard'] ?? 0;
        $projetsEnAttente = $stats['projetsEnAttente'] ?? 0;
        $projetsScopes = Projet::query();
        $this->applyFilters($projetsScopes, $entrepriseId, $user);

        $repartitionStatuts = [
            'en_cours' => (clone $projetsScopes)->where('statut', 'en_cours')->count(),
            'termine' => (clone $projetsScopes)->where('statut', 'termine')->count(),
            'en_retard' => (clone $projetsScopes)->where('statut', 'en_retard')->count(),
            'en_attente' => (clone $projetsScopes)->where('statut', 'en_attente')->count(),
            'en_pause' => (clone $projetsScopes)->where('statut', 'en_pause')->count(),
        ];
        $evenementsCalendrier = collect([]);
        $tachesParProjet = (clone $projetsScopes)
            ->select('id', 'nom')
            ->withCount([
                'taches as terminees' => function ($query) {
                    $query->whereIn('statut', ['terminee', 'termine']);
                },
                'taches as en_retard' => function ($query) {
                    $query->whereNotIn('statut', ['terminee', 'termine'])
                        ->whereDate('date_fin_prevue', '<', now()->toDateString());
                },
            ])
            ->orderBy('nom')
            ->limit(10)
            ->get();

        return view('role-dynamique.dashboard', compact(
            'data',
            'stats',
            'avancementProjets',
            'role',
            'grantedPermissions',
            'totalProjets',
            'projetsEnCours',
            'totalTaches',
            'tachesTerminees',
            'tachesEnCours',
            'tachesEnRetard',
            'incidentsOuverts',
            'totalSatisfactions',
            'satisfactionsRepondues',
            'totalStocks',
            'totalFournisseurs',
            'totalDocuments',
            'totalSousTraitances',
            'totalRendezvous',
            'rapportsAValider',
            'budgetGlobal',
            'totalAlloue',
            'totalConsomme',
            'alertes',
            'projetsTermines',
            'projetsEnRetard',
            'projetsEnAttente',
            'repartitionStatuts',
            'tachesEnRetardList',
            'rapportsEnAttente',
            'evenementsCalendrier',
            'tachesParProjet'
        ))->with('canPermission', $has);
    }

    public function showPermission($permission)
    {
        $user = auth()->user();
        if (!$user->hasPermission($permission)) {
            abort(403);
        }
        return redirect()->route('role-dynamique.dashboard')->with('success', 'Accès autorisé à ' . $permission . '.');
    }
}
