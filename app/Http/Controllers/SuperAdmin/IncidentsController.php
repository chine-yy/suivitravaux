<?php
namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IncidentsController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Incident::with(['projet', 'signalePar']);

        if ($request->filled('projet_id')) {
            $query->where('projet_id', $request->projet_id);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('gravite')) {
            $query->where('gravite', $request->gravite);
        }
        if ($request->filled('titre')) {
            $query->where('titre', 'like', '%' . $request->titre . '%');
        }

        $incidents = $query->latest()->paginate(15);
        $projets = \App\Models\Projet::orderBy('nom')->get();

        $totalIncidents = \App\Models\Incident::count();
        $openIncidents = \App\Models\Incident::whereIn('statut', ['ouvert', 'en_traitement'])->count();
        $resolvedIncidents = \App\Models\Incident::where('statut', 'resolu')->count();

        return view('super-admin.incidents.index', compact('incidents', 'totalIncidents', 'openIncidents', 'resolvedIncidents', 'projets'));
    }

    public function create()
    {
        $projets = \App\Models\Projet::orderBy('nom')->get();
        return view('super-admin.incidents.create', compact('projets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'gravite' => 'required|in:faible,moyen,critique',
        ]);

        \App\Models\Incident::create([
            'projet_id' => $request->projet_id,
            'signale_par' => auth()->id(),
            'titre' => $request->titre,
            'description' => $request->description,
            'gravite' => $request->gravite,
            'statut' => 'ouvert',
        ]);

        return redirect()->route('super-admin.incidents.index')
            ->with('success', 'Incident enregistré avec succès.');
    }

    public function show($id)
    {
        $incident = \App\Models\Incident::with(['projet', 'signalePar'])->findOrFail($id);
        return view('super-admin.incidents.show', compact('incident'));
    }

    public function edit($id)
    {
        $incident = \App\Models\Incident::findOrFail($id);
        $projets = \App\Models\Projet::orderBy('nom')->get();
        return view('super-admin.incidents.edit', compact('incident', 'projets'));
    }

    public function update(Request $request, $id)
    {
        $incident = \App\Models\Incident::findOrFail($id);
        
        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'gravite' => 'required|in:faible,moyen,critique',
            'statut' => 'required|in:ouvert,en_traitement,resolu',
        ]);

        $incident->update($request->all());

        return redirect()->route('super-admin.incidents.index')
            ->with('success', 'Incident mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $incident = \App\Models\Incident::findOrFail($id);
        $incident->delete();

        return redirect()->route('super-admin.incidents.index')
            ->with('success', 'Incident supprimé avec succès.');
    }
}
