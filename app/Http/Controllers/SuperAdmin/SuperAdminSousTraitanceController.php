<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SousTraitance;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SousTraitanceBudgetUpdated;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SuperAdminSousTraitanceController extends Controller
{
    public function index(Request $request)
    {
        $query = SousTraitance::with('projet');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nom_entreprise', 'like', "%{$search}%")
                  ->orWhere('contact_nom', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%");
        }

        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->projet_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $sousTraitances = $query->latest()->get();
        $projets = Projet::all();

        return view('super-admin.sous-traitances.index', compact('sousTraitances', 'projets'));
    }

    public function show($id)
    {
        $sousTraitance = SousTraitance::with('projet')->findOrFail($id);
        return view('super-admin.sous-traitances.show', compact('sousTraitance'));
    }

    public function create()
    {
        $projets = Projet::all();
        return view('super-admin.sous-traitances.create', compact('projets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'nom_entreprise' => 'required|string|max:255',
            'contact_nom' => 'nullable|string|max:255',
            'contact_prenom' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_telephone' => 'nullable|string|max:50',
            'description_tache' => 'nullable|string',
            'nombre_employes' => 'nullable|integer|min:1',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'nullable|in:en_attente,en_cours,terminee,annule',
            'notes' => 'nullable|string',
        ]);

        $sousTraitance = SousTraitance::create([
            'projet_id' => $request->projet_id,
            'nom_entreprise' => $request->nom_entreprise,
            'contact_nom' => $request->contact_nom,
            'contact_prenom' => $request->contact_prenom,
            'contact_email' => $request->contact_email,
            'contact_telephone' => $request->contact_telephone,
            'description_tache' => $request->description_tache,
            'nombre_employes' => $request->nombre_employes ?? 1,
            'montant_contrat' => 0, // Initial budget is 0, to be managed in Budget section
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => $request->statut ?? 'en_attente',
            'notes' => $request->notes,
        ]);

        // Send email to partner if email exists
        if ($sousTraitance->contact_email) {
            try {
                Mail::to($sousTraitance->contact_email)->send(new SousTraitanceBudgetUpdated($sousTraitance, 0, true));
            } catch (\Exception $e) {
                Log::error("Failed to send initial sous-traitance email to {$sousTraitance->contact_email}: " . $e->getMessage());
            }
        }

        return redirect()->route('super-admin.sous-traitances.index')->with('success', 'Sous-traitance ajoutée avec succès. Le budget peut être alloué dans la gestion budgétaire.');
    }

    public function edit($id)
    {
        $sousTraitance = SousTraitance::findOrFail($id);
        $projets = Projet::all();
        return view('super-admin.sous-traitances.edit', compact('sousTraitance', 'projets'));
    }

    public function update(Request $request, $id)
    {
        $sousTraitance = SousTraitance::findOrFail($id);

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'nom_entreprise' => 'required|string|max:255',
            'contact_nom' => 'nullable|string|max:255',
            'contact_prenom' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_telephone' => 'nullable|string|max:50',
            'description_tache' => 'nullable|string',
            'nombre_employes' => 'nullable|integer|min:1',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'required|in:en_attente,en_cours,terminee,annule',
            'notes' => 'nullable|string',
        ]);

        $data = $request->except('montant_contrat');
        $sousTraitance->update($data);

        return redirect()->route('super-admin.sous-traitances.index')->with('success', 'Sous-traitance mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $sousTraitance = SousTraitance::findOrFail($id);
        $sousTraitance->delete();
        return back()->with('success', 'Sous-traitance supprimée avec succès.');
    }

    /**
     * Export une sous-traitance spécifique en PDF
     */
    public function exportPdf($id)
    {
        $sousTraitance = SousTraitance::with('projet')->findOrFail($id);
        
        $pdf = Pdf::loadView('partials.pdf-sous-traitance', compact('sousTraitance'))
            ->setPaper('a4')
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0)
            ->setOption('defaultfont', 'sans-serif');
        
        return $pdf->download('sous-traitance_' . $sousTraitance->id . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exporte toutes les sous-traitances en PDF
     */
    public function exportAllPdf()
    {
        $sousTraitances = SousTraitance::with('projet')->latest()->get();
        
        $pdf = Pdf::loadView('partials.pdf-sous-traitances-list', compact('sousTraitances'))
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0)
            ->setOption('margin-right', 0)
            ->setOption('defaultfont', 'sans-serif');
        
        return $pdf->download('sous-traitances_export_' . date('Y-m-d_His') . '.pdf');
    }
}
