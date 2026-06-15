<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ChecksPermissions;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class RoleProjetsController extends Controller
{
    use ChecksPermissions;

    public function __construct()
    {
        $this->middleware('auth:web');
        $this->middleware('permission:view-projets');
        $this->middleware('permission:create-projets')->only(['create', 'store']);
        $this->middleware('permission:edit-projets')->only(['edit', 'update']);
        $this->middleware('permission:delete-projets')->only(['destroy']);

        View::composer('role-dynamique.projets.*', function ($view) {
            $view->with('canPermission', fn($perm) => $this->hasPermission($perm));
        });
    }

    private function entrepriseId(): ?int
    {
        return 1;
    }

    private function adminsForEntreprise()
    {
        return User::nonSuperAdmins()->nonPartenaires()->get();
    }

    public function index(Request $request)
    {
        $entrepriseId = $this->entrepriseId();
        $userId = Auth::id();

        $projets = Projet::query()
            ->with(['taches', 'phases'])
            ->where(function ($query) use ($userId) {
                // All projects or projects where the user is a member
                // Since entreprise_id is gone, we might show all projects or filter by participation
                // The user said there is only ONE entreprise, so typically a role-dynamique user 
                // in that entreprise should see all projects of that entreprise.
                // For now, I'll allow seeing all projects since they all belong to the same unique entreprise.
            })
            ->when($request->filled('nom'), function ($query) use ($request) {
                $query->where('nom', 'like', '%' . trim((string) $request->nom) . '%');
            })
            ->when($request->filled('statut'), function ($query) use ($request) {
                $query->where('statut', $request->statut);
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('date_debut', $request->date);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('role-dynamique.projets.index', compact('projets'));
    }

    public function create()
    {
        $admins = $this->adminsForEntreprise();
        return view('role-dynamique.projets.create', compact('admins'));
    }

    public function show(Projet $projet)
    {
        $this->authorizeProjet($projet);

        $projet->load([
            'phases',
            'taches.sousTaches',
            'sousTraitances',
            'budgetProjets',
            'partenaire',
        ]);

        $budgetTotal = $projet->budgetProjets->sum('montant_alloue');
        $budgetConsomme = $projet->getDynamicConsomme();
        $budgetRestant = $budgetTotal - $budgetConsomme;
        $budgetPourcentage = $budgetTotal > 0 ? round(($budgetConsomme / $budgetTotal) * 100) : 0;

        $tachesStats = [
            'en_attente' => $projet->taches->where('statut', 'en_attente')->count(),
            'en_cours' => $projet->taches->where('statut', 'en_cours')->count(),
            'terminee' => $projet->taches->where('statut', 'terminee')->count(),
        ];

        $sousTachesStats = [
            'en_cours' => \App\Models\SousTache::whereIn('tache_id', $projet->taches->pluck('id'))->where('statut', 'en_cours')->count(),
            'terminee' => \App\Models\SousTache::whereIn('tache_id', $projet->taches->pluck('id'))->where('statut', 'terminee')->count(),
        ];

        $partenaires = \App\Models\User::where('projet_id', $projet->id)
            ->whereHas('role', fn($q) => $q->where('nom', 'Partenaire'))
            ->get();

        return view('role-dynamique.projets.show', compact(
            'projet',
            'tachesStats',
            'sousTachesStats',
            'budgetTotal',
            'budgetConsomme',
            'budgetRestant',
            'budgetPourcentage',
            'partenaires'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'date_fin_reelle' => 'nullable|date',
            'statut' => 'required|in:en_attente,en_cours,termine,en_retard',
            'avancement' => 'nullable|numeric|min:0|max:100',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $validated['date_fin_prevue'] = $validated['date_fin'] ?? null;
        $validated['date_fin_reelle'] = $validated['date_fin_reelle'] ?? null;
        unset($validated['date_fin']);


        $validated['createur_id'] = Auth::id();

        Projet::create($validated);

        return redirect()->route('role-dynamique.projets.index')
            ->with('success', 'Projet créé avec succès.');
    }

    public function edit(Projet $projet)
    {
        $this->authorizeProjet($projet);
        $admins = $this->adminsForEntreprise();
        return view('role-dynamique.projets.edit', compact('projet', 'admins'));
    }

    public function update(Request $request, Projet $projet)
    {
        $this->authorizeProjet($projet);

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'date_fin_reelle' => 'nullable|date',
            'statut' => 'required|in:en_attente,en_cours,termine,en_retard',
            'avancement' => 'nullable|numeric|min:0|max:100',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $validated['date_fin_prevue'] = $validated['date_fin'] ?? null;
        $validated['date_fin_reelle'] = $validated['date_fin_reelle'] ?? null;
        unset($validated['date_fin']);

        $projet->update($validated);

        return redirect()->route('role-dynamique.projets.index')
            ->with('success', 'Projet mis à jour avec succès.');
    }

    public function destroy(Projet $projet)
    {
        $this->authorizeProjet($projet);
        $projet->delete();

        return redirect()->route('role-dynamique.projets.index')
            ->with('success', 'Projet supprimé avec succès.');
    }

    private function authorizeProjet(Projet $projet)
    {
        if (!Auth::guard('web')->check()) {
            abort(403);
        }
        // Unified company architecture: all projects are accessible to authorized personnel
        return;
    }
}
