<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use App\Models\Projet;
use App\Models\Budget;
use App\Models\BudgetProjet;
use App\Models\User;
use Illuminate\Http\Request;
use PDF;

class ContratsController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrat::with(['partenaire', 'projet']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_contrat', 'like', "%{$search}%")
                  ->orWhere('objet', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $contrats = $query->latest()->get();

        return view('super-admin.contrats.index', compact('contrats'));
    }

    public function show($id)
    {
        $contrat = Contrat::with(['partenaire', 'projet', 'createur'])->findOrFail($id);
        return view('super-admin.contrats.show', compact('contrat'));
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
        return view('super-admin.contrats.create', compact('projets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_contrat' => 'nullable|string|unique:contrats,numero_contrat',
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
            'created_by' => $user->id ?? null,
        ]);

        return redirect()->route('super-admin.contrats.index')->with('success', 'Contrat créé avec succès.');
    }

    public function edit($id)
    {
        $contrat = Contrat::findOrFail($id);
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
        return view('super-admin.contrats.edit', compact('contrat', 'projets'));
    }

    public function update(Request $request, $id)
    {
        $contrat = Contrat::findOrFail($id);
        
        $request->validate([
            'numero_contrat' => 'nullable|string|unique:contrats,numero_contrat,' . $id,
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

        $contrat->update($request->all());

        return redirect()->route('super-admin.contrats.index')->with('success', 'Contrat mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $contrat = Contrat::findOrFail($id);
        $contrat->delete();
        return redirect()->route('super-admin.contrats.index')->with('success', 'Contrat supprimé avec succès.');
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
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_contrat', 'like', "%{$search}%")
                    ->orWhere('objet', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $contrats = $query->latest()->get();

        $pdf = PDF::loadView('partials.pdf-contrats-list', compact('contrats'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('contrats_' . date('Y-m-d') . '.pdf');
    }
}
