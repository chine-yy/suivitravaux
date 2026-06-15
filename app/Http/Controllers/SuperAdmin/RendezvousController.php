<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Rendezvous;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;

class RendezvousController extends Controller
{
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

        return view('super-admin.rendezvous.index', compact('rendezvous', 'projets'));
    }

    public function show($id)
    {
        $rendezvous = Rendezvous::with(['projet', 'user'])->findOrFail($id);
        return view('super-admin.rendezvous.show', compact('rendezvous'));
    }


    public function create()
    {
        $projets = Projet::all();
        $users = User::all();
        return view('super-admin.rendezvous.create', compact('projets', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'nullable|exists:projets,id',
            'user_id' => 'nullable|exists:users,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_heure' => 'required|date',
            'duree_minutes' => 'nullable|integer|min:1',
            'lieu' => 'nullable|string',
            'type' => 'required|in:reunion,visite,appel,autre',
            'type_autre' => 'nullable|string|max:255',
            'statut' => 'required|in:planifie,confirme,termine,annule',
            'rappel' => 'nullable|boolean',
        ]);

        $user = auth()->user();

        Rendezvous::create([
            'entreprise_id' => $user->entreprise_id ?? 1,
            'projet_id' => $request->projet_id,
            'user_id' => $request->user_id,
            'titre' => $request->titre,
            'description' => $request->description,
            'date_heure' => $request->date_heure,
            'duree_minutes' => $request->duree_minutes ?? 60,
            'lieu' => $request->lieu,
            'type' => $request->type,
            'type_autre' => $request->type_autre,
            'statut' => $request->statut,
            'rappel' => $request->rappel ?? false,
        ]);

        return redirect()->route('super-admin.rendezvous.index')->with('success', 'Rendez-vous créé avec succès');
    }

    public function edit($id)
    {
        $rendezvous = Rendezvous::findOrFail($id);
        $projets = Projet::all();
        $users = User::all();
        return view('super-admin.rendezvous.edit', compact('rendezvous', 'projets', 'users'));
    }

    public function update(Request $request, $id)
    {
        $rendezvous = Rendezvous::findOrFail($id);

        $request->validate([
            'projet_id' => 'nullable|exists:projets,id',
            'user_id' => 'nullable|exists:users,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_heure' => 'required|date',
            'duree_minutes' => 'nullable|integer|min:1',
            'lieu' => 'nullable|string',
            'type' => 'required|in:reunion,visite,appel,autre',
            'type_autre' => 'nullable|string|max:255',
            'statut' => 'required|in:planifie,confirme,termine,annule',
            'rappel' => 'nullable|boolean',
        ]);

        $rendezvous->update($request->all());

        return redirect()->route('super-admin.rendezvous.index')->with('success', 'Rendez-vous mis à jour');
    }

    public function destroy($id)
    {
        $rendezvous = Rendezvous::findOrFail($id);
        $rendezvous->delete();
        return redirect()->route('super-admin.rendezvous.index')->with('success', 'Rendez-vous supprimé');
    }
}
