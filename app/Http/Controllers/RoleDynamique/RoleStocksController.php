<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Fournisseur;
use Illuminate\Http\Request;

class RoleStocksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-stocks');
    }

    public function index(Request $request)
    {
        $query = Stock::with('fournisseur');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhere('categorie', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $stocks = $query->latest()->get();
        return view('role-dynamique.stocks.index', compact('stocks'));
    }

    public function show($id)
    {
        $stock = Stock::with('fournisseur')->findOrFail($id);
        return view('role-dynamique.stocks.show', compact('stock'));
    }

    public function export(Request $request)
    {
        // This will be handled by ExportController via a link
        return redirect()->route('role-dynamique.export.pdf.direct', ['type' => 'stock_list', 'id' => 0]);
    }

    public function create()
    {
        $fournisseurs = Fournisseur::all();
        return view('role-dynamique.stocks.create', compact('fournisseurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'reference' => 'nullable|string',
            'categorie' => 'nullable|string',
            'quantite' => 'required|integer|min:0',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'emplacement' => 'nullable|string',
            'description' => 'nullable|string',
            'statut' => 'required|in:disponible,epuise,en_reapprovisionnement',
        ]);

        Stock::create([
            'fournisseur_id' => $request->fournisseur_id,
            'nom' => $request->nom,
            'reference' => $request->reference,
            'categorie' => $request->categorie,
            'quantite' => $request->quantite,
            'prix_unitaire' => $request->prix_unitaire ?? 0,
            'emplacement' => $request->emplacement,
            'description' => $request->description,
            'statut' => $request->statut,
        ]);

        return redirect()->route('role-dynamique.stocks.index')->with('success', 'Stock créé avec succès.');
    }

    public function edit($id)
    {
        $stock = Stock::findOrFail($id);
        $fournisseurs = Fournisseur::all();
        return view('role-dynamique.stocks.edit', compact('stock', 'fournisseurs'));
    }

    public function update(Request $request, $id)
    {
        $stock = Stock::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'reference' => 'nullable|string',
            'categorie' => 'nullable|string',
            'quantite' => 'required|integer|min:0',
            'prix_unitaire' => 'nullable|numeric|min:0',
            'emplacement' => 'nullable|string',
            'description' => 'nullable|string',
            'statut' => 'required|in:disponible,epuise,en_reapprovisionnement',
        ]);

        $stock->update($request->all());

        return redirect()->route('role-dynamique.stocks.index')->with('success', 'Stock mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $stock = Stock::findOrFail($id);
        $stock->delete();
        return redirect()->route('role-dynamique.stocks.index')->with('success', 'Stock supprimé avec succès.');
    }
}
