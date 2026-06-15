<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Fournisseur;
use Illuminate\Http\Request;

class RoleFournisseursController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-fournisseurs');
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
        $categories = Fournisseur::select('categorie')->whereNotNull('categorie')->distinct()->pluck('categorie');

        return view('role-dynamique.fournisseurs.index', compact('fournisseurs', 'categories'));
    }

    public function show($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        return view('role-dynamique.fournisseurs.show', compact('fournisseur'));
    }

    public function export(Request $request)
    {
        return redirect()->route('role-dynamique.export.pdf.direct', ['type' => 'fournisseur_list', 'id' => 0]);
    }

    public function create()
    {
        return view('role-dynamique.fournisseurs.create');
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

        Fournisseur::create($request->all());

        return redirect()->route('role-dynamique.fournisseurs.index')->with('success', 'Fournisseur créé avec succès.');
    }

    public function edit($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        return view('role-dynamique.fournisseurs.edit', compact('fournisseur'));
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

        return redirect()->route('role-dynamique.fournisseurs.index')->with('success', 'Fournisseur mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();
        return redirect()->route('role-dynamique.fournisseurs.index')->with('success', 'Fournisseur supprimé avec succès.');
    }
}
