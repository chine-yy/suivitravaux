<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\Projet;
use App\Models\User;

use App\Models\Depense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\BudgetUpdated;
use App\Mail\SousTraitanceBudgetUpdated;
use App\Models\SousTraitance;
use App\Models\Facture;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SuperAdminBudgetController extends Controller
{
    public function index()
    {
        $budgetParAnnee = Budget::select(DB::raw('annee'), DB::raw('SUM(budget_total) as total'))
            ->groupBy('annee')
            ->orderBy('annee', 'desc')
            ->get();

        $currentYear = request('annee', date('Y'));
        $annualBudget = Budget::where('annee', $currentYear)->first();

        if ($annualBudget) {
            $budgetTotalGlobal = $annualBudget->budget_total;
            // Alloué aux projets
            $budgetAlloueProjets = $annualBudget->getTotalAlloueProjets();
            // Alloué aux sous-traitances
            $budgetAlloueST = $annualBudget->getTotalAlloueSousTraitance();
            // Total alloué = projets + ST
            $budgetAlloueGlobal = $budgetAlloueProjets + $budgetAlloueST;
            // Restant à allouer = total - alloué
            $budgetRestantGlobal = $annualBudget->getSoldeTotal();
        } else {
            $budgetTotalGlobal = 0;
            $budgetAlloueGlobal = 0;
            $budgetRestantGlobal = 0;
        }

        $projets = Projet::orderBy('nom')->get()->map(function($p) use ($annualBudget) {
            $p->dynamic_consomme = $p->getDynamicConsomme($annualBudget ? $annualBudget->id : null);
            $bp = ($annualBudget && $p) ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $p->id)->first() : null;
            $p->dynamic_budget = $bp ? $bp->montant_alloue : 0;
            return $p;
        });

        // Alertes
        $alertes = [];
        foreach ($projets as $projet) {
            if ($projet->dynamic_budget > 0 && $projet->dynamic_consomme > $projet->dynamic_budget) {
                $alertes[] = [
                    'type' => 'danger',
                    'titre' => 'Budget dépassé',
                    'message' => "Le projet '{$projet->nom}' a dépassé son budget de " .
                        number_format($projet->dynamic_consomme - $projet->dynamic_budget, 0, ',', ' ') . " FCF",
                    'icon' => 'bi-exclamation-octagon'
                ];
            } elseif ($projet->dynamic_budget > 0) {
                $percentage = round(($projet->dynamic_consomme / $projet->dynamic_budget) * 100);
                if ($percentage > 80) {
                    $alertes[] = [
                        'type' => 'warning',
                        'titre' => 'Budget à risque',
                        'message' => "Le projet '{$projet->nom}' a utilisé {$percentage}% de son budget",
                        'icon' => 'bi-exclamation-triangle'
                    ];
                }
            }
        }

        $depensesRecentes = Depense::with('projet')
            ->whereYear('date_depense', $currentYear)
            ->latest()
            ->paginate(15);

        $sousTraitances = SousTraitance::with('projet')->get();

        $factures = Facture::select('id', 'numero_facture', 'montant_ttc', 'projet_id')->orderBy('numero_facture', 'desc')->get();

        return view('super-admin.budget.index', compact(
            'projets', 'sousTraitances', 'factures',
            'budgetParAnnee', 'budgetTotalGlobal', 'budgetAlloueGlobal', 'budgetRestantGlobal',
            'alertes', 'depensesRecentes', 'annualBudget', 'currentYear'
        ));
    }

    /**
     * Display depenses management view (separate page similar to allocation-projet)
     */
    public function depenses()
    {
        $currentYear = request('annee', date('Y'));
        $annualBudget = Budget::where('annee', $currentYear)->first();

        $budgetTotalGlobal = $annualBudget->budget_total ?? 0;

        $projets = Projet::orderBy('nom')->get()->map(function (Projet $projet) use ($annualBudget) {
            $projet->dynamic_consomme = $projet->getDynamicConsomme($annualBudget?->id);
            $bp = $annualBudget
                ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $projet->id)->first()
                : null;
            $projet->dynamic_budget = $bp ? $bp->montant_alloue : 0;

            return $projet;
        });
        $projectIds = $projets->pluck('id')->toArray();

        $budgetConsommeGlobal = !empty($projectIds)
            ? Depense::whereIn('projet_id', $projectIds)->where('statut', 'validee')->whereYear('date_depense', $currentYear)->sum('montant')
            : 0;

        $budgetRestantGlobal = $budgetTotalGlobal - $budgetConsommeGlobal;

        $depensesRecentes = !empty($projectIds)
            ? Depense::whereIn('projet_id', $projectIds)->with('projet')->whereYear('date_depense', $currentYear)->latest()->paginate(20)
            : collect();

        $sousTraitances = !empty($projectIds)
            ? SousTraitance::whereIn('projet_id', $projectIds)->orderBy('nom_entreprise')->get()
            : collect();

        $factures = Facture::select('id', 'numero_facture', 'montant_ttc', 'projet_id')->orderBy('numero_facture', 'desc')->get();

        return view('super-admin.depenses.index', compact(
            'annualBudget', 'currentYear', 'budgetTotalGlobal', 'budgetConsommeGlobal', 'budgetRestantGlobal',
            'depensesRecentes', 'projets', 'sousTraitances', 'factures'
        ));
    }

    public function create()
    {
        $currentYear = Carbon::now()->year;
        return view('super-admin.budget.create', ['currentYear' => $currentYear]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'budget_total' => 'required|numeric|min:0',
            'annee' => 'required|integer|min:2000|max:2100',
            'description' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        Budget::updateOrCreate(
            ['annee' => $request->annee],
            [
                'user_id' => null,
                'budget_total' => $request->budget_total,
                'description' => $request->description,
                'statut' => 'valide',
            ]
        );

        return redirect()->route('super-admin.budget.index')->with('success', "Budget pour l'année {$request->annee} créé avec succès.");
    }

    public function edit(Budget $budget)
    {
        return view('super-admin.budget.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget)
    {
        $request->validate([
            'budget_total' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $budget->update([
            'budget_total' => $request->budget_total,
            'description' => $request->description,
        ]);

        return redirect()->route('super-admin.budget.index')->with('success', 'Budget annuel mis à jour.');
    }

    public function assignProjectBudget(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'montant_alloue' => 'required|numeric|min:0',
        ]);

        $projet = Projet::findOrFail($request->projet_id);
        $currentYear = date('Y');
        $budget = Budget::where('annee', $currentYear)->first();

        if (!$budget) {
            return back()->with('error', "Aucun budget annuel défini pour $currentYear.");
        }

        // Check if enough global budget remains
        $bp_old = BudgetProjet::where('budget_id', $budget->id)
            ->where('projet_id', $projet->id)
            ->first();
        $oldAllocation = $bp_old ? $bp_old->montant_alloue : 0;

        $available = $budget->getSoldeTotal() + $oldAllocation;

        if ($request->montant_alloue > $available) {
            return back()->with('error', "Le montant alloué dépasse le budget disponible (" . number_format($available, 0, ',', ' ') . " FCF).");
        }

        // Update or Create BudgetProjet
        BudgetProjet::updateOrCreate(
            ['budget_id' => $budget->id, 'projet_id' => $projet->id],
            ['montant_alloue' => $request->montant_alloue]
        );

        // Sync with projets table column for backward compatibility
        $projet->update(['budget' => $request->montant_alloue]);

        // Email notifications
        $this->notifyProjectMembers($projet, $request->montant_alloue);

        return redirect()->route('super-admin.budget.index')->with('success', "Budget alloué au projet {$projet->nom} avec succès.");
    }

    private function notifyProjectMembers($projet, $amount)
    {
        $recipients = [];

        // 1. Admin responsible for the project
        if ($projet->admin && $projet->admin->email) {
            $recipients[] = $projet->admin;
        }

        // 2. Project members (from teams)
        $members = User::whereHas('equipes', function($q) use ($projet) {
            $q->where('projet_id', $projet->id);
        })->get();

        foreach ($members as $member) {
            if ($member->email) {
                $recipients[] = $member;
            }
        }

        // Unique recipients
        $uniqueRecipients = collect($recipients)->unique('email');

        foreach ($uniqueRecipients as $recipient) {
            try {
                Mail::to($recipient->email)->send(new BudgetUpdated($projet, $amount, $recipient));
            } catch (\Exception $e) {
                Log::error("Failed to send budget email to {$recipient->email}: " . $e->getMessage());
            }
        }
    }

    public function assignSousTraitanceBudget(Request $request)
    {
        $request->validate([
            'sous_traitance_id' => 'required|exists:sous_traitances,id',
            'montant_contrat' => 'required|numeric|min:0',
        ]);

        $st = SousTraitance::with('projet')->findOrFail($request->sous_traitance_id);
        $currentYear = date('Y');
        $budget = Budget::where('annee', $currentYear)->first();

        if (!$budget) {
            return back()->with('error', "Aucun budget annuel défini pour $currentYear.");
        }

        // Check project budget instead of global budget
        $bp = BudgetProjet::where('budget_id', $budget->id)->where('projet_id', $st->projet_id)->first();
        if (!$bp) {
            return back()->with('error', "Aucune allocation budget annuelle définie pour le projet de cette sous-traitance.");
        }

        $projetBudgetAllocated = $bp->montant_alloue;
        $oldAllocation = $st->montant_contrat;
        $consommeActuel = $st->projet->getDynamicConsomme($budget->id);
        $consommeHorsCetteST = $consommeActuel - $oldAllocation;
        $availableForThisST = $projetBudgetAllocated - $consommeHorsCetteST;

        if ($request->montant_contrat > $availableForThisST) {
            return back()->with('error', "Le montant alloué dépasse le budget restant du projet (" . number_format($availableForThisST, 0, ',', ' ') . " FCF).");
        }

        // Update SousTraitance budget
        $st->update(['montant_contrat' => $request->montant_contrat]);

        // Send email notification to partner
        if ($st->contact_email) {
            try {
                Mail::to($st->contact_email)->send(new SousTraitanceBudgetUpdated($st, $request->montant_contrat));
            } catch (\Exception $e) {
                Log::error("Failed to send sous-traitance budget email to {$st->contact_email}: " . $e->getMessage());
            }
        }

        return redirect()->route('super-admin.budget.index')->with('success', "Budget alloué au service sous-traitance : {$st->nom_entreprise} avec succès.");
    }

    /**
     * Store a new expense. Enforces remaining budget constraint.
     */
    public function storeDepense(Request $request)
    {
        $request->validate([
            'projet_id'    => 'required|exists:projets,id',
            'montant'      => 'required|numeric|min:0.01',
            'description'  => 'nullable|string|max:1000',
            'categorie'    => 'required|in:materiaux,main_oeuvre,equipement,transport,sous_traitance,services,autres',
            'date_depense' => 'required|date',
            'type_paiement'=> 'required|in:especes,virement,cheque,carte_bancaire,autres',
            'reference'    => 'nullable|string|max:255',
            'statut'       => 'required|in:en_attente,validee,rejetee',
        ]);

        $currentYear = date('Y');
        $budget = Budget::where('annee', $currentYear)->first();

        if ($budget) {
            $bp = BudgetProjet::where('budget_id', $budget->id)
                ->where('projet_id', $request->projet_id)->first();
            $projet = Projet::findOrFail($request->projet_id);
            $budgetProjetMontant = $bp ? $bp->montant_alloue : 0;

            $consommeProjet = $projet->getDynamicConsomme($budget->id);
            $restantProjet = $budgetProjetMontant - $consommeProjet;

            if ($request->montant > $restantProjet && $budgetProjetMontant > 0 && $request->statut !== 'rejetee') {
                return back()->with('error', sprintf(
                    'La dépense (%.0f FCF) dépasse le budget restant du projet (%.0f FCF, incluant les sous-traitances et dépenses en attente).',
                    $request->montant, $restantProjet
                ))->withInput();
            }

            $globalAllocated = $budget->getTotalAlloue();
            $globalRemaining = $budget->budget_total - $globalAllocated;
            if ($request->statut === 'validee' && $globalRemaining < 0) {
                return back()->with('error', sprintf(
                    'Le budget global est déjà entièrement alloué. Budget restant: %.0f FCF.',
                    $globalRemaining
                ))->withInput();
            }
        }

        Depense::create([
            'projet_id'     => $request->projet_id,
            'budget_projet_id' => $bp ? $bp->id : null,
            'montant'       => $request->montant,
            'description'   => $request->description,
            'categorie'     => $request->categorie,
            'date_depense'  => $request->date_depense,
            'type_paiement' => $request->type_paiement,
            'reference'     => $request->reference,
            'statut'        => $request->statut,
        ]);

        return redirect()->route('super-admin.depenses.index')->with('success', 'Dépense enregistrée avec succès.');
    }

    /**
     * Update an existing expense. Enforces remaining budget constraint.
     */
    public function updateDepense(Request $request, Depense $depense)
    {
        $request->validate([
            'projet_id'    => 'required|exists:projets,id',
            'montant'      => 'required|numeric|min:0.01',
            'description'  => 'nullable|string|max:1000',
            'categorie'    => 'required|in:materiaux,main_oeuvre,equipement,transport,sous_traitance,services,autres',
            'date_depense' => 'required|date',
            'type_paiement'=> 'required|in:especes,virement,cheque,carte_bancaire,autres',
            'reference'    => 'nullable|string|max:255',
            'statut'       => 'required|in:en_attente,validee,rejetee',
        ]);

        $currentYear = date('Y');
        $budget = Budget::where('annee', $currentYear)->first();

        if ($budget) {
            $bp = BudgetProjet::where('budget_id', $budget->id)
                ->where('projet_id', $request->projet_id)->first();
            $projet = Projet::findOrFail($request->projet_id);
            $budgetProjetMontant = $bp ? $bp->montant_alloue : 0;

            $consommeActuel = $projet->getDynamicConsomme($budget->id);
            // Deduct old expense if it was affecting the consumed budget (validee or en_attente)
            $oldMontant = in_array($depense->statut, ['validee', 'en_attente']) ? $depense->montant : 0;
            $restantProjet = $budgetProjetMontant - ($consommeActuel - $oldMontant);

            if ($request->montant > $restantProjet && $budgetProjetMontant > 0 && $request->statut !== 'rejetee') {
                return back()->with('error', sprintf(
                    'La dépense modifiée (%.0f FCF) dépasse le budget restant du projet (%.0f FCF, incluant les sous-traitances et dépenses en attente).',
                    $request->montant, $restantProjet
                ))->withInput();
            }
        }

        $depense->update([
            'projet_id'     => $request->projet_id,
            'budget_projet_id' => $bp ? $bp->id : null,
            'montant'       => $request->montant,
            'description'   => $request->description,
            'categorie'     => $request->categorie,
            'date_depense'  => $request->date_depense,
            'type_paiement' => $request->type_paiement,
            'reference'     => $request->reference,
            'statut'        => $request->statut,
        ]);

        return redirect()->route('super-admin.depenses.index')->with('success', 'Dépense mise à jour avec succès.');
    }

    /**
     * Delete an expense.
     */
    public function destroyDepense(Depense $depense)
    {
        $depense->delete();
        return redirect()->route('super-admin.depenses.index')->with('success', 'Dépense supprimée.');
    }
    public function destroy(Budget $budget)
    {
        $budget->delete();
        return redirect()->route("super-admin.budget.index")->with("success", "Budget annuel supprimé.");
    }

}
