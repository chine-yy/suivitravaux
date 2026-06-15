<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\Projet;
use App\Models\SousTraitance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAllocationSousTraitanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-budget-allocation-sous-traitance')->only(['index', 'show']);
        $this->middleware('permission:edit-budget-allocation-sous-traitance')->only(['store']);
        $this->middleware('permission:delete-budget-allocation-sous-traitance')->only(['destroy']);
    }

    private function hasPermission(string $permission): bool
    {
        return auth()->user()->hasPermission($permission);
    }

    private function authorizePermission(string $permission): void
    {
        if (!$this->hasPermission($permission)) {
            abort(403, 'Accès refusé. Permission "' . $permission . '" requise.');
        }
    }

    private function entrepriseId(): ?int
    {
        $activeData = \App\Helpers\SessionHelper::getActiveSessionData();
        if (!empty($activeData['entreprise_id'])) {
            return (int) $activeData['entreprise_id'];
        }

        $user = Auth::user();
        return $user->entreprise_id ?? $user->id_entreprise ?? null;
    }

    private function companyAdminId(): ?int
    {
        $entrepriseId = $this->entrepriseId();
        if (!$entrepriseId) {
            return null;
        }
        return \App\Models\User::entrepriseAdmins()->where('entreprise_id', $entrepriseId)->value('id');
    }

    private function companyProjectsQuery()
    {
        $entrepriseId = $this->entrepriseId();
        return Projet::query()
            ->when($entrepriseId, fn($q) => $q->where('entreprise_id', $entrepriseId))
            ->orderBy('nom');
    }

    private function ensureCompanySousTraitance(int $stId): SousTraitance
    {
        $projectIds = $this->companyProjectsQuery()->pluck('id')->toArray();
        $st = SousTraitance::whereIn('projet_id', $projectIds)->findOrFail($stId);
        return $st;
    }

    public function index()
    {
        $this->authorizePermission('view-budget-allocation-sous-traitance');

        $currentYear = (int) request('annee', Carbon::now()->year);

        $annualBudget = Budget::where('annee', $currentYear)->first();

        $budgetTotalGlobal = $annualBudget ? $annualBudget->budget_total : 0;

        // Alloué aux projets
        $alloueProjets = $annualBudget ? $annualBudget->getTotalAlloueProjets() : 0;
        // Alloué aux sous-traitances
        $alloueST = SousTraitance::sum('montant_contrat');
        
        $budgetAlloueGlobal = $alloueProjets + $alloueST;
        $budgetRestantGlobal = $annualBudget ? $annualBudget->getSoldeTotal() : 0;

        $projectIds = $this->companyProjectsQuery()->pluck('id')->toArray();

        $sousTraitancesQuery = SousTraitance::with('projet')
            ->whereIn('projet_id', $projectIds);

        if (request()->filled('projet_st')) {
            $sousTraitancesQuery->whereHas('projet', function ($q) {
                $q->where('nom', 'like', '%' . request('projet_st') . '%');
            });
        }

        $sousTraitances = $sousTraitancesQuery->orderBy('nom_entreprise')->get();
        $budgetDisponibleAllocation = $budgetRestantGlobal;

        return view('role-dynamique.allocation-sous-traitance.index', compact(
            'annualBudget',
            'currentYear',
            'budgetTotalGlobal',
            'budgetAlloueGlobal',
            'budgetRestantGlobal',
            'budgetDisponibleAllocation',
            'sousTraitances'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('edit-budget-allocation-sous-traitance');

        $request->validate([
            'sous_traitance_id' => 'required|exists:sous_traitances,id',
            'montant_contrat' => 'required|numeric|min:0',
        ]);

        $st = $this->ensureCompanySousTraitance((int) $request->sous_traitance_id);

        $currentYear = (int) date('Y');
        $budget = Budget::where('annee', $currentYear)->first();

        if (!$budget) {
            return back()->with('error', "Aucun budget annuel défini pour {$currentYear}.");
        }

        $oldAllocation = (float) ($st->montant_contrat ?? 0);

        $bp = BudgetProjet::where('budget_id', $budget->id)->where('projet_id', $st->projet_id)->first();
        if (!$bp) {
            return back()->with('error', "Aucune allocation budget annuelle définie pour le projet de cette sous-traitance.");
        }

        $projetBudgetAllocated = $bp->montant_alloue;
        $consommeActuel = $st->projet->getDynamicConsomme($budget->id);
        $consommeHorsCetteST = $consommeActuel - $oldAllocation;
        $availableForThisST = $projetBudgetAllocated - $consommeHorsCetteST;

        if ((float) $request->montant_contrat > $availableForThisST) {
            return back()->with('error', 'Le montant dépasse le budget disponible du projet (' . number_format($availableForThisST, 0, ',', ' ') . ' FCF).');
        }

        $st->update(['montant_contrat' => $request->montant_contrat]);

        return back()->with('success', 'Budget sous-traitance alloué avec succès.');
    }
    public function destroy($id)
    {
         $this->authorizePermission("delete-budget-allocation-sous-traitance");
         $st = $this->ensureCompanySousTraitance((int) $id);
         $st->update(["montant_contrat" => 0]);
         return back()->with("success", "Allocation budget de la sous-traitance ru00e9initialisu00e9e.");
    }

}
