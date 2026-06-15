<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Intervention;
use App\Models\Partenaire;
use App\Models\Projet;
use App\Models\Tache;
use App\Models\SousTache;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleInterventionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-interventions');
    }

    public function index(Request $request)
    {
        $userId = Auth::id();

        $interventionsQuery = Intervention::query()
            ->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                  ->orWhere('technicien_id', $userId);
            })
            ->with(['projet', 'tache', 'sousTache', 'partenaire', 'technicien'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $interventionsQuery->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%')
                  ->orWhereHas('projet', function ($sub) use ($search) {
                      $sub->where('nom', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('technicien', function ($sub) use ($search) {
                      $sub->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('technicien_id')) {
            $interventionsQuery->where('technicien_id', (int) $request->technicien_id);
        }

        if ($request->filled('statut')) {
            $interventionsQuery->where('statut', $request->statut);
        }

        if ($request->filled('date_intervention')) {
            $interventionsQuery->whereDate('date_intervention', $request->date_intervention);
        }

        $interventions = $interventionsQuery->paginate(10)->withQueryString();

        $techniciens = User::nonSuperAdmins()->nonPartenaires()
            ->orderBy('name')
            ->get(['id', 'name', 'prenom']);

        return view('role-dynamique.interventions.index', compact('interventions', 'techniciens'));
    }

    public function create()
    {
        $partenaires = Partenaire::get();
        $projets = Projet::get();
        $techniciens = User::with('role')->nonSuperAdmins()->nonPartenaires()->get();

        $taches = Tache::get();
        $sousTaches = SousTache::get();

        return view('role-dynamique.interventions.create', compact('partenaires', 'projets', 'techniciens', 'taches', 'sousTaches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'partenaire_id' => 'nullable|exists:partenaires,id',
            'projet_id' => 'nullable|exists:projets,id',
            'mission_type' => 'required|in:tache,sous_tache',
            'tache_id' => 'nullable|required_if:mission_type,tache|exists:taches,id',
            'sous_tache_id' => 'nullable|required_if:mission_type,sous_tache|exists:sous_taches,id',
            'type' => 'required|in:installation,maintenance,reparation,inspection,autre',
            'type_autre' => 'nullable|required_if:type,autre|string|max:255',
            'description' => 'nullable|string',
            'date_intervention' => 'nullable|date',
            'technicien_id' => 'nullable|exists:users,id',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
        ]);

        Intervention::create([
            'partenaire_id' => $request->partenaire_id,
            'projet_id' => $request->projet_id,
            'tache_id' => $request->mission_type == 'tache' ? $request->tache_id : null,
            'sous_tache_id' => $request->mission_type == 'sous_tache' ? $request->sous_tache_id : null,
            'type' => $request->type,
            'type_autre' => $request->type == 'autre' ? $request->type_autre : null,
            'description' => $request->description,
            'date_intervention' => $request->date_intervention,
            'technicien_id' => $request->technicien_id,
            'statut' => $request->statut,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('role-dynamique.interventions.index')->with('success', 'Intervention créée avec succès.');
    }

    public function show($id)
    {
        $intervention = Intervention::with(['projet', 'tache', 'sousTache', 'partenaire', 'technicien'])->findOrFail($id);
        return view('role-dynamique.interventions.show', compact('intervention'));
    }

    public function edit($id)
    {
        $intervention = Intervention::findOrFail($id);

        $partenaires = Partenaire::get();
        $projets = Projet::get();
        $techniciens = User::with('role')->nonSuperAdmins()->nonPartenaires()->get();

        $taches = Tache::get();
        $sousTaches = SousTache::get();

        return view('role-dynamique.interventions.edit', compact('intervention', 'partenaires', 'projets', 'techniciens', 'taches', 'sousTaches'));
    }

    public function update(Request $request, $id)
    {
        $intervention = Intervention::findOrFail($id);

        $request->validate([
            'partenaire_id' => 'nullable|exists:partenaires,id',
            'projet_id' => 'nullable|exists:projets,id',
            'mission_type' => 'required|in:tache,sous_tache',
            'tache_id' => 'nullable|required_if:mission_type,tache|exists:taches,id',
            'sous_tache_id' => 'nullable|required_if:mission_type,sous_tache|exists:sous_taches,id',
            'type' => 'required|in:installation,maintenance,reparation,inspection,autre',
            'type_autre' => 'nullable|required_if:type,autre|string|max:255',
            'description' => 'nullable|string',
            'date_intervention' => 'nullable|date',
            'technicien_id' => 'nullable|exists:users,id',
            'statut' => 'required|in:planifie,en_cours,termine,annule',
        ]);

        $data = $request->all();
        $data['tache_id'] = $request->mission_type == 'tache' ? $request->tache_id : null;
        $data['sous_tache_id'] = $request->mission_type == 'sous_tache' ? $request->sous_tache_id : null;
        $data['type_autre'] = $request->type == 'autre' ? $request->type_autre : null;

        $intervention->update($data);

        return redirect()->route('role-dynamique.interventions.index')->with('success', 'Intervention mise à jour avec succès.');
    }

    public function destroy($id)
    {
        $intervention = Intervention::findOrFail($id);
        $intervention->delete();
        return redirect()->route('role-dynamique.interventions.index')->with('success', 'Intervention supprimée avec succès.');
    }
}