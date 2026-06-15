<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::with(['projet', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nom', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->projet_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $documents = $query->latest()->get();
        $projets = Projet::all();

        return view('super-admin.documents.index', compact('documents', 'projets'));
    }

    public function show($id)
    {
        $document = Document::with(['projet', 'user'])->findOrFail($id);
        return view('super-admin.documents.show', compact('document'));
    }

    public function create()
    {
        $projets = Projet::all();
        return view('super-admin.documents.create', compact('projets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'nullable|exists:projets,id',
            'type' => 'required|in:contrat,facture,rapport,photo,plan,autre',
            'type_personnalise' => 'required_if:type,autre|string|max:255|nullable',
            'nom' => 'required|string|max:255',
            'fichier' => 'nullable|file|max:10240',
            'description' => 'nullable|string',
            'categorie' => 'nullable|string',
            'statut' => 'required|in:actif,archive',
        ]);

        $user = auth()->user();

        $fichierPath = null;
        if ($request->hasFile('fichier')) {
            $fichierPath = $request->file('fichier')->store('documents', 'public');
        }

        Document::create([
            'projet_id' => $request->projet_id,
            'user_id' => $user->id ?? null,
            'type' => $request->type,
            'type_personnalise' => $request->type === 'autre' ? $request->type_personnalise : null,
            'nom' => $request->nom,
            'fichier' => $fichierPath,
            'description' => $request->description,
            'categorie' => $request->categorie,
            'statut' => $request->statut,
        ]);

        return redirect()->route('super-admin.documents.index')->with('success', 'Document créé avec succès.');
    }

    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $projets = Projet::all();
        return view('super-admin.documents.edit', compact('document', 'projets'));
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $request->validate([
            'projet_id' => 'nullable|exists:projets,id',
            'type' => 'required|in:contrat,facture,rapport,photo,plan,autre',
            'type_personnalise' => 'required_if:type,autre|string|max:255|nullable',
            'nom' => 'required|string|max:255',
            'fichier' => 'nullable|file|max:10240',
            'description' => 'nullable|string',
            'categorie' => 'nullable|string',
            'statut' => 'required|in:actif,archive',
        ]);

        $data = $request->all();
        if ($request->hasFile('fichier')) {
            $data['fichier'] = $request->file('fichier')->store('documents', 'public');
        }

        $data['type_personnalise'] = $request->type === 'autre' ? $request->type_personnalise : null;

        unset($data['entreprise_id']);
        $document->update($data);

        return redirect()->route('super-admin.documents.index')->with('success', 'Document mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        return redirect()->route('super-admin.documents.index')->with('success', 'Document supprimé avec succès.');
    }
}
