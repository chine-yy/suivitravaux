<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ChecksPermissions;
use App\Models\SousTache;
use Illuminate\Http\Request;
use App\Models\Tache;
use Illuminate\Support\Facades\View;

class RoleSousTachesController extends Controller
{
    use ChecksPermissions;

    public function __construct()
    {
        $this->middleware('permission:view-sous-taches');
        $this->middleware('permission:create-sous-taches')->only(['create', 'store']);
        $this->middleware('permission:edit-sous-taches')->only(['edit', 'update']);
        $this->middleware('permission:delete-sous-taches')->only(['destroy']);

        View::composer('role-dynamique.sous-taches.*', function ($view) {
            $view->with('canPermission', fn($perm) => $this->hasPermission($perm));
        });
    }

    private function currentActorId()
    {
        return (auth()->check() && method_exists(auth()->user(), 'isAdminEntreprise') && auth()->user()->isAdminEntreprise()) ? auth()->id() : auth()->id();
    }

    private function entrepriseId()
    {
        $activeData = \App\Helpers\SessionHelper::getActiveSessionData();
        if (!empty($activeData['entreprise_id'])) {
            return (int) $activeData['entreprise_id'];
        }

        $actor = auth()->user();
        return $actor->entreprise_id ?? $actor->id_entreprise ?? null;
    }

    public function index(Request $request)
    {
        $query = SousTache::whereHas('tache', function($q) {
            $q->whereNull('user_id');
        })->with(['tache.projet', 'tache.phase', 'user']);

        if ($request->filled('titre')) {
            $query->where('titre', 'like', '%' . $request->titre . '%');
        }

        if ($request->filled('tache_id')) {
            $query->where('tache_id', $request->tache_id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $sousTaches = $query
            ->latest()
            ->paginate(10)
            ->appends($request->query());

        $taches = Tache::whereNull('user_id')->orderBy('titre')->get();

        return view('role-dynamique.sous-taches.index', compact('sousTaches', 'taches'));
    }

    public function create()
    {
        $taches = Tache::orderBy('titre')->get();
        $projets = \App\Models\Projet::with('phases')->get();
        $membres = \App\Models\User::membres()->with('role')->get();

        return view('role-dynamique.sous-taches.create', compact('taches', 'projets', 'membres'));
    }

    public function edit($id)
    {
        $sousTache = SousTache::with(['tache.projet', 'tache.phase'])->findOrFail($id);

        $taches = Tache::whereNull('user_id')->orderBy('titre')->get();
        $projets = \App\Models\Projet::with('phases')->get();
        $membres = \App\Models\User::membres()->with('role')->get();

        return view('role-dynamique.sous-taches.edit', compact('sousTache', 'taches', 'projets', 'membres'));
    }

    public function show($id)
    {
        $sousTache = SousTache::with(['tache.projet', 'tache.phase'])->findOrFail($id);

        return view('role-dynamique.sous-taches.show', compact('sousTache'));
    }

    public function update(Request $request, $id)
    {
        $sousTache = SousTache::findOrFail($id);

        $request->validate([
            'titre' => 'required|string|max:255|unique:sous_taches,titre,' . $id . ',id,tache_id,' . $request->tache_id,
            'tache_id' => 'required|exists:taches,id',
            'user_id' => 'nullable|exists:users,id',
            'priorite' => 'nullable|in:basse,moyenne,haute,normale,critique',
            'description' => 'nullable|string',
            'statut' => 'nullable|in:en_attente,en_cours,terminee,bloquee',
        ]);

        $sousTache->update([
            'titre' => $request->titre,
            'tache_id' => $request->tache_id,
            'user_id' => $request->user_id,
            'description' => $request->description,
            'statut' => $request->statut ?? $sousTache->statut,
        ]);

        return redirect()->route('role-dynamique.sous-taches.index')->with('success', 'Sous-tâche mise à jour.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255|unique:sous_taches,titre,NULL,id,tache_id,' . $request->tache_id,
            'tache_id' => 'required|exists:taches,id',
            'user_id' => 'nullable|exists:users,id',
            'priorite' => 'nullable|in:basse,moyenne,haute,normale,critique',
            'description' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date|after_or_equal:date_debut',
        ]);

        SousTache::create([
            'titre' => $request->titre,
            'tache_id' => $request->tache_id,
            'user_id' => $request->user_id,
            'statut' => 'en_attente',
            'description' => $request->description,
            'date_debut' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin_prevue,
        ]);

        return redirect()->route('role-dynamique.sous-taches.index')->with('success', 'Sous-tâche créée avec succès');
    }

    public function toggle($id)
    {
        $sousTache = SousTache::findOrFail($id);

        $sousTache->statut = $sousTache->statut === 'terminee' ? 'en_cours' : 'terminee';
        $sousTache->save();

        return back()->with('success', 'Statut de la sous-tâche mis à jour.');
    }

    public function destroy($id)
    {
        $sousTache = SousTache::findOrFail($id);
        $sousTache->delete();

        return back()->with('success', 'Sous-tâche supprimée.');
    }
}
