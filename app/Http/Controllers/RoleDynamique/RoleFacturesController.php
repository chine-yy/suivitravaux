<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\Projet;
use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleFacturesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function entrepriseId()
    {
        $user = auth()->user();
        $activeData = \App\Helpers\SessionHelper::getActiveSessionData();
        if (!empty($activeData['entreprise_id'])) {
            return (int) $activeData['entreprise_id'];
        }

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

    public function index(Request $request)
    {
        $entrepriseId = $this->entrepriseId();

        $facturesQuery = Facture::query()
            ->with(['projet', 'partenaire']);

        if ($request->filled('search')) {
            $facturesQuery->where(function ($q) use ($request) {
                $q->where('numero_facture', 'like', '%' . $request->search . '%')
                    ->orWhere('objet', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('statut')) {
            $facturesQuery->where('statut', $request->statut);
        }

        if ($request->filled('projet_id')) {
            $facturesQuery->where('projet_id', $request->projet_id);
        }

        $factures = $facturesQuery->latest()->paginate(10);

        $projets = Projet::orderBy('nom')->get();

        return view('role-dynamique.factures.index', compact('factures', 'projets'));
    }

    public function create()
    {
        $currentYear = (int) date('Y');
        $annualBudget = Budget::where('annee', $currentYear)->first();
        $projets = Projet::with('partenaire', 'partenaires')->orderBy('nom')->get()->map(function ($p) use ($annualBudget) {
            $p->dynamic_consomme = $p->getDynamicConsomme($annualBudget?->id);
            $bp = $annualBudget
                ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $p->id)->first()
                : null;
            $p->dynamic_budget = $bp ? (float) $bp->montant_alloue : 0;
            $p->dynamic_remaining = max(0, $p->dynamic_budget - $p->dynamic_consomme);
            return $p;
        });

        return view('role-dynamique.factures.create', compact('projets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'projet_id' => 'nullable|exists:projets,id',
            'partenaire_id' => 'nullable|exists:users,id',
            'numero_facture' => 'required|string|unique:factures,numero_facture',
            'type' => 'required|in:facture,avoir,proforma',
            'montant_ht' => 'nullable|numeric|min:0',
            'montant_tva' => 'nullable|numeric|min:0',
            'montant_ttc' => 'required|numeric|min:0',
            'date_emission' => 'nullable|date',
            'date_echeance' => 'nullable|date',
            'statut_paiement' => 'required|in:en_attente,paye,en_retard,annule',
            'mode_paiement' => 'nullable|in:virement,cheque,especes,carte',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['entreprise_id'] = $this->entrepriseId() ?? 1;

        // Enforce montant_ttc <= budget restant du projet si un projet est sélectionné
        $currentYear = date('Y');
        $montantTtc = floatval($validated['montant_ttc'] ?? 0);
        $annualBudget = \App\Models\Budget::where('annee', $currentYear)->first();
        if (!empty($validated['projet_id'])) {
            $project = Projet::find($validated['projet_id']);
            if ($project) {
                $consomme = $project->getDynamicConsomme($annualBudget?->id);
                $bp = $annualBudget
                    ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $project->id)->first()
                    : null;
                $budgetProjet = $bp ? (float) $bp->montant_alloue : 0;
                if ($budgetProjet <= 0) {
                    return back()->withInput()->withErrors(['projet_id' => "Veuillez allouer d'abord une somme pour le projet \"" . $project->nom . "\""]);
                }
                $resteProjet = max(0, $budgetProjet - $consomme);
                if ($montantTtc > $resteProjet) {
                    return back()->withInput()->withErrors(['montant_ttc' => "Le montant TTC (" . number_format($montantTtc, 2, ',', ' ') . ") dépasse le budget restant du projet \"" . $project->nom . "\" (" . number_format($resteProjet, 2, ',', ' ') . " FCF)."]);
                }
            }
        }

        Facture::create($validated);

        return redirect()->route('role-dynamique.factures.index')->with('success', 'Facture créée avec succès.');
    }

    public function show($id)
    {
        $facture = Facture::with(['projet', 'partenaire'])->findOrFail($id);
        return view('role-dynamique.factures.show', compact('facture'));
    }

    public function edit($id)
    {
        $currentYear = (int) date('Y');
        $annualBudget = Budget::where('annee', $currentYear)->first();
        $facture = Facture::findOrFail($id);
        $projets = \App\Models\Projet::with('partenaire', 'partenaires')->orderBy('nom')->get()->map(function ($p) use ($annualBudget) {
            $p->dynamic_consomme = $p->getDynamicConsomme($annualBudget?->id);
            $bp = $annualBudget
                ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $p->id)->first()
                : null;
            $p->dynamic_budget = $bp ? (float) $bp->montant_alloue : 0;
            $p->dynamic_remaining = max(0, $p->dynamic_budget - $p->dynamic_consomme);
            return $p;
        });
        return view('role-dynamique.factures.edit', compact('facture', 'projets'));
    }

    public function update(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);

        $validated = $request->validate([
            'projet_id' => 'nullable|exists:projets,id',
            'partenaire_id' => 'nullable|exists:users,id',
            'numero_facture' => 'required|string|unique:factures,numero_facture,' . $id,
            'type' => 'required|in:facture,avoir,proforma',
            'montant_ht' => 'nullable|numeric|min:0',
            'montant_tva' => 'nullable|numeric|min:0',
            'montant_ttc' => 'required|numeric|min:0',
            'date_emission' => 'nullable|date',
            'date_echeance' => 'nullable|date',
            'statut_paiement' => 'required|in:en_attente,paye,en_retard,annule',
            'mode_paiement' => 'nullable|in:virement,cheque,especes,carte',
            'notes' => 'nullable|string',
        ]);

        // Enforce montant_ttc <= budget restant du projet si un projet est sélectionné
        $currentYear = date('Y');
        $newMontant = floatval($validated['montant_ttc'] ?? $facture->montant_ttc);
        $annualBudget = \App\Models\Budget::where('annee', $currentYear)->first();
        $projetId = $validated['projet_id'] ?? $facture->projet_id;
        if (!empty($projetId)) {
            $project = Projet::find($projetId);
            if ($project) {
                $consomme = $project->getDynamicConsomme($annualBudget?->id);
                $bp = $annualBudget
                    ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $project->id)->first()
                    : null;
                $budgetProjet = $bp ? (float) $bp->montant_alloue : 0;
                if ($budgetProjet <= 0) {
                    return back()->withInput()->withErrors(['projet_id' => "Veuillez allouer d'abord une somme pour le projet \"" . $project->nom . "\""]);
                }
                $resteProjet = max(0, $budgetProjet - $consomme);
                // Allow the current facture's montant_ttc to be subtracted from consumed
                $oldFactureMontant = $facture->projet_id == $projetId ? floatval($facture->montant_ttc ?? 0) : 0;
                $allowedProjet = $resteProjet + $oldFactureMontant;
                if ($newMontant > $allowedProjet) {
                    return back()->withInput()->withErrors(['montant_ttc' => "Le montant TTC (" . number_format($newMontant, 2, ',', ' ') . ") dépasse le budget restant du projet \"" . $project->nom . "\" (" . number_format($allowedProjet, 2, ',', ' ') . " FCF)."]);
                }
            }
        }

        $facture->update($validated);

        return redirect()->route('role-dynamique.factures.index')->with('success', 'Facture mise à jour.');
    }

    public function destroy($id)
    {
        $facture = Facture::findOrFail($id);
        $facture->delete();
        return redirect()->route('role-dynamique.factures.index')->with('success', 'Facture supprimée.');
    }

    public function envoyerPartenaire(Request $request, $id)
    {
        $facture = Facture::with('projet.partenaires')->findOrFail($id);

        $partenaireId = null;

        if ($facture->projet) {
            if ($facture->projet->partenaire_id) {
                $partenaireId = $facture->projet->partenaire_id;
            } elseif ($facture->projet->partenaires->isNotEmpty()) {
                $partenaireId = $facture->projet->partenaires->first()->id;
            }
        }

        if (!$partenaireId && $facture->partenaire_id) {
            $partenaireId = $facture->partenaire_id;
        }

        if (!$partenaireId) {
            return redirect()->back()->with('error', 'Aucun partenaire trouvé pour cette facture.');
        }

        $facture->partenaire_id = $partenaireId;
        $facture->est_envoye_partenaire = true;
        $facture->date_envoi_partenaire = now();
        $facture->save();

        return redirect()->back()->with('success', 'La facture a été envoyée au partenaire avec succès.');
    }
}
