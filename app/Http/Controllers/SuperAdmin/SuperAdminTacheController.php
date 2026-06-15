<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tache;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NouvelleTacheMail;
use Illuminate\Support\Facades\Auth;

class SuperAdminTacheController extends Controller
{
    public function index(Request $request)
    {
        $query = Tache::with(['projet', 'user', 'sousTaches.user', 'sousTaches.personnels']);

        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->projet_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('phase_id')) {
            $query->where('phase_id', $request->phase_id);
        }
        if ($request->filled('titre')) {
            $query->where('titre', 'like', '%' . $request->titre . '%');
        }

        $taches = $query->latest()->get();
        $projets = Projet::with('phases')->get();

        return view('super-admin.taches.index', compact('taches', 'projets'));
    }

    public function create()
    {
        $projets = Projet::with('phases')->get();
        $membres = User::membres()->with('role')->get();
        return view('super-admin.taches.create', compact('projets', 'membres'));
    }

    public function show($id)
    {
        $tache = Tache::with([
            'projet.partenaire',
            'phase',
            'sousTaches',
        ])->findOrFail($id);

        $personnels = $tache->assignedPersonnels();

        return view('super-admin.taches.show', compact('tache', 'personnels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_tache' => 'required|string|max:255|unique:taches,titre,NULL,id,projet_id,' . $request->projet_id,
            'projet_id' => 'required|exists:projets,id',
            'phase_id' => 'nullable|exists:phases,id',
            'user_id' => 'nullable|exists:users,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
        ]);

        $tache = Tache::create([
            'titre' => $request->nom_tache,
            'projet_id' => $request->projet_id,
            'phase_id' => $request->phase_id,
            'user_id' => $request->user_id,
            'date_debut_prevue' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin,
            'statut' => 'a_faire',
            'description' => $request->description,
        ]);

        return redirect()->route('super-admin.taches.index')->with('success', 'Tâche créée avec succès');
    }

    public function edit($id)
    {
        $tache = Tache::findOrFail($id);
        $projets = Projet::with('phases')->get();
        $membres = User::membres()->with('role')->get();
        return view('super-admin.taches.edit', compact('tache', 'projets', 'membres'));
    }

    public function update(Request $request, $id)
    {
        $tache = Tache::findOrFail($id);
        $request->validate([
            'nom_tache' => 'required|string|max:255',
            'projet_id' => 'required|exists:projets,id',
            'phase_id' => 'nullable|exists:phases,id',
            'user_id' => 'nullable|exists:users,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'statut' => 'required|in:a_faire,en_cours,terminee',
            'description' => 'nullable|string',
        ]);

        $tache->update([
            'titre' => $request->nom_tache,
            'projet_id' => $request->projet_id,
            'phase_id' => $request->phase_id,
            'user_id' => $request->user_id,
            'date_debut_prevue' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin,
            'statut' => $request->statut,
            'description' => $request->description,
        ]);

        return redirect()->route('super-admin.taches.index')->with('success', 'Tâche mise à jour');
    }

    public function destroy($id)
    {
        $tache = Tache::findOrFail($id);
        $tache->delete();
        return back()->with('success', 'Tâche supprimée');
    }
}
