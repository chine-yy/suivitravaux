<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ChecksPermissions;
use App\Models\Tache;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class RoleTachesController extends Controller
{
    use ChecksPermissions;

    public function __construct()
    {
        $this->middleware('permission:view-taches')->except(['create', 'store']);

        View::composer('role-dynamique.taches.*', function ($view) {
            $view->with('canPermission', fn($perm) => $this->hasPermission($perm));
        });
    }

    private function currentActor()
    {
        return auth()->user() ?: auth()->user();
    }

    private function currentActorId()
    {
        return (auth()->check() && method_exists(auth()->user(), 'isAdminEntreprise') && auth()->user()->isAdminEntreprise()) ? auth()->id() : auth()->id();
    }

    private function entrepriseId()
    {
        return 1;
    }

    private function hasTaskPermission(string $permission): bool
    {
        $actor = $this->currentActor();

        if (!$actor) {
            return false;
        }

        if ((auth()->check() && method_exists(auth()->user(), 'isAdminEntreprise') && auth()->user()->isAdminEntreprise()) && $actor->isAdminEntreprise()) {
            return true;
        }

        $permissions = $actor->role ? $actor->role->permissions()->pluck('slug')->toArray() : [];

        return in_array($permission, $permissions, true);
    }

    public function index()
    {
        $entrepriseId = $this->entrepriseId();

        $query = Tache::query()->with(['projet.phases', 'phase', 'sousTaches.user.role', 'user']);

        if (request()->filled('titre')) {
            $query->where('titre', 'like', '%' . request('titre') . '%');
        }

        if (request()->filled('projet_id')) {
            $query->where('projet_id', request('projet_id'));
        }

        if (request()->filled('statut')) {
            $query->where('statut', request('statut'));
        }

        if (request()->filled('phase_id')) {
            $query->where('phase_id', request('phase_id'));
        }

        $taches = $query->latest()->paginate(10)->withQueryString();
        $projets = Projet::with('phases')->orderBy('nom')->get();

        return view('role-dynamique.taches.index', compact('taches', 'projets'));
    }

    public function create()
    {
        abort_unless(auth()->check(), 403);

        if (!$this->hasTaskPermission('create-taches')) {
            abort(403, "Vous n'avez pas la permission de créer des tâches.");
        }

        $entrepriseId = $this->entrepriseId();
        $projets = Projet::with('phases')->orderBy('nom')->get();

        $membres = User::membres()->with('role')->get();
        return view('role-dynamique.taches.create', compact('projets', 'membres'));
    }

    public function show($id)
    {
        $entrepriseId = $this->entrepriseId();
        $tache = Tache::with(['projet.partenaire', 'phase', 'sousTaches.interventions'])->findOrFail($id);

        return view('role-dynamique.taches.show', compact('tache'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->check(), 403);

        if (!$this->hasTaskPermission('create-taches')) {
            abort(403, "Vous n'avez pas la permission de créer des tâches.");
        }

        $request->validate([
            'nom_tache' => 'required|string|max:255|unique:taches,titre,NULL,id,projet_id,' . $request->projet_id,
            'projet_id' => 'required|exists:projets,id',
            'phase_id' => 'nullable|exists:phases,id',
            'user_id' => 'nullable|exists:users,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
        ]);

        $entrepriseId = $this->entrepriseId();
        Tache::create([
            'titre' => $request->nom_tache,
            'projet_id' => $request->projet_id,
            'phase_id' => $request->phase_id,
            'user_id' => $request->user_id,
            'date_debut_prevue' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin,
            'description' => $request->description,
            'statut' => 'a_faire',
            'priorite' => 'normale',
        ]);

        return redirect()->route('role-dynamique.taches.index')
            ->with('success', 'Tâche créée avec succès.');
    }

    public function edit($id)
    {
        abort_unless(auth()->check(), 403);

        if (!$this->hasTaskPermission('edit-taches')) {
            abort(403, "Vous n'avez pas la permission de modifier des tâches.");
        }

        $entrepriseId = $this->entrepriseId();
        $tache = Tache::findOrFail($id);

        $projets = Projet::orderBy('nom')->get();
        $membres = User::membres()->with('role')->get();

        return view('role-dynamique.taches.edit', compact('tache', 'projets', 'membres'));
    }

    public function update(Request $request, $id)
    {
        abort_unless(auth()->check(), 403);

        if (!$this->hasTaskPermission('edit-taches')) {
            abort(403, "Vous n'avez pas la permission de modifier des tâches.");
        }

        $entrepriseId = $this->entrepriseId();
        $tache = Tache::findOrFail($id);

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'nom_tache' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
            'statut' => 'required|in:a_faire,en_cours,terminee',
        ]);

        $tache->update([
            'projet_id' => $request->projet_id,
            'titre' => $request->nom_tache,
            'description' => $request->description,
            'statut' => $request->statut,
            'date_debut_prevue' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin,
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('role-dynamique.taches.index')
            ->with('success', 'Tâche mise à jour.');
    }

    public function destroy($id)
    {
        abort_unless(auth()->check(), 403);

        if (!$this->hasTaskPermission('delete-taches')) {
            abort(403, "Vous n'avez pas la permission de supprimer des tâches.");
        }

        $entrepriseId = $this->entrepriseId();
        $tache = Tache::findOrFail($id);
        $tache->delete();

        return back()->with('success', 'Tâche supprimée.');
    }
}
