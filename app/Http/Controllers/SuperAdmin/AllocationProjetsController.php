<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\Depense;
use App\Models\Projet;
use App\Models\SousTraitance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllocationProjetsController extends Controller
{
    public function index()
    {
        $currentYear = (int) request('annee', Carbon::now()->year);

        $annualBudget = Budget::where('annee', $currentYear)->first();
        $budgetTotalGlobal = $annualBudget ? $annualBudget->budget_total : 0;

        // Alloué aux projets (somme des allocations)
        $alloueProjets = $annualBudget ? $annualBudget->getTotalAlloueProjets() : 0;
        
        // Alloué aux sous-traitances (somme des montants contrat)
        $alloueSousTraitance = SousTraitance::sum('montant_contrat');
        
        $budgetAlloueGlobal = $alloueProjets + $alloueSousTraitance;
        $budgetRestantGlobal = max(0, $budgetTotalGlobal - $budgetAlloueGlobal);

        $projectQuery = Projet::query();
        if (request()->filled('nom')) {
            $projectQuery->where('nom', 'like', '%' . request('nom') . '%');
        }

        $projets = $projectQuery->orderBy('nom')->get()->map(function ($projet) use ($annualBudget) {
            $bp = $annualBudget
                ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $projet->id)->first()
                : null;
            $projet->dynamic_budget = $bp ? $bp->montant_alloue : 0;
            $projet->dynamic_consomme = $projet->getDynamicConsomme($annualBudget?->id);
            return $projet;
        });

        $budgetDisponibleAllocation = $budgetRestantGlobal;

        return view('super-admin.allocation-projet.index', compact(
            'annualBudget',
            'currentYear',
            'budgetTotalGlobal',
            'budgetAlloueGlobal',
            'budgetRestantGlobal',
            'budgetDisponibleAllocation',
            'projets'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'montant_alloue' => 'required|numeric|min:1',
        ]);

        $projet = Projet::findOrFail($request->projet_id);
        $currentYear = (int) date('Y');
        $budget = Budget::where('annee', $currentYear)->first();

        if (!$budget) {
            return back()->with('error', "Aucun budget annuel défini pour $currentYear.");
        }

        $bpOld = BudgetProjet::where('budget_id', $budget->id)
            ->where('projet_id', $projet->id)
            ->first();
        $oldAllocation = $bpOld ? $bpOld->montant_alloue : 0;
        
        // Le nouveau montant doit être > 0 et <= (restant + ancienne allocation)
        $available = $budget->getSoldeTotal() + $oldAllocation;

        if ((float) $request->montant_alloue > $available) {
            return back()->with('error', 'Le montant alloué dépasse le budget disponible (' . number_format($available, 0, ',', ' ') . ' FCF).');
        }

        BudgetProjet::updateOrCreate(
            ['budget_id' => $budget->id, 'projet_id' => $projet->id],
            ['montant_alloue' => $request->montant_alloue]
        );

        $projet->update(['budget' => $request->montant_alloue]);

        return back()->with('success', 'Budget projet alloué avec succès.');
    }
    public function destroy($id)
    {
         $currentYear = (int) date("Y");
         $budget = Budget::where("annee", $currentYear)->first();
         if ($budget) {
             BudgetProjet::where("budget_id", $budget->id)->where("projet_id", $id)->delete();
         }
         $projet = Projet::findOrFail($id);
         $projet->update(["budget" => 0]);
         return back()->with("success", "Allocation budget du projet réinitialisée.");
    }

}
