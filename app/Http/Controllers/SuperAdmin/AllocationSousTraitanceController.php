<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\Projet;
use App\Models\SousTraitance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllocationSousTraitanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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

        // Get only projects that have at least one sous-traitance
        $projets = Projet::whereHas('sousTraitances')->orderBy('nom')->get();

        $sousTraitancesQuery = SousTraitance::with('projet');

        if (request()->filled('projet_st')) {
            $sousTraitancesQuery->whereHas('projet', function ($q) {
                $q->where('nom', 'like', '%' . request('projet_st') . '%');
            });
        }

        $sousTraitances = $sousTraitancesQuery->orderBy('nom_entreprise')->get();
        
        $budgetDisponibleAllocation = $budgetRestantGlobal;

        return view('super-admin.allocation-sous-traitance.index', compact(
            'annualBudget',
            'currentYear',
            'budgetTotalGlobal',
            'budgetAlloueGlobal',
            'budgetRestantGlobal',
            'budgetDisponibleAllocation',
            'sousTraitances',
            'projets'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sous_traitance_id' => 'required|exists:sous_traitances,id',
            'montant_contrat' => 'required|numeric|min:0',
        ]);

        $st = SousTraitance::findOrFail($request->sous_traitance_id);
        
        $currentYear = (int) date('Y');
        $budget = Budget::where('annee', $currentYear)->first();

        if (!$budget) {
            return back()->with('error', "Aucun budget annuel défini pour $currentYear.");
        }

        $oldAllocation = $st->montant_contrat ?? 0;
        
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
        $st = SousTraitance::findOrFail($id);
        $st->update(['montant_contrat' => 0]);
        return back()->with('success', 'Allocation budget de la sous-traitance réinitialisée.');
    }
}
