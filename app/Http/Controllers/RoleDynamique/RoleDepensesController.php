<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\Depense;
use App\Models\Facture;
use App\Models\Projet;
use App\Models\SousTraitance;
use Illuminate\Support\Facades\Auth;

class RoleDepensesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function userPermissions(): array
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            return [];
        }

        return $user->role->permissions()->pluck('slug')->toArray();
    }

    private function permissionAliases(string $permission): array
    {
        $aliases = [
            'gerer-budgets' => ['gerer-budgets'],
            'view-historique' => ['view-historique', 'view-historique-budgets'],
            'alloc-project-budget' => ['alloc-project-budget', 'allouer-projet-budgets'],
            'alloc-st-budget' => ['alloc-st-budget'],
            'manage-depenses' => ['manage-depenses', 'create-depenses', 'edit-depenses', 'delete-depenses', 'gerer-budgets'],
            'view-depenses' => ['view-depenses', 'manage-depenses', 'create-depenses', 'edit-depenses', 'delete-depenses', 'gerer-budgets'],
        ];

        return $aliases[$permission] ?? [$permission];
    }

    private function hasPermission(string $permission): bool
    {
        $granted = $this->userPermissions();

        foreach ($this->permissionAliases($permission) as $alias) {
            if (in_array($alias, $granted, true)) {
                return true;
            }
        }

        return false;
    }

    public function index()
    {
        if (!$this->hasPermission('view-depenses')) {
            abort(403, 'Accès refusé. Permission budget/dépenses requise.');
        }

        $currentYear = (int) date('Y');

        $annualBudget = Budget::where('annee', $currentYear)->first();

        $projets = Projet::orderBy('nom')->get()->map(function (Projet $projet) use ($annualBudget) {
            $projet->dynamic_consomme = $projet->getDynamicConsomme($annualBudget?->id);
            $bp = $annualBudget
                ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $projet->id)->first()
                : null;
            $projet->dynamic_budget = $bp ? $bp->montant_alloue : 0;
            return $projet;
        });
        $projectIds = $projets->pluck('id')->toArray();

        $budgetTotalGlobal = $annualBudget->budget_total ?? 0;

        $budgetConsommeGlobal = !empty($projectIds)
            ? Depense::whereIn('projet_id', $projectIds)->sum('montant')
            : 0;
        $budgetRestantGlobal = $budgetTotalGlobal - $budgetConsommeGlobal;

        $depensesRecentes = !empty($projectIds)
            ? Depense::whereIn('projet_id', $projectIds)->with('projet')->latest()->paginate(20)
            : Depense::query()->whereRaw('1 = 0')->paginate(20);

        $sousTraitances = !empty($projectIds)
            ? SousTraitance::whereIn('projet_id', $projectIds)->orderBy('nom_entreprise')->get()
            : collect();

        $factures = Facture::orderBy('date_emission', 'desc')->limit(20)->get();

        $canDefineBudget = $this->hasPermission('gerer-budgets');
        $canViewHistorique = $this->hasPermission('view-historique');
        $canAllocProject = $this->hasPermission('alloc-project-budget');
        $canAllocST = $this->hasPermission('alloc-st-budget');
        $canManageDepenses = $this->hasPermission('manage-depenses');
        $canDeleteDepenses = $canManageDepenses;

        return view('role-dynamique.depenses.index', compact(
            'annualBudget',
            'currentYear',
            'budgetTotalGlobal',
            'budgetConsommeGlobal',
            'budgetRestantGlobal',
            'depensesRecentes',
            'projets',
            'sousTraitances',
            'factures',
            'canDefineBudget',
            'canViewHistorique',
            'canAllocProject',
            'canAllocST',
            'canManageDepenses',
            'canDeleteDepenses'
        ));
    }
}