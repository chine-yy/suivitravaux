<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ChecksPermissions;
use App\Models\Phase;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class RolePhasesController extends Controller
{
    use ChecksPermissions;

    public function __construct()
    {
        $this->middleware('permission:view-phases');

        View::composer('role-dynamique.phases.*', function ($view) {
            $view->with('canPermission', fn($perm) => $this->hasPermission($perm));
        });
    }

    private function entrepriseId()
    {
        return 1;
    }

    private function currentActor()
    {
        return auth()->user() ?: auth()->user();
    }

    private function hasPhasePermission(string $permission): bool
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

        $userPermissions = [];
        $isFullAdmin = false;

        $query = \App\Models\Phase::query()->with(['taches', 'projet.sousTraitances']);

        if (request()->filled('nom')) {
            $nom = request('nom');
            $query->whereHas('projet', function ($q) use ($nom) {
                $q->where('nom', 'like', '%' . $nom . '%');
            });
        }

        if ((auth()->check() && method_exists(auth()->user(), 'isAdminEntreprise') && auth()->user()->isAdminEntreprise())) {
            $admin = auth()->user();
            if (method_exists($admin, 'permissions')) {
                $userPermissions = $admin->permissions()->pluck('slug')->toArray();
            }
            $isFullAdmin = $admin->isAdminEntreprise();
        } elseif (auth()->check()) {
            $user = auth()->user();
            $userPermissions = $user->role ? $user->role->permissions()->pluck('slug')->toArray() : [];
        }

        $phases = $query->latest()->paginate(15)->withQueryString();

        return view('role-dynamique.phases.index', compact('phases', 'userPermissions', 'isFullAdmin'));
    }

    public function create()
    {
        $isAdmin = (auth()->check() && method_exists(auth()->user(), 'isAdminEntreprise') && auth()->user()->isAdminEntreprise());
        abort_unless($isAdmin || auth()->check(), 403);
        
        if ($isAdmin) {
            $admin = auth()->user();
            $isFullAdmin = $admin->isAdminEntreprise();
            $userPermissions = $admin->role ? $admin->role->permissions()->pluck('slug')->toArray() : [];

            if (!$isFullAdmin && !in_array('create-phases', $userPermissions)) {
                abort(403, "Vous n'avez pas la permission de créer des phases.");
            }
        }
        
        $projets = Projet::orderBy('nom')->get();
        return view('role-dynamique.phases.create', compact('projets'));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->check(), 403);

        if (!$this->hasPhasePermission('create-phases')) {
            abort(403, "Vous n'avez pas la permission de créer des phases.");
        }

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'nom' => 'required|string|max:255|unique:phases,nom,NULL,id,projet_id,' . $request->projet_id,
            'description' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
        ]);

        Phase::create([
            'projet_id' => $request->projet_id,
            'nom' => $request->nom,
            'description' => $request->description,
            'date_debut' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin_prevue,
        ]);

        return redirect()->route('role-dynamique.phases.index')
            ->with('success', 'Phase créée avec succès.');
    }

    public function show($id)
    {
        $phase = Phase::with(['projet.admin', 'projet.partenaire', 'taches.responsable', 'taches.sousTaches'])->findOrFail($id);

        $userPermissions = [];
        $isFullAdmin = false;

        if ((auth()->check() && method_exists(auth()->user(), 'isAdminEntreprise') && auth()->user()->isAdminEntreprise())) {
            $admin = auth()->user();
            if (method_exists($admin, 'permissions')) {
                $userPermissions = $admin->permissions()->pluck('slug')->toArray();
            }
            $isFullAdmin = $admin->isAdminEntreprise();
        } elseif (auth()->check()) {
            $user = auth()->user();
            $userPermissions = $user->role ? $user->role->permissions()->pluck('slug')->toArray() : [];
        }

        return view('role-dynamique.phases.show', compact('phase', 'userPermissions', 'isFullAdmin'));
    }

    public function edit($id)
    {
        $isAdmin = (auth()->check() && method_exists(auth()->user(), 'isAdminEntreprise') && auth()->user()->isAdminEntreprise());
        abort_unless($isAdmin || auth()->check(), 403);
        
        if ($isAdmin) {
            $admin = auth()->user();
            $isFullAdmin = $admin->isAdminEntreprise();
            $userPermissions = $admin->role ? $admin->role->permissions()->pluck('slug')->toArray() : [];

            if (!$isFullAdmin && !in_array('edit-phases', $userPermissions)) {
                abort(403, "Vous n'avez pas la permission de modifier des phases.");
            }
        }
        
        $phase = Phase::findOrFail($id);
        $projets = Projet::orderBy('nom')->get();
        return view('role-dynamique.phases.edit', compact('phase', 'projets'));
    }

    public function update(Request $request, $id)
    {
        abort_unless(auth()->check(), 403);

        if (!$this->hasPhasePermission('edit-phases')) {
            abort(403, "Vous n'avez pas la permission de modifier des phases.");
        }

        $phase = Phase::findOrFail($id);
        
        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'nom' => 'required|string|max:255|unique:phases,nom,' . $phase->id . ',id,projet_id,' . $request->projet_id,
            'description' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
        ]);

        $phase->update([
            'projet_id' => $request->projet_id,
            'nom' => $request->nom,
            'description' => $request->description,
            'date_debut' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin_prevue,
        ]);

        return redirect()->route('role-dynamique.phases.index')
            ->with('success', 'Phase modifiée avec succès.');
    }

    public function destroy($id)
    {
        abort_unless(auth()->check(), 403);

        if (!$this->hasPhasePermission('delete-phases')) {
            abort(403, "Vous n'avez pas la permission de supprimer des phases.");
        }
        
        $phase = Phase::findOrFail($id);
        $phase->delete();
        return back()->with('success', 'Phase supprimée.');
    }
}
