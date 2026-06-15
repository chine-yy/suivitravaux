<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Budget;
use App\Models\User;
use App\Models\Role;
use App\Models\Tache;
use App\Models\SousTache;
use App\Models\Phase;
use App\Models\Document;
use App\Models\Incident;
use App\Models\Rapport;
use App\Models\Contrat;
use App\Models\Facture;
use App\Models\Intervention;
use App\Models\SousTraitance;
use App\Models\Equipe;

use App\Models\Partenaire;
use App\Models\Fournisseur;
use App\Models\Stock;
use App\Models\Rendezvous;
use App\Models\Satisfaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuperAdminHistoriqueController extends Controller
{
    /**
     * Liste des années disponibles dans l'historique.
     */
    public function index()
    {
        // Collecter toutes les années à partir des différentes sources
        $anneesFromProjets = DB::table('projets')
            ->selectRaw('YEAR(created_at) as annee')
            ->union(DB::table('projets')->selectRaw('YEAR(date_debut) as annee')->whereNotNull('date_debut'))
            ->union(DB::table('projets')->selectRaw('YEAR(date_fin_reelle) as annee')->whereNotNull('date_fin_reelle'));

        $anneesFromBudgets = DB::table('budgets')->selectRaw('annee');

        $anneesFromTaches = DB::table('taches')->selectRaw('YEAR(created_at) as annee');
        $anneesFromFactures = DB::table('factures')->selectRaw('YEAR(created_at) as annee');
        $anneesFromContrats = DB::table('contrats')->selectRaw('YEAR(created_at) as annee');

        $anneesRaw = DB::table(DB::raw("({$anneesFromProjets->toSql()}
            UNION {$anneesFromBudgets->toSql()}
            UNION {$anneesFromTaches->toSql()}
            UNION {$anneesFromFactures->toSql()}
            UNION {$anneesFromContrats->toSql()}) as t"))
            ->mergeBindings($anneesFromProjets)
            ->mergeBindings($anneesFromBudgets)
            ->mergeBindings($anneesFromTaches)
            ->mergeBindings($anneesFromFactures)
            ->mergeBindings($anneesFromContrats)
            ->selectRaw('DISTINCT annee')
            ->whereRaw('annee IS NOT NULL')
            ->orderBy('annee', 'desc')
            ->pluck('annee');

        // Cas de figure : si vide, utiliser l'année courante
        if ($anneesRaw->isEmpty()) {
            $annees = collect([date('Y')]);
        } else {
            $annees = $anneesRaw;
        }

        // Stats par année
        $statsParAnnee = [];
        foreach ($annees as $annee) {
            $projetsAnnee = Projet::where(function($q) use ($annee) {
                $q->whereYear('created_at', $annee)
                  ->orWhereYear('date_debut', $annee)
                  ->orWhere(function($q2) use ($annee) {
                      $q2->whereYear('date_debut', '<=', $annee)
                         ->where(function($q3) use ($annee) {
                             $q3->whereYear('date_fin_reelle', '>=', $annee)
                                ->orWhereYear('date_fin_prevue', '>=', $annee)
                                ->orWhereNull('date_fin_reelle');
                         });
                  });
            })->get();

            $projetsIds = $projetsAnnee->pluck('id');

            $budgetAnnee = Budget::where('annee', $annee)->sum('budget_total');

            $usersAnnee = User::whereYear('created_at', '<=', $annee)->count();

            $tachesAnnee = Tache::whereIn('projet_id', $projetsIds)->count();

            $termines = $projetsAnnee->where('statut', 'termine')->count();

            $statsParAnnee[$annee] = [
                'annee'         => $annee,
                'total_projets' => $projetsAnnee->count(),
                'termines'      => $termines,
                'en_cours'      => $projetsAnnee->where('statut', 'en_cours')->count(),
                'budget_total'  => $budgetAnnee,
                'total_taches'  => $tachesAnnee,
                'total_users'   => $usersAnnee,
                'is_current'    => ($annee == date('Y')),
            ];
        }

        return view('super-admin.historique.index', compact('annees', 'statsParAnnee'));
    }

    /**
     * Détail complet pour une année donnée.
     */
    public function show($annee)
    {
        $annee = (int) $annee;

        // === PROJETS de l'année ===
        $projets = Projet::with([
                'phases.taches.sousTaches',
                'equipes.users',
                'partenaire',
                'incidents',
                'rapports',
                'depenses',
                'sousTraitances',
            ])
            ->where(function($q) use ($annee) {
                $q->whereYear('created_at', $annee)
                  ->orWhereYear('date_debut', $annee)
                  ->orWhere(function($q2) use ($annee) {
                      $q2->whereYear('date_debut', '<=', $annee)
                         ->where(function($q3) use ($annee) {
                             $q3->whereYear('date_fin_reelle', '>=', $annee)
                                ->orWhereYear('date_fin_prevue', '>=', $annee)
                                ->orWhereNull('date_fin_reelle');
                         });
                  });
            })
            ->get();

        $projetsIds = $projets->pluck('id');

        // === PHASES ===
        $phases = Phase::with(['taches.sousTaches', 'projet'])
            ->whereIn('projet_id', $projetsIds)
            ->get();

        // === TACHES ===
        $taches = Tache::with(['projet', 'sousTaches'])
            ->whereIn('projet_id', $projetsIds)
            ->get();

        // === SOUS-TACHES ===
        $sousTaches = SousTache::with(['tache.projet'])
            ->whereIn('tache_id', $taches->pluck('id'))
            ->get();

        // === DOCUMENTS ===
        $documents = Document::whereIn('projet_id', $projetsIds)->get();

        // === INCIDENTS ===
        $incidents = Incident::whereIn('projet_id', $projetsIds)->get();

        // === RAPPORTS ===
        $rapports = Rapport::with('auteur')
            ->whereIn('projet_id', $projetsIds)
            ->get();

        // === ÉQUIPES & MEMBRES ===
        $equipes = Equipe::with(['users', 'projet'])
            ->whereIn('projet_id', $projetsIds)
            ->get();

        $userIds = $equipes->flatMap(function($e) { return $e->users->pluck('id'); })->unique();
        $membres = User::with('role')
            ->whereIn('id', $userIds)
            ->get();

        // === UTILISATEURS & RÔLES (globaux, créés avant ou pendant l'année) ===
        $users = User::with('role')
            ->whereYear('created_at', '<=', $annee)
            ->get();

        $rolesRaw = Role::withCount('users')
            ->with('permissions')
            ->whereNotIn('nom', ['Administration', 'Super Admin', 'Partenaire'])
            ->get();

        // Regrouper les doublons historiques (même nom normalisé) en une seule ligne.
        $roles = $rolesRaw
            ->groupBy(fn ($role) => Str::lower(preg_replace('/\s+/u', ' ', trim((string) $role->nom)) ?? ''))
            ->map(function ($group) {
                $primary = $group->first();
                $primary->users_count = $group->sum('users_count');
                $primary->setRelation(
                    'permissions',
                    $group->flatMap(fn ($role) => $role->permissions)->unique('id')->values()
                );

                return $primary;
            })
            ->values();

        $admins = \App\Models\User::with('role')
            ->whereHas('role', function ($q) {
                $q->where('nom', \App\Models\User::ROLE_ADMIN_ENTREPRISE);
            })
            ->whereYear('created_at', '<=', $annee)
            ->get();

        // === BUDGETS ===
        $budgets = Budget::with(['budgetProjets.projet', 'budgetProjets.depenses'])
            ->where('annee', $annee)
            ->get();

        $budgetTotal = $budgets->sum('budget_total');
        $budgetConsomme = DB::table('depenses')
            ->join('budget_projets', 'depenses.budget_projet_id', '=', 'budget_projets.id')
            ->join('budgets', 'budget_projets.budget_id', '=', 'budgets.id')
            ->where('budgets.annee', $annee)
            ->where('depenses.statut', 'validee')
            ->sum('depenses.montant');

        // === CONTRATS ===
        $contrats = Contrat::whereIn('projet_id', $projetsIds)
            ->orWhereYear('created_at', $annee)
            ->get();

        // === FACTURES ===
        $factures = Facture::whereYear('created_at', $annee)->get();
        $totalFactures = $factures->sum('montant_ttc');

        // === INTERVENTIONS ===
        $interventions = Intervention::whereYear('created_at', $annee)->get();

        // === SOUS-TRAITANCES ===
        $sousTraitances = SousTraitance::whereIn('projet_id', $projetsIds)->get();
        $totalSousTraitance = $sousTraitances->sum('montant_contrat');

        // === CLIENTS ===
        $partenaires = Partenaire::whereIn('projet_id', $projetsIds)->get();

        // === FOURNISSEURS ===
        $fournisseurs = Fournisseur::whereYear('created_at', '<=', $annee)->get();

        // === STOCKS & MATÉRIAUX ===
        $stocks = Stock::with('fournisseur')->whereYear('created_at', $annee)->get();

        // === RENDEZ-VOUS ===
        $rendezvous = Rendezvous::with(['projet', 'user'])
            ->whereYear('date_heure', $annee)
            ->orWhereIn('projet_id', $projetsIds)
            ->get();

        // === SATISFACTION CLIENT ===
        $satisfactions = Satisfaction::with(['partenaire', 'projet'])
            ->whereYear('created_at', $annee)
            ->orWhereIn('projet_id', $projetsIds)
            ->get();

        // Stats résumées
        $stats = [
            'total_projets'     => $projets->count(),
            'termines'          => $projets->where('statut', 'termine')->count(),
            'en_cours'          => $projets->where('statut', 'en_cours')->count(),
            'en_retard'         => $projets->where('statut', 'en_retard')->count(),
            'total_phases'      => $phases->count(),
            'total_taches'      => $taches->count(),
            'total_sous_taches' => $sousTaches->count(),
            'taches_terminees'  => $taches->where('statut', 'terminee')->count(),
            'total_documents'   => $documents->count(),
            'total_incidents'   => $incidents->count(),
            'total_rapports'    => $rapports->count(),
            'total_roles'       => $roles->count(),
            'total_users'       => $users->count(),
            'total_equipes'     => $equipes->count(),
            'total_membres'     => $membres->count(),
            'total_partenaires'     => $partenaires->count(),
            'budget_total'      => $budgetTotal,
            'budget_consomme'   => $budgetConsomme,
            'budget_restant'    => $budgetTotal - $budgetConsomme,
            'total_contrats'    => $contrats->count(),
            'total_factures'    => $totalFactures,
            'total_interventions'=> $interventions->count(),
            'total_sous_traitances' => $sousTraitances->count(),
            'total_st'          => $totalSousTraitance,
            'total_fournisseurs'=> $fournisseurs->count(),
            'total_stocks'      => $stocks->count(),
            'total_rendezvous'  => $rendezvous->count(),
            'total_satisfactions'=> $satisfactions->count(),
        ];

        return view('super-admin.historique.show', compact(
            'annee', 'projets', 'phases', 'taches', 'sousTaches',
            'documents', 'incidents', 'rapports',
            'equipes', 'membres', 'users', 'roles', 'admins',
            'partenaires', 'fournisseurs', 'stocks', 'rendezvous', 'satisfactions',
            'budgets', 'budgetTotal', 'budgetConsomme',
            'contrats', 'factures', 'interventions', 'sousTraitances',
            'stats'
        ));
    }

    /**
     * Exporter l'historique en PDF (téléchargement)
     */
    public function exportPdf($annee)
    {
        $annee = (int) $annee;
        $data = $this->getHistoriqueData($annee);

        $pdf = \PDF::loadView('partials.pdf-historique', $data);
        $filename = 'historique_' . $annee . '_' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Voir l'historique en PDF dans le navigateur
     */
    public function voirPdf($annee)
    {
        $annee = (int) $annee;
        $data = $this->getHistoriqueData($annee);

        $pdf = \PDF::loadView('partials.pdf-historique', $data);
        return $pdf->stream();
    }

    /**
     * Récupérer les données de l'historique pour une année donnée
     */
    private function getHistoriqueData($annee)
    {
        $projets = Projet::with([
                'phases.taches.sousTaches', 'equipes.users', 'partenaire',
                'incidents', 'rapports', 'depenses', 'sousTraitances',
            ])
            ->where(function($q) use ($annee) {
                $q->whereYear('created_at', $annee)
                  ->orWhereYear('date_debut', $annee)
                  ->orWhere(function($q2) use ($annee) {
                      $q2->whereYear('date_debut', '<=', $annee)
                         ->where(function($q3) use ($annee) {
                             $q3->whereYear('date_fin_reelle', '>=', $annee)
                                ->orWhereYear('date_fin_prevue', '>=', $annee)
                                ->orWhereNull('date_fin_reelle');
                         });
                  });
            })->get();

        $projetsIds = $projets->pluck('id');

        $phases = Phase::with(['taches.sousTaches', 'projet'])->whereIn('projet_id', $projetsIds)->get();
        $taches = Tache::with(['projet', 'sousTaches'])->whereIn('projet_id', $projetsIds)->get();
        $sousTaches = SousTache::with(['tache.projet'])->whereIn('tache_id', $taches->pluck('id'))->get();
        $documents = Document::whereIn('projet_id', $projetsIds)->get();
        $incidents = Incident::whereIn('projet_id', $projetsIds)->get();
        $rapports = Rapport::with('auteur')->whereIn('projet_id', $projetsIds)->get();
        $equipes = Equipe::with(['users', 'projet'])->whereIn('projet_id', $projetsIds)->get();
        $userIds = $equipes->flatMap(fn($e) => $e->users->pluck('id'))->unique();
        $membres = User::with('role')->whereIn('id', $userIds)->get();
        $users = User::with('role')->whereYear('created_at', '<=', $annee)->get();
        $rolesRaw = Role::withCount('users')->with('permissions')->where('nom', '!=', 'Administration')->get();
        $roles = $rolesRaw->groupBy(fn ($role) => \Illuminate\Support\Str::lower(preg_replace('/\s+/u', ' ', trim((string) $role->nom)) ?? ''))
            ->map(function ($group) {
                $primary = $group->first();
                $primary->users_count = $group->sum('users_count');
                $primary->setRelation('permissions', $group->flatMap(fn ($role) => $role->permissions)->unique('id')->values());
                return $primary;
            })->values();
        $admins = \App\Models\User::with('role')->whereHas('role', function ($q) {
            $q->where('nom', \App\Models\User::ROLE_ADMIN_ENTREPRISE);
        })->whereYear('created_at', '<=', $annee)->get();
        $budgets = Budget::with(['budgetProjets.projet', 'budgetProjets.depenses'])->where('annee', $annee)->get();
        $budgetTotal = $budgets->sum('budget_total');
        $budgetConsomme = DB::table('depenses')
            ->join('budget_projets', 'depenses.budget_projet_id', '=', 'budget_projets.id')
            ->join('budgets', 'budget_projets.budget_id', '=', 'budgets.id')
            ->where('budgets.annee', $annee)->where('depenses.statut', 'validee')->sum('depenses.montant');
        $contrats = Contrat::whereIn('projet_id', $projetsIds)->orWhereYear('created_at', $annee)->get();
        $factures = Facture::whereYear('created_at', $annee)->get();
        $totalFactures = $factures->sum('montant_ttc');
        $interventions = Intervention::whereYear('created_at', $annee)->get();
        $sousTraitances = SousTraitance::whereIn('projet_id', $projetsIds)->get();
        $partenaires = Partenaire::whereIn('projet_id', $projetsIds)->get();
        $fournisseurs = Fournisseur::whereYear('created_at', '<=', $annee)->get();
        $stocks = Stock::with('fournisseur')->whereYear('created_at', $annee)->get();
        $rendezvous = Rendezvous::with(['projet', 'user'])->whereYear('date_heure', $annee)->orWhereIn('projet_id', $projetsIds)->get();
        $satisfactions = Satisfaction::with(['partenaire', 'projet'])->whereYear('created_at', $annee)->orWhereIn('projet_id', $projetsIds)->get();

        $stats = [
            'total_projets' => $projets->count(),
            'termines' => $projets->where('statut', 'termine')->count(),
            'en_cours' => $projets->where('statut', 'en_cours')->count(),
            'en_retard' => $projets->where('statut', 'en_retard')->count(),
            'total_phases' => $phases->count(),
            'total_taches' => $taches->count(),
            'total_sous_taches' => $sousTaches->count(),
            'taches_terminees' => $taches->where('statut', 'terminee')->count(),
            'total_documents' => $documents->count(),
            'total_incidents' => $incidents->count(),
            'total_rapports' => $rapports->count(),
            'total_roles' => $roles->count(),
            'total_users' => $users->count(),
            'total_equipes' => $equipes->count(),
            'total_membres' => $membres->count(),
            'total_partenaires' => $partenaires->count(),
            'budget_total' => $budgetTotal,
            'budget_consomme' => $budgetConsomme,
            'budget_restant' => $budgetTotal - $budgetConsomme,
            'total_contrats' => $contrats->count(),
            'total_factures' => $totalFactures,
            'total_interventions' => $interventions->count(),
            'total_sous_traitances' => $sousTraitances->count(),
            'total_st' => $sousTraitances->sum('montant_contrat'),
            'total_fournisseurs' => $fournisseurs->count(),
            'total_stocks' => $stocks->count(),
            'total_rendezvous' => $rendezvous->count(),
            'total_satisfactions' => $satisfactions->count(),
        ];

        return compact('annee', 'projets', 'phases', 'taches', 'sousTaches', 'documents', 'incidents', 'rapports',
            'equipes', 'membres', 'users', 'roles', 'admins', 'partenaires', 'fournisseurs', 'stocks', 'rendezvous', 'satisfactions',
            'budgets', 'budgetTotal', 'budgetConsomme', 'contrats', 'factures', 'interventions', 'sousTraitances', 'stats');
    }
}
