<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Fournisseur;
use Illuminate\Http\Request;

class FournisseursController extends Controller
{
    public function export(Request $request)
    {
        return redirect()->route('super-admin.export.pdf.direct', ['type' => 'fournisseur_list', 'id' => 0]);
    }

    public function index(Request $request)
    {
        $query = Fournisseur::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_nom', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $fournisseurs = $query->latest()->get();
        // Fetch unique categories for the filter dropdown
        $categories = Fournisseur::select('categorie')->whereNotNull('categorie')->distinct()->pluck('categorie');

        return view('super-admin.fournisseurs.index', compact('fournisseurs', 'categories'));
    }

    public function create()
    {
        return view('super-admin.fournisseurs.create');
    }

    public function show($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        return view('super-admin.fournisseurs.show', compact('fournisseur'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
            'categorie' => 'nullable|string',
            'site_web' => 'nullable|url',
            'contact_nom' => 'nullable|string',
            'contact_prenom' => 'nullable|string',
            'contact_telephone' => 'nullable|string',
            'notes' => 'nullable|string',
            'statut' => 'required|in:actif,inactif',
        ]);

        $user = auth()->user();

        Fournisseur::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'categorie' => $request->categorie,
            'site_web' => $request->site_web,
            'contact_nom' => $request->contact_nom,
            'contact_prenom' => $request->contact_prenom,
            'contact_telephone' => $request->contact_telephone,
            'notes' => $request->notes,
            'statut' => $request->statut,
        ]);

        return redirect()->route('super-admin.fournisseurs.index')->with('success', 'Fournisseur créé avec succès.');
    }

    public function edit($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        return view('super-admin.fournisseurs.edit', compact('fournisseur'));
    }

    public function update(Request $request, $id)
    {
        $fournisseur = Fournisseur::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string',
            'adresse' => 'nullable|string',
            'categorie' => 'nullable|string',
            'site_web' => 'nullable|url',
            'contact_nom' => 'nullable|string',
            'contact_prenom' => 'nullable|string',
            'contact_telephone' => 'nullable|string',
            'notes' => 'nullable|string',
            'statut' => 'required|in:actif,inactif',
        ]);

        $fournisseur->update($request->all());

        return redirect()->route('super-admin.fournisseurs.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();
        return redirect()->route('super-admin.fournisseurs.index')->with('success', 'Fournisseur supprimé avec succès.');
    }
}
