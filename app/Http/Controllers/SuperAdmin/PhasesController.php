<?php
namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhasesController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Phase::with('projet');

        if ($request->filled('nom')) {
            $query->where('nom', 'like', '%' . $request->nom . '%');
        }

        $phases = $query->latest()->paginate(15);
        return view('super-admin.phases.index', compact('phases'));
    }

    public function create()
    {
        $projets = \App\Models\Projet::orderBy('nom')->get();
        return view('super-admin.phases.create', compact('projets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'nom' => 'required|string|max:255|unique:phases,nom,NULL,id,projet_id,' . $request->projet_id,
            'description' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
        ]);

        $phase = \App\Models\Phase::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'phase' => $phase
            ]);
        }

        return redirect()->route('super-admin.phases.index')->with('success', 'Phase créée avec succès');
    }

    public function show($id)
    {
        $phase = \App\Models\Phase::with(['projet', 'taches', 'taches.sousTaches'])->find($id);

        if (! $phase) {
            return redirect()->route('super-admin.phases.index')->with('error', 'Phase introuvable.');
        }

        return view('super-admin.phases.show', compact('phase'));
    }

    public function edit($id)
    {
        $phase = \App\Models\Phase::findOrFail($id);
        $projets = \App\Models\Projet::orderBy('nom')->get();
        return view('super-admin.phases.edit', compact('phase', 'projets'));
    }

    public function update(Request $request, $id)
    {
        $phase = \App\Models\Phase::findOrFail($id);

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'nom' => 'required|string|max:255|unique:phases,nom,' . $phase->id . ',id,projet_id,' . $request->projet_id,
            'description' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
        ]);

        $phase->update($request->all());

        return redirect()->route('super-admin.phases.index')->with('success', 'Phase mise à jour avec succès');
    }

    public function destroy($id)
    {
        $phase = \App\Models\Phase::findOrFail($id);

        // Optional: prevent deletion if related tasks exist
        if ($phase->taches()->exists()) {
            return redirect()->route('super-admin.phases.index')->with('error', 'Impossible de supprimer une phase contenant des tâches.');
        }

        $phase->delete();
        return redirect()->route('super-admin.phases.index')->with('success', 'Phase supprimée');
    }
}
