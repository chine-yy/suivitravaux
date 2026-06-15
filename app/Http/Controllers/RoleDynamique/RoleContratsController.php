<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Models\Projet;
use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\User;
use Illuminate\Http\Request;
use PDF;

class RoleContratsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
        $this->middleware('permission:view-contrats')->only(['index', 'show']);
        $this->middleware('permission:create-contrats')->only(['create', 'store']);
        $this->middleware('permission:edit-contrats')->only(['edit', 'update']);
        $this->middleware('permission:delete-contrats')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Contrat::with(['partenaire', 'projet']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('numero_contrat', 'like', '%' . $search . '%')
                    ->orWhere('objet', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', (string) $request->statut);
        }

        $contrats = $query->latest()->get();

        return view('role-dynamique.contrats.index', compact('contrats'));
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

        return view('role-dynamique.contrats.create', compact('projets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_contrat' => 'nullable|string|max:255|unique:contrats,numero_contrat',
            'partenaire_id' => 'nullable|exists:users,id',
            'projet_id' => 'nullable|exists:projets,id',
            'type' => 'required|in:prestation,marche,sous_traitance,autre',
            'objet' => 'nullable|string',
            'montant' => 'nullable|numeric|min:0',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'conditions' => 'nullable|string',
            'statut' => 'required|in:brouillon,signe,en_cours,termine,annule',
        ]);

        $user = auth()->user();

        Contrat::create([
            'partenaire_id' => $request->partenaire_id,
            'projet_id' => $request->projet_id,
            'numero_contrat' => $request->numero_contrat,
            'type' => $request->type,
            'objet' => $request->objet,
            'montant' => $request->montant ?? 0,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'conditions' => $request->conditions,
            'statut' => $request->statut,
            'created_by' => $user?->id,
        ]);

        return redirect()->route('role-dynamique.contrats.index')->with('success', 'Contrat créé avec succès.');
    }

    public function show(Contrat $contrat)
    {
        $contrat->load(['partenaire', 'projet']);

        return view('role-dynamique.contrats.show', compact('contrat'));
    }

    public function edit(Contrat $contrat)
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

        return view('role-dynamique.contrats.edit', compact('contrat', 'projets'));
    }

    public function update(Request $request, Contrat $contrat)
    {
        $request->validate([
            'numero_contrat' => 'nullable|string|max:255|unique:contrats,numero_contrat,' . $contrat->id,
            'partenaire_id' => 'nullable|exists:users,id',
            'projet_id' => 'nullable|exists:projets,id',
            'type' => 'required|in:prestation,marche,sous_traitance,autre',
            'objet' => 'nullable|string',
            'montant' => 'nullable|numeric|min:0',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'conditions' => 'nullable|string',
            'statut' => 'required|in:brouillon,signe,en_cours,termine,annule',
        ]);

        $contrat->update($request->only([
            'numero_contrat',
            'partenaire_id',
            'projet_id',
            'type',
            'objet',
            'montant',
            'date_debut',
            'date_fin',
            'conditions',
            'statut',
        ]));

        return redirect()->route('role-dynamique.contrats.index')->with('success', 'Contrat mis à jour avec succès.');
    }

    public function destroy(Contrat $contrat)
    {
        $contrat->delete();

        return redirect()->route('role-dynamique.contrats.index')->with('success', 'Contrat supprimé avec succès.');
    }

    public function envoyerPartenaire(Request $request, $id)
    {
        $contrat = Contrat::with('projet.partenaires')->findOrFail($id);

        $partenaireId = null;

        if ($contrat->projet) {
            if ($contrat->projet->partenaire_id) {
                $partenaireId = $contrat->projet->partenaire_id;
            } elseif ($contrat->projet->partenaires->isNotEmpty()) {
                $partenaireId = $contrat->projet->partenaires->first()->id;
            }
        }

        if (!$partenaireId && $contrat->partenaire_id) {
            $partenaireId = $contrat->partenaire_id;
        }

        if (!$partenaireId) {
            return redirect()->back()->with('error', 'Aucun partenaire trouvé pour ce contrat.');
        }

        $contrat->partenaire_id = $partenaireId;
        $contrat->est_envoye_partenaire = true;
        $contrat->date_envoi_partenaire = now();
        $contrat->save();

        return redirect()->back()->with('success', 'Le contrat a été envoyé au partenaire avec succès.');
    }

    public function exportAllPdf(Request $request)
    {
        $query = Contrat::with(['partenaire', 'projet']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('numero_contrat', 'like', '%' . $search . '%')
                    ->orWhere('objet', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', (string) $request->statut);
        }

        $contrats = $query->latest()->get();

        $pdf = PDF::loadView('partials.pdf-contrats-list', compact('contrats'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('contrats_' . date('Y-m-d') . '.pdf');
    }
}
