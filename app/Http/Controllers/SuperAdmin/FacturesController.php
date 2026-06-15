<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Facture;
use App\Models\Projet;
use App\Models\Contrat;
use App\Models\Budget;
use App\Models\BudgetProjet;
use Illuminate\Http\Request;

class FacturesController extends Controller
{
    public function index(Request $request)
    {
        $query = Facture::with(['partenaire', 'projet', 'contrat']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('numero_facture', 'like', "%{$search}%");
        }

        if ($request->filled('statut_paiement')) {
            $query->where('statut_paiement', $request->statut_paiement);
        }

        if ($request->filled('date_emission_start')) {
            $query->whereDate('date_emission', '>=', $request->date_emission_start);
        }

        if ($request->filled('date_emission_end')) {
            $query->whereDate('date_emission', '<=', $request->date_emission_end);
        }

        $factures = $query->latest()->get();

        return view('super-admin.factures.index', compact('factures'));
    }

    public function show($id)
    {
        $facture = Facture::with(['partenaire', 'projet', 'contrat', 'createur'])->findOrFail($id);
        return view('super-admin.factures.show', compact('facture'));
    }


    public function create()
    {
        $currentYear = (int) date('Y');
        $annualBudget = Budget::where('annee', $currentYear)->first();
        $projets = Projet::with('partenaire', 'partenaires')->get()->map(function ($p) use ($annualBudget) {
            $p->dynamic_consomme = $p->getDynamicConsomme($annualBudget?->id);
            $bp = $annualBudget
                ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $p->id)->first()
                : null;
            $p->dynamic_budget = $bp ? (float) $bp->montant_alloue : 0;
            $p->dynamic_remaining = max(0, $p->dynamic_budget - $p->dynamic_consomme);
            return $p;
        });
        $contrats = Contrat::all();
        $budgetDisponibleFacture = $annualBudget ? max(0, (float) $annualBudget->getSoldeTotal()) : null;

        return view('super-admin.factures.create', compact('projets', 'contrats', 'budgetDisponibleFacture'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_facture' => 'nullable|string|unique:factures,numero_facture',
            'partenaire_id' => 'nullable|exists:users,id',
            'projet_id' => 'nullable|exists:projets,id',
            'contrat_id' => 'nullable|exists:contrats,id',
            'type' => 'required|in:facture,avoir,proforma',
            'montant_ht' => 'nullable|numeric|min:0',
            'montant_tva' => 'nullable|numeric|min:0',
            'montant_ttc' => 'nullable|numeric|min:0',
            'date_emission' => 'nullable|date',
            'date_echeance' => 'nullable|date',
            'statut_paiement' => 'required|in:en_attente,paye,en_retard,annule',
            'mode_paiement' => 'nullable|in:virement,cheque,especes,carte',
            'notes' => 'nullable|string',
        ]);

        $user = auth()->user();

        // Enforce montant_ttc <= budget restant du projet si un projet est sélectionné
        $currentYear = date('Y');
        $annualBudget = \App\Models\Budget::where('annee', $currentYear)->first();
        $montantTtc = floatval($request->montant_ttc ?? 0);
        if (!empty($request->projet_id)) {
            $project = Projet::find($request->projet_id);
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

        Facture::create([
            'entreprise_id' => $user->entreprise_id ?? 1,
            'partenaire_id' => $request->partenaire_id,
            'projet_id' => $request->projet_id,
            'contrat_id' => $request->contrat_id,
            'numero_facture' => $request->numero_facture,
            'type' => $request->type,
            'montant_ht' => $request->montant_ht ?? 0,
            'montant_tva' => $request->montant_tva ?? 0,
            'montant_ttc' => $request->montant_ttc ?? 0,
            'date_emission' => $request->date_emission,
            'date_echeance' => $request->date_echeance,
            'statut_paiement' => $request->statut_paiement,
            'mode_paiement' => $request->mode_paiement,
            'notes' => $request->notes,
            'created_by' => $user->id ?? null,
        ]);

        return redirect()->route('super-admin.factures.index')->with('success', 'Facture créée avec succès.');
    }

    public function edit($id)
    {
        $currentYear = (int) date('Y');
        $annualBudget = Budget::where('annee', $currentYear)->first();
        $facture = Facture::findOrFail($id);
        $projets = Projet::with('partenaire', 'partenaires')->get()->map(function ($p) use ($annualBudget) {
            $p->dynamic_consomme = $p->getDynamicConsomme($annualBudget?->id);
            $bp = $annualBudget
                ? BudgetProjet::where('budget_id', $annualBudget->id)->where('projet_id', $p->id)->first()
                : null;
            $p->dynamic_budget = $bp ? (float) $bp->montant_alloue : 0;
            $p->dynamic_remaining = max(0, $p->dynamic_budget - $p->dynamic_consomme);
            return $p;
        });
        $contrats = Contrat::all();
        $budgetDisponibleFacture = $annualBudget
            ? max(0, (float) $annualBudget->getSoldeTotal() + (float) ($facture->montant_ttc ?? 0))
            : null;

        return view('super-admin.factures.edit', compact('facture', 'projets', 'contrats', 'budgetDisponibleFacture'));
    }

    public function update(Request $request, $id)
    {
        $facture = Facture::findOrFail($id);

        $request->validate([
            'numero_facture' => 'nullable|string|unique:factures,numero_facture,' . $id,
            'partenaire_id' => 'nullable|exists:users,id',
            'projet_id' => 'nullable|exists:projets,id',
            'contrat_id' => 'nullable|exists:contrats,id',
            'type' => 'required|in:facture,avoir,proforma',
            'montant_ht' => 'nullable|numeric|min:0',
            'montant_tva' => 'nullable|numeric|min:0',
            'montant_ttc' => 'nullable|numeric|min:0',
            'date_emission' => 'nullable|date',
            'date_echeance' => 'nullable|date',
            'statut_paiement' => 'required|in:en_attente,paye,en_retard,annule',
            'mode_paiement' => 'nullable|in:virement,cheque,especes,carte',
            'notes' => 'nullable|string',
        ]);

        // Enforce montant_ttc <= budget restant du projet si un projet est sélectionné
        $currentYear = date('Y');
        $annualBudget = \App\Models\Budget::where('annee', $currentYear)->first();
        $newMontant = floatval($request->montant_ttc ?? $facture->montant_ttc);
        $projetId = $request->projet_id ?? $facture->projet_id;
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
                $oldFactureMontant = $facture->projet_id == $projetId ? floatval($facture->montant_ttc ?? 0) : 0;
                $allowedProjet = $resteProjet + $oldFactureMontant;
                if ($newMontant > $allowedProjet) {
                    return back()->withInput()->withErrors(['montant_ttc' => "Le montant TTC (" . number_format($newMontant, 2, ',', ' ') . ") dépasse le budget restant du projet \"" . $project->nom . "\" (" . number_format($allowedProjet, 2, ',', ' ') . " FCF)."]);
                }
            }
        }

        $facture->update($request->all());

        return redirect()->route('super-admin.factures.index')->with('success', 'Facture mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $facture = Facture::findOrFail($id);
        $facture->delete();
        return redirect()->route('super-admin.factures.index')->with('success', 'Facture supprimée avec succès.');
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
