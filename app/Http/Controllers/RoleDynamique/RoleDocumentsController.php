<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RoleDocumentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $has = fn(string $perm) => $this->hasPermission($perm);

        $projets = Projet::whereIn('statut', ['en_attente', 'en_cours', 'en_pause', 'en_retard'])->orderBy('nom')->get();
        $documentsQuery = Document::query();

        if (request()->filled('search')) {
            $documentsQuery->where(function($q) {
                $q->where('nom', 'like', '%' . request('search') . '%')
                  ->orWhere('description', 'like', '%' . request('search') . '%');
            });
        }

        if (request()->filled('projet_id')) {
            $documentsQuery->where('projet_id', request('projet_id'));
        }

        if (request()->filled('type')) {
            $documentsQuery->where('type', request('type'));
        }

        if (request()->filled('statut')) {
            $documentsQuery->where('statut', request('statut'));
        }

        $documents = $documentsQuery->latest()->paginate(15)->withQueryString();

        return view('role-dynamique.documents.index', compact('documents', 'projets', 'has'));
    }

    private function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        if (!$user || !$user->role) {
            return false;
        }
        return $user->hasPermission($permission);
    }

    public function create()
    {
        $projets = Projet::whereIn('statut', ['en_attente', 'en_cours', 'en_pause', 'en_retard'])->orderBy('nom')->get();
        return view('role-dynamique.documents.create', compact('projets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:contrat,facture,rapport,photo,plan,autre',
            'type_personnalise' => 'nullable|required_if:type,autre|string|max:255',
            'projet_id' => 'nullable|exists:projets,id',
            'categorie' => 'nullable|string|max:255',
            'fichier' => 'nullable|file|max:10240',
            'statut' => 'required|in:actif,archive',
            'description' => 'nullable|string',
        ]);

        $fichierPath = null;

        if ($request->hasFile('fichier')) {
            $fichierPath = $request->file('fichier')->store('documents', 'public');
        }

        Document::create([
            'nom' => $request->nom,
            'projet_id' => $request->projet_id,
            'type' => $request->type,
            'type_personnalise' => $request->type === 'autre' ? $request->type_personnalise : null,
            'fichier' => $fichierPath,
            'categorie' => $request->categorie,
            'statut' => $request->statut,
            'description' => $request->description,
        ]);

        return redirect()->route('role-dynamique.documents.index')->with('success', 'Document créé avec succès.');
    }

    public function show(string $id)
    {
        $document = Document::findOrFail($id);
        return view('role-dynamique.documents.show', compact('document'));
    }

    public function edit(string $id)
    {
        $document = Document::findOrFail($id);
        $projets = Projet::whereIn('statut', ['en_attente', 'en_cours', 'en_pause', 'en_retard'])->orderBy('nom')->get();
        return view('role-dynamique.documents.edit', compact('document', 'projets'));
    }

    public function update(Request $request, string $id)
    {
        $document = Document::findOrFail($id);

        $request->validate([
            'nom' => 'required|string|max:255',
            'type' => 'required|in:contrat,facture,rapport,photo,plan,autre',
            'type_personnalise' => 'nullable|required_if:type,autre|string|max:255',
            'projet_id' => 'nullable|exists:projets,id',
            'categorie' => 'nullable|string|max:255',
            'fichier' => 'nullable|file|max:10240',
            'statut' => 'required|in:actif,archive',
            'description' => 'nullable|string',
        ]);

        $data = $request->except('fichier');

        if ($request->hasFile('fichier')) {
            if ($document->fichier && Storage::disk('public')->exists($document->fichier)) {
                Storage::disk('public')->delete($document->fichier);
            }
            $data['fichier'] = $request->file('fichier')->store('documents', 'public');
        }

        $data['type_personnalise'] = $request->type === 'autre' ? $request->type_personnalise : null;

        $document->update($data);

        return redirect()->route('role-dynamique.documents.index')->with('success', 'Document mis à jour avec succès.');
    }

    public function destroy(string $id)
    {
        $document = Document::findOrFail($id);

        if ($document->fichier && Storage::disk('public')->exists($document->fichier)) {
            Storage::disk('public')->delete($document->fichier);
        }

        $document->delete();

        return redirect()->route('role-dynamique.documents.index')->with('success', 'Document supprimé avec succès.');
    }
}