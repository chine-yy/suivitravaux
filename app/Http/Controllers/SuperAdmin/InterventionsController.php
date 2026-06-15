<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\Projet;
use App\Models\Tache;
use App\Models\SousTache;
use App\Models\Partenaire;
use App\Models\User;
use Illuminate\Http\Request;

class InterventionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Intervention::with(['projet', 'tache', 'sousTache', 'partenaire', 'technicien']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('technicien_id')) {
            $query->where('technicien_id', $request->technicien_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('date_intervention')) {
            $query->whereDate('date_intervention', $request->date_intervention);
        }

        $interventions = $query->latest()->get();
        $techniciens = User::nonSuperAdmins()->nonPartenaires()->get();

        return view('super-admin.interventions.index', compact('interventions', 'techniciens'));
    }

    public function create()
    {
        $projets = Projet::all();
        $taches = Tache::all();
        $sousTaches = SousTache::all();
        $partenaires = \App\Models\User::where('type_compte', 'partenaire')->orderBy('name')->get();
        $techniciens = User::with('role')->nonSuperAdmins()->nonPartenaires()->get();
        return view('super-admin.interventions.create', compact('projets', 'taches', 'sousTaches', 'partenaires', 'techniciens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'nullable|exists:projets,id',
            'mission_type' => 'required|in:tache,sous_tache',
            'tache_id' => 'nullable|required_if:mission_type,tache|exists:taches,id',
            'sous_tache_id' => 'nullable|required_if:mission_type,sous_tache|exists:sous_taches,id',
            'technicien_id' => 'nullable|exists:users,id',
            'partenaire_id' => 'nullable|exists:partenaires,id',
            'type' => 'required|in:installation,maintenance,reparation,inspection,autre',
            'type_autre' => 'nullable|required_if:type,autre|string|max:255',
            'description' => 'nullable|string',
            'date_intervention' => 'required|date',
            'duree_minutes' => 'nullable|integer|min:1',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
            'rapport' => 'nullable|string',
        ]);

        $user = auth()->user();

        Intervention::create([
            'projet_id' => $request->projet_id,
            'tache_id' => $request->mission_type == 'tache' ? $request->tache_id : null,
            'sous_tache_id' => $request->mission_type == 'sous_tache' ? $request->sous_tache_id : null,
            'technicien_id' => $request->technicien_id,
            'partenaire_id' => $request->partenaire_id,
            'type' => $request->type,
            'type_autre' => $request->type == 'autre' ? $request->type_autre : null,
            'description' => $request->description,
            'date_intervention' => $request->date_intervention,
            'duree_minutes' => $request->duree_minutes ?? 60,
            'statut' => $request->statut,
            'rapport' => $request->rapport,
            'created_by' => $user->id ?? null,
        ]);

        return redirect()->route('super-admin.interventions.index')->with('success', 'Intervention créée avec succès');
    }

    public function show($id)
    {
        $intervention = Intervention::with(['projet', 'tache', 'sousTache', 'partenaire', 'technicien', 'creator'])->findOrFail($id);
        return view('super-admin.interventions.show', compact('intervention'));
    }

    public function edit($id)
    {
        $intervention = Intervention::findOrFail($id);
        $projets = Projet::all();
        $taches = Tache::all();
        $sousTaches = SousTache::all();
        $partenaires = \App\Models\User::where('type_compte', 'partenaire')->orderBy('name')->get();
        $techniciens = User::with('role')->nonSuperAdmins()->nonPartenaires()->get();
        return view('super-admin.interventions.edit', compact('intervention', 'projets', 'taches', 'sousTaches', 'partenaires', 'techniciens'));
    }

    public function update(Request $request, $id)
    {
        $intervention = Intervention::findOrFail($id);

        $request->validate([
            'projet_id' => 'nullable|exists:projets,id',
            'mission_type' => 'required|in:tache,sous_tache',
            'tache_id' => 'nullable|required_if:mission_type,tache|exists:taches,id',
            'sous_tache_id' => 'nullable|required_if:mission_type,sous_tache|exists:sous_taches,id',
            'technicien_id' => 'nullable|exists:users,id',
            'partenaire_id' => 'nullable|exists:partenaires,id',
            'type' => 'required|in:installation,maintenance,reparation,inspection,autre',
            'type_autre' => 'nullable|required_if:type,autre|string|max:255',
            'description' => 'nullable|string',
            'date_intervention' => 'required|date',
            'duree_minutes' => 'nullable|integer|min:1',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
            'rapport' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['tache_id'] = $request->mission_type == 'tache' ? $request->tache_id : null;
        $data['sous_tache_id'] = $request->mission_type == 'sous_tache' ? $request->sous_tache_id : null;

        $intervention->update($data);

        return redirect()->route('super-admin.interventions.index')->with('success', 'Intervention mise à jour');
    }

    public function destroy($id)
    {
        $intervention = Intervention::findOrFail($id);
        $intervention->delete();
        return redirect()->route('super-admin.interventions.index')->with('success', 'Intervention supprimée');
    }
}
