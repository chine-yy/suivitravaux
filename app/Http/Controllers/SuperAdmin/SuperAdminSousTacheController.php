<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SousTache;
use App\Models\Tache;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NouvelleSousTacheMail;
use Illuminate\Support\Facades\Auth;

class SuperAdminSousTacheController extends Controller
{
    public function index(Request $request)
    {
        $query = SousTache::with(['tache.projet', 'tache.phase', 'user']);

        if ($request->filled('tache_id')) {
            $query->where('tache_id', $request->tache_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('titre')) {
            $query->where('titre', 'like', '%' . $request->titre . '%');
        }

        $sousTaches = $query->latest()->get();
        $taches = Tache::with(['projet', 'phase'])->get();
        $projets = \App\Models\Projet::with('phases')->get();

        return view('super-admin.sous-taches.index', compact('sousTaches', 'taches', 'projets'));
    }

    public function create()
    {
        $taches = Tache::whereNull('user_id')->with('user')->orderBy('titre')->get();
        $projets = \App\Models\Projet::with('phases')->get();
        $membres = User::membres()->with('role')->get();
        return view('super-admin.sous-taches.create', compact('taches', 'projets', 'membres'));
    }

    public function show($id)
    {
        $sousTache = SousTache::with([
            'tache.projet.partenaire',
            'tache.phase',
        ])->findOrFail($id);

        $personnels = $sousTache->assignedPersonnels();

        return view('super-admin.sous-taches.show', compact('sousTache', 'personnels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tache_id' => 'required|exists:taches,id',
            'titre' => 'required|string|max:255|unique:sous_taches,titre,NULL,id,tache_id,' . $request->tache_id,
            'user_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
            'statut' => 'nullable|in:en_attente,en_cours,terminee,bloquee',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
            'avancement' => 'nullable|integer|min:0|max:100',
        ]);

        $sousTache = SousTache::create([
            'tache_id' => $request->tache_id,
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'description' => $request->description,
            'statut' => $request->statut ?? 'en_attente',
            'date_debut' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin_prevue,
            'avancement' => $request->avancement ?? 0,
        ]);

        if ($request->filled('phase_id')) {
            $tache = Tache::find($request->tache_id);
            if ($tache) {
                $tache->update(['phase_id' => $request->phase_id]);
            }
        }

        return redirect()->route('super-admin.sous-taches.index')->with('success', 'Sous-tâche créée avec succès');
    }

    public function edit($id)
    {
        $sousTache = SousTache::findOrFail($id);
        $taches = Tache::whereNull('user_id')->with('user')->orderBy('titre')->get();
        $projets = \App\Models\Projet::with('phases')->get();
        $membres = User::membres()->with('role')->get();
        return view('super-admin.sous-taches.edit', compact('sousTache', 'taches', 'projets', 'membres'));
    }

    public function update(Request $request, $id)
    {
        $sousTache = SousTache::findOrFail($id);
        $request->validate([
            'tache_id' => 'required|exists:taches,id',
            'user_id' => 'nullable|exists:users,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'statut' => 'required|in:en_attente,en_cours,terminee,bloquee',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
            'avancement' => 'required|integer|min:0|max:100',
        ]);

        $sousTache->update([
            'tache_id' => $request->tache_id,
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'description' => $request->description,
            'statut' => $request->statut,
            'date_debut' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin_prevue,
            'avancement' => $request->avancement,
        ]);

        if ($request->filled('phase_id')) {
            $tache = Tache::find($request->tache_id);
            if ($tache) {
                $tache->update(['phase_id' => $request->phase_id]);
            }
        }

        return redirect()->route('super-admin.sous-taches.index')->with('success', 'Sous-tâche mise à jour');
    }

    public function destroy($id)
    {
        $sousTache = SousTache::findOrFail($id);
        $sousTache->delete();
        return back()->with('success', 'Sous-tâche supprimée');
    }
}
