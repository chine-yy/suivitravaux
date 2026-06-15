<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;

use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\Depense;
use App\Models\Facture;
use App\Models\Projet;
use App\Models\SousTraitance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleBudgetsController extends Controller
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
            'alloc-project-budget' => [
                'alloc-project-budget', 'allouer-projet-budgets', 'allouer-projet-budget-allocation-projet',
                'create-budget-allocation-projet', 'view-budget-allocation-projet',
                'edit-budget-allocation-projet', 'delete-budget-allocation-projet'
            ],
            'alloc-st-budget' => [
                'alloc-st-budget', 'allouer-sous-traitance-budget-allocation-sous-traitance', 'create-budget-allocation-sous-traitance',
                'view-budget-allocation-sous-traitance', 'edit-budget-allocation-sous-traitance', 'delete-budget-allocation-sous-traitance'
            ],
            'manage-depenses' => [
                'manage-depenses', 'create-depenses', 'view-depenses', 'edit-depenses', 'delete-depenses'
            ],
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

        return null;
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
            ->when(
                $entrepriseId,
                fn($q) => $q->where('entreprise_id', $entrepriseId)
            )
            ->orderBy('nom');
    }

    private function ensureCompanyProject(int $projectId): Projet
    {
        $project = $this->companyProjectsQuery()->where('id', $projectId)->first();

        if (!$project) {
            abort(403, 'Projet non autorisé pour votre entreprise.');
        }

        return $project;
    }

    private function ensureCompanySousTraitance(int $stId): SousTraitance
    {
        $projectIds = $this->companyProjectsQuery()->pluck('id')->toArray();

        $st = SousTraitance::whereIn('projet_id', $projectIds)->findOrFail($stId);
        return $st;
    }

    private function ensureCompanyDepense(int $depenseId): Depense
    {
        $projectIds = $this->companyProjectsQuery()->pluck('id')->toArray();
        $depense = Depense::findOrFail($depenseId);

        if (!in_array((int) $depense->projet_id, $projectIds, true)) {
            abort(403, 'Dépense non autorisée pour votre entreprise.');
        }

        return $depense;
    }

    public function index()
    {
        if (!$this->hasPermission('gerer-budgets')
            && !$this->hasPermission('alloc-project-budget')
            && !$this->hasPermission('alloc-st-budget')
            && !$this->hasPermission('manage-depenses')
        ) {
            abort(403, 'Accès refusé. Permission budget requise.');
        }

        $currentYear = (int) request('annee', Carbon::now()->year);
        $annualBudget = Budget::where('annee', $currentYear)->first();

        // Redirect to create if budget not established and user has gerer-budgets
        if (!$annualBudget && $this->hasPermission('gerer-budgets')) {
            return redirect()->route('role-dynamique.budget.create', ['annee' => $currentYear])
                ->with('status', "Le budget pour l'année {$currentYear} n'est pas encore établi. Veuillez le configurer.");
        }

        $adminId = $this->companyAdminId();
        $has = fn(string $perm) => $this->hasPermission($perm);

        $budgets = Budget::latest()->paginate(10);

        if ($annualBudget) {
            $budgetTotalGlobal = (float)$annualBudget->budget_total;
            $budgetAlloueProjets = $annualBudget->getTotalAlloueProjets();
            $budgetAlloueST = $annualBudget->getTotalAlloueSousTraitance();
            $budgetAlloueGlobal = $budgetAlloueProjets + $budgetAlloueST;
            $budgetRestantGlobal = $annualBudget->getSoldeTotal();
        } else {
            $budgetTotalGlobal = 0;
            $budgetAlloueGlobal = 0;
            $budgetRestantGlobal = 0;
        }

        $projectQuery = $this->companyProjectsQuery();
        if (request()->filled('nom')) {
            $projectQuery->where('nom', 'like', '%' . request('nom') . '%');
        }

        $projets = $projectQuery->get();
        $projectIds = $projets->pluck('id')->toArray();

        foreach ($projets as $projet) {
            $projet->dynamic_consomme = $projet->getDynamicConsomme($annualBudget?->id);
            $bp = $annualBudget
                ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $projet->id)->first()
                : null;
            $projet->dynamic_budget = $bp ? (float)$bp->montant_alloue : 0;
            $projet->dynamic_remaining = $bp ? max(0, (float)$bp->montant_alloue - $projet->dynamic_consomme) : 0;
        }

        $budgetParAnnee = Budget::selectRaw('MAX(id) as id, annee, SUM(budget_total) as total')
            ->groupBy('annee')
            ->orderBy('annee', 'desc')
            ->get();

        $sousTraitancesQuery = SousTraitance::with('projet')->whereIn('projet_id', $projectIds);
        if (request()->filled('projet_st')) {
            $sousTraitancesQuery->whereHas('projet', function ($q) {
                $q->where('nom', 'like', '%' . request('projet_st') . '%');
            });
        }
        $sousTraitances = $sousTraitancesQuery->orderBy('nom_entreprise')->get();

        $depensesRecentes = !empty($projectIds)
            ? Depense::whereIn('projet_id', $projectIds)->with('projet')
                ->whereYear('date_depense', $currentYear)
                ->latest()->paginate(20)
            : Depense::query()->whereRaw('1 = 0')->paginate(20);

        $factures = Facture::select('id', 'numero_facture', 'montant_ttc', 'projet_id')->orderBy('numero_facture', 'desc')->get();

        $alertes = [];
        foreach ($projets as $projet) {
            if ($projet->dynamic_budget > 0 && $projet->dynamic_consomme > $projet->dynamic_budget) {
                $alertes[] = [
                    'type' => 'danger',
                    'titre' => 'Budget dépassé',
                    'message' => "Le projet '{$projet->nom}' a dépassé son budget alloué de " .
                        number_format($projet->dynamic_consomme - $projet->dynamic_budget, 0, ',', ' ') . " FCF",
                    'icon' => 'bi-exclamation-octagon'
                ];
            } elseif ($projet->dynamic_budget > 0) {
                $percentage = round(($projet->dynamic_consomme / $projet->dynamic_budget) * 100);
                if ($percentage > 80) {
                    $alertes[] = [
                        'type' => 'warning',
                        'titre' => 'Budget à risque',
                        'message' => "Le projet '{$projet->nom}' a utilisé {$percentage}% de son budget alloué",
                        'icon' => 'bi-exclamation-triangle'
                    ];
                }
            }
        }

        return view('role-dynamique.budget.index', compact(
            'budgets', 'annualBudget', 'currentYear', 'alertes',
            'budgetTotalGlobal', 'budgetAlloueGlobal', 'budgetRestantGlobal',
            'has', 'budgetParAnnee', 'projets', 'sousTraitances',
            'depensesRecentes', 'factures'
        ));
    }



    public function create()
    {
        $this->authorizePermission('gerer-budgets');
        $currentYear = request('annee', date('Y'));
        return view('role-dynamique.budget.create', compact('currentYear'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('gerer-budgets');
        $request->validate([
            'budget_total' => 'required|numeric|min:0',
            'annee' => 'required|integer|min:2000|max:2100',
            'description' => 'nullable|string|max:1000',
        ]);

        Budget::updateOrCreate(
            ['annee' => $request->annee],
            [
                'user_id' => null, // Or companyAdminId() if you want to restrict
                'budget_total' => $request->budget_total,
                'description' => $request->description,
                'statut' => 'valide',
            ]
        );

        return redirect()->route('role-dynamique.budget.index')->with('success', "Budget pour l'année {$request->annee} créé avec succès.");
    }

    public function edit(Budget $budget)
    {
        $this->authorizePermission('gerer-budgets');
        return view('role-dynamique.budget.edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorizePermission('gerer-budgets');
        $request->validate([
            'budget_total' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $budget->update([
            'budget_total' => $request->budget_total,
            'description' => $request->description,
        ]);

        return redirect()->route('role-dynamique.budget.index')->with('success', 'Budget mis à jour.');
    }

    public function show(Budget $budget)
    {
        $this->authorizePermission('gerer-budgets');

        if ($budget->user_id !== $this->companyAdminId()) {
            abort(403);
        }

        return redirect()->route('role-dynamique.budget.edit', $budget);
    }

    public function destroy(Budget $budget)
    {
        $this->authorizePermission('gerer-budgets');

        if ($budget->user_id !== $this->companyAdminId()) {
            abort(403);
        }

        $budget->delete();

        return redirect()->route('role-dynamique.budget.index')->with('success', 'Budget supprimé avec succès.');
    }

    public function assignProjectBudget(Request $request)
    {
        $this->authorizePermission('alloc-project-budget');

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'montant_alloue' => 'required|numeric|min:1',
        ]);

        $project = $this->ensureCompanyProject((int) $request->projet_id);
        $currentYear = (int) date('Y');
        $budget = \App\Models\Budget::where('annee', $currentYear)->first();

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

        return redirect()->route('role-dynamique.budget.index')->with('success', 'Budget projet alloué avec succès.');
    }

    public function assignSousTraitanceBudget(Request $request)
    {
        $this->authorizePermission('alloc-st-budget');

        $request->validate([
            'sous_traitance_id' => 'required|exists:sous_traitances,id',
            'montant_alloue' => 'required_without:montant_contrat|numeric|min:1',
            'montant_contrat' => 'required_without:montant_alloue|numeric|min:1',
        ]);

        $st = $this->ensureCompanySousTraitance((int) $request->sous_traitance_id);
        $montant = (float) ($request->montant_alloue ?? $request->montant_contrat);

        $currentYear = (int) date('Y');
        $budget = \App\Models\Budget::where('annee', $currentYear)->first();

        if (!$budget) {
            return back()->with('error', "Aucun budget annuel défini pour {$currentYear}.");
        }

        $bp = BudgetProjet::where('budget_id', $budget->id)->where('projet_id', $st->projet_id)->first();
        if (!$bp) {
            return back()->with('error', "Aucune allocation budget annuelle définie pour le projet de cette sous-traitance.");
        }

        $projetBudgetAllocated = $bp->montant_alloue;
        $oldAllocation = (float) ($st->montant_contrat ?? 0);
        $consommeActuel = $st->projet->getDynamicConsomme($budget->id);
        $consommeHorsCetteST = $consommeActuel - $oldAllocation;
        $availableForThisST = $projetBudgetAllocated - $consommeHorsCetteST;

        if ($montant > $availableForThisST) {
            return back()->with('error', 'Le montant alloué dépasse le budget disponible du projet (' . number_format($availableForThisST, 0, ',', ' ') . ' FCF).');
        }

        $st->update(['montant_contrat' => $montant]);

        return redirect()->route('role-dynamique.budget.index')->with('success', 'Budget sous-traitance alloué.');
    }

    public function storeDepense(Request $request)
    {
        $this->authorizePermission('manage-depenses');

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'montant' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'categorie' => 'required|in:materiaux,main_oeuvre,equipement,transport,sous_traitance,services,autres',
            'date_depense' => 'required|date',
            'type_paiement' => 'required|in:especes,virement,cheque,carte_bancaire,autres',
            'reference' => 'nullable|string|max:255',
            'statut' => 'required|in:en_attente,validee,rejetee',
        ]);

        $project = $this->ensureCompanyProject((int) $request->projet_id);
        $currentYear = (int) date('Y');
        $budget = \App\Models\Budget::where('annee', $currentYear)->first();

        if ($budget) {
            $bp = BudgetProjet::where('budget_id', $budget->id)
                ->where('projet_id', $project->id)
                ->first();
            $budgetProjetMontant = $bp ? (float) $bp->montant_alloue : 0.0;

            $consommeProjet = $project->getDynamicConsomme($budget->id);
            $restantProjet = $budgetProjetMontant - $consommeProjet;

            if ((float) $request->montant > $restantProjet && $budgetProjetMontant > 0 && $request->statut !== 'rejetee') {
                return back()->with('error', sprintf(
                    'La dépense (%.0f FCF) dépasse le budget restant du projet (%.0f FCF, incluant les sous-traitances et dépenses en attente).',
                    $request->montant,
                    $restantProjet
                ))->withInput();
            }
        }

        Depense::create([
            'projet_id' => $project->id,
            'budget_projet_id' => $bp ? $bp->id : null,
            'montant' => $request->montant,
            'description' => $request->description,
            'categorie' => $request->categorie,
            'date_depense' => $request->date_depense,
            'type_paiement' => $request->type_paiement,
            'reference' => $request->reference,
            'statut' => $request->statut,
        ]);

        return redirect()->route('role-dynamique.depenses.index')->with('success', 'Dépense enregistrée avec succès.');
    }

    public function updateDepense(Request $request, int $depense)
    {
        $this->authorizePermission('manage-depenses');

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'montant' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'categorie' => 'required|in:materiaux,main_oeuvre,equipement,transport,sous_traitance,services,autres',
            'date_depense' => 'required|date',
            'type_paiement' => 'required|in:especes,virement,cheque,carte_bancaire,autres',
            'reference' => 'nullable|string|max:255',
            'statut' => 'required|in:en_attente,validee,rejetee',
        ]);

        $depenseModel = $this->ensureCompanyDepense($depense);
        $project = $this->ensureCompanyProject((int) $request->projet_id);

        $currentYear = (int) date('Y');
        $budget = \App\Models\Budget::where('annee', $currentYear)->first();

        if ($budget) {
            $bp = BudgetProjet::where('budget_id', $budget->id)
                ->where('projet_id', $project->id)
                ->first();
            $budgetProjetMontant = $bp ? (float) $bp->montant_alloue : 0.0;

            $consommeActuel = $project->getDynamicConsomme($budget->id);
            // Deduct old expense if it was affecting the consumed budget (validee or en_attente)
            $oldMontant = in_array($depenseModel->statut, ['validee', 'en_attente']) ? $depenseModel->montant : 0;
            $restantProjet = $budgetProjetMontant - ($consommeActuel - $oldMontant);

            if ((float) $request->montant > $restantProjet && $budgetProjetMontant > 0 && $request->statut !== 'rejetee') {
                return back()->with('error', sprintf(
                    'La dépense modifiée (%.0f FCF) dépasse le budget restant du projet (%.0f FCF, incluant les sous-traitances et dépenses en attente).',
                    $request->montant,
                    $restantProjet
                ))->withInput();
            }
        }

        $depenseModel->update([
            'projet_id' => $project->id,
            'budget_projet_id' => $bp ? $bp->id : null,
            'montant' => $request->montant,
            'description' => $request->description,
            'categorie' => $request->categorie,
            'date_depense' => $request->date_depense,
            'type_paiement' => $request->type_paiement,
            'reference' => $request->reference,
            'statut' => $request->statut,
        ]);

        return redirect()->route('role-dynamique.depenses.index')->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroyDepense(int $depense)
    {
        $this->authorizePermission('manage-depenses');

        $depenseModel = $this->ensureCompanyDepense($depense);
        $depenseModel->delete();

        return redirect()->route('role-dynamique.depenses.index')->with('success', 'Dépense supprimée avec succès.');
    }
}
