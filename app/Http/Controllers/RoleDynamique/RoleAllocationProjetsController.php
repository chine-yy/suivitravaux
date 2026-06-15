<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\Depense;
use App\Models\Projet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAllocationProjetsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-budget-allocation-projet')->only(['index', 'show']);
        $this->middleware('permission:edit-budget-allocation-projet')->only(['store']);
        $this->middleware('permission:delete-budget-allocation-projet')->only(['destroy']);
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

    private function ensureCompanyProject(int $projectId): Projet
    {
        $entrepriseId = $this->entrepriseId();
        $project = Projet::query()
            ->when($entrepriseId, fn($q) => $q->where('entreprise_id', $entrepriseId))
            ->where('id', $projectId)
            ->firstOrFail();
        return $project;
    }

    public function index()
    {
        $this->authorizePermission('view-budget-allocation-projet');

        $currentYear = (int) request('annee', Carbon::now()->year);

        $annualBudget = Budget::where('annee', $currentYear)->first();

        $budgetTotalGlobal = $annualBudget ? $annualBudget->budget_total : 0;

        // Alloué aux projets
        $alloueProjets = $annualBudget ? $annualBudget->getTotalAlloueProjets() : 0;
        // Alloué aux sous-traitances
        $alloueST = \App\Models\SousTraitance::sum('montant_contrat');
        
        $budgetAlloueGlobal = $alloueProjets + $alloueST;
        $budgetRestantGlobal = $annualBudget ? $annualBudget->getSoldeTotal() : 0;

        $projectQuery = Projet::query()
            ->when($this->entrepriseId(), fn($q) => $q->where('entreprise_id', $this->entrepriseId()));

        if (request()->filled('nom')) {
            $projectQuery->where('nom', 'like', '%' . request('nom') . '%');
        }

        $projets = $projectQuery->orderBy('nom')->get()->map(function ($projet) use ($annualBudget) {
            $projet->dynamic_consomme = $projet->getDynamicConsomme($annualBudget?->id);
            $bp = $annualBudget
                ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $projet->id)->first()
                : null;
            $projet->dynamic_budget = $bp ? $bp->montant_alloue : 0;
            return $projet;
        });

        $budgetDisponibleAllocation = $budgetRestantGlobal;

        $projetsSansAllocation = $projets->filter(fn($p) => $p->dynamic_budget == 0)->values();

        return view('role-dynamique.allocation-projet.index', compact(
            'annualBudget',
            'currentYear',
            'budgetTotalGlobal',
            'budgetAlloueGlobal',
            'budgetRestantGlobal',
            'budgetDisponibleAllocation',
            'projets',
            'projetsSansAllocation'
        ));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('edit-budget-allocation-projet');

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'montant_alloue' => 'required|numeric|min:1',
        ]);

        $project = $this->ensureCompanyProject((int) $request->projet_id);
        $currentYear = (int) date('Y');
        $budget = Budget::where('annee', $currentYear)->first();

        if (!$budget) {
            return back()->with('error', "Aucun budget annuel défini pour {$currentYear}.");
        }

        $bpOld = BudgetProjet::where('budget_id', $budget->id)
            ->where('projet_id', $project->id)
            ->first();
        $oldAllocation = $bpOld ? $bpOld->montant_alloue : 0;
        $available = $budget->getSoldeTotal() + $oldAllocation;

        if ((float) $request->montant_alloue > $available) {
            return back()->with('error', 'Le montant alloué dépasse le budget disponible (' . number_format($available, 0, ',', ' ') . ' FCF).');
        }

        BudgetProjet::updateOrCreate(
            ['budget_id' => $budget->id, 'projet_id' => $project->id],
            ['montant_alloue' => $request->montant_alloue]
        );

        $project->update(['budget' => $request->montant_alloue]);
        return back()->with('success', 'Budget projet alloué avec succès.');
    }

    public function destroy($id)
    {
         $this->authorizePermission("delete-budget-allocation-projet");
         $currentYear = (int) date("Y");
         $budget = Budget::where("annee", $currentYear)->first();
         if ($budget) {
             BudgetProjet::where("budget_id", $budget->id)->where("projet_id", $id)->delete();
         }
         $projet = $this->ensureCompanyProject((int) $id);
         $projet->update(["budget" => 0]);
         return back()->with("success", "Allocation budget du projet réinitialisée.");
    }
}
