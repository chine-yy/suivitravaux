<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Rendezvous;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleRendezvousController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Rendezvous::with(['projet', 'user']);

        if ($request->filled('date')) {
            $query->whereDate('date_heure', $request->date);
        }
        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->projet_id);
        }
        if ($request->filled('lieu')) {
            $query->where('lieu', 'like', '%' . $request->lieu . '%');
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $rendezvous = $query->orderBy('date_heure', 'desc')->get();
        $projets = Projet::all();

        return view('role-dynamique.rendezvous.index', compact('rendezvous', 'projets'));
    }

    public function show($id)
    {
        $rendezvous = Rendezvous::with(['projet', 'user'])->findOrFail($id);
        return view('role-dynamique.rendezvous.show', compact('rendezvous'));
    }

    public function create()
    {
        $projets = Projet::orderBy('nom')->get();
        return view('role-dynamique.rendezvous.create', compact('projets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'nullable|exists:projets,id',
            'titre' => 'required|string|max:255',
            'type' => 'required|in:reunion,visite,appel,autre',
            'type_autre' => 'nullable|string|max:255',
            'date_heure' => 'required|date',
            'duree_minutes' => 'nullable|integer|min:5',
            'lieu' => 'nullable|string|max:255',
            'statut' => 'nullable|in:planifie,confirme,termine,annule',
            'description' => 'nullable|string',
        ]);

        Rendezvous::create([
            'titre' => $request->titre,
            'type' => $request->type,
            'type_autre' => $request->type === 'autre' ? $request->type_autre : null,
            'date_heure' => $request->date_heure,
            'duree_minutes' => $request->duree_minutes ?? 60,
            'lieu' => $request->lieu,
            'projet_id' => $request->projet_id,
            'statut' => $request->statut ?? 'planifie',
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('role-dynamique.rendezvous.index')->with('success', 'Rendez-vous créé avec succès');
    }

    public function edit($id)
    {
        $rendezvous = Rendezvous::findOrFail($id);
        $projets = Projet::orderBy('nom')->get();
        return view('role-dynamique.rendezvous.edit', compact('rendezvous', 'projets'));
    }

    public function update(Request $request, $id)
    {
        $rendezvous = Rendezvous::findOrFail($id);

        $request->validate([
            'projet_id' => 'nullable|exists:projets,id',
            'titre' => 'required|string|max:255',
            'type' => 'required|in:reunion,visite,appel,autre',
            'type_autre' => 'nullable|string|max:255',
            'date_heure' => 'required|date',
            'duree_minutes' => 'nullable|integer|min:5',
            'lieu' => 'nullable|string|max:255',
            'statut' => 'nullable|in:planifie,confirme,termine,annule',
            'description' => 'nullable|string',
        ]);

        $rendezvous->update([
            'titre' => $request->titre,
            'type' => $request->type,
            'type_autre' => $request->type === 'autre' ? $request->type_autre : null,
            'date_heure' => $request->date_heure,
            'duree_minutes' => $request->duree_minutes ?? 60,
            'lieu' => $request->lieu,
            'projet_id' => $request->projet_id,
            'statut' => $request->statut ?? 'planifie',
            'description' => $request->description,
        ]);

        return redirect()->route('role-dynamique.rendezvous.index')->with('success', 'Rendez-vous mis à jour');
    }

    public function destroy($id)
    {
        $rendezvous = Rendezvous::findOrFail($id);
        $rendezvous->delete();
        return redirect()->route('role-dynamique.rendezvous.index')->with('success', 'Rendez-vous supprimé');
    }
}