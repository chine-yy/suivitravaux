<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ChecksPermissions;
use App\Models\Incident;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class RoleIncidentsController extends Controller
{
    use ChecksPermissions;

    public function __construct()
    {
        $this->middleware('permission:view-incidents');

        View::composer('role-dynamique.incidents.*', function ($view) {
            $view->with('canPermission', fn($perm) => $this->hasPermission($perm));
        });
    }

    public function index()
    {
        $projets = collect();
        $totalIncidents = 0;
        $openIncidents = 0;
        $resolvedIncidents = 0;

        $user = auth()->user();

        $incidents = Incident::with('projet')->latest()->paginate(10);
        $projets = Projet::orderBy('nom')->get();
        $totalIncidents = Incident::count();
        $openIncidents = Incident::whereIn('statut', ['ouvert', 'en_traitement'])->count();
        $resolvedIncidents = Incident::where('statut', 'resolu')->count();

        return view('role-dynamique.incidents.index', compact('incidents', 'projets', 'totalIncidents', 'openIncidents', 'resolvedIncidents'));
    }

    public function create()
    {
        $user = auth()->user();
        $isFullAdmin = $user->isAdminEntreprise();
        $userPermissions = $user->role ? $user->role->permissions()->pluck('slug')->toArray() : [];

        if (!$isFullAdmin && !in_array('create-incidents', $userPermissions)) {
            abort(403, "Vous n'avez pas la permission de créer des incidents.");
        }

        $projets = Projet::orderBy('nom')->get();
        return view('role-dynamique.incidents.create', compact('projets'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isFullAdmin = $user->isAdminEntreprise();
        $userPermissions = $user->role ? $user->role->permissions()->pluck('slug')->toArray() : [];

        if (!$isFullAdmin && !in_array('create-incidents', $userPermissions)) {
            abort(403, "Vous n'avez pas la permission de créer des incidents.");
        }

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'gravite' => 'required|in:faible,moyen,critique',
        ]);

        Incident::create([
            'projet_id' => $request->projet_id,
            'signale_par' => Auth::id(),
            'titre' => $request->titre,
            'description' => $request->description,
            'gravite' => $request->gravite,
            'statut' => 'ouvert',
        ]);

        return redirect()->route('role-dynamique.incidents.index')
            ->with('success', 'Incident enregistré avec succès.');
    }

    public function show($id)
    {
        $incident = Incident::findOrFail($id);
        $projet = Projet::find($incident->projet_id);

        return view('role-dynamique.incidents.show', compact('incident', 'projet'));
    }

    public function edit($id)
    {
        $incident = Incident::findOrFail($id);
        $projets = Projet::orderBy('nom')->get();

        return view('role-dynamique.incidents.edit', compact('incident', 'projets'));
    }

    public function update(Request $request, $id)
    {
        $incident = Incident::findOrFail($id);
        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'gravite' => 'required|in:faible,moyen,critique',
            'statut' => 'required|in:ouvert,en_traitement,resolu,ferme',
        ]);

        $incident->update($request->only(['projet_id', 'titre', 'description', 'gravite', 'statut']));

        return redirect()->route('role-dynamique.incidents.index')
            ->with('success', 'Incident mis à jour.');
    }

    public function destroy($id)
    {
        $incident = Incident::findOrFail($id);
        $incident->delete();

        return back()->with('success', 'Incident supprimé.');
    }
}