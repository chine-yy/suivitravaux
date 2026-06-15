<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\SousTraitance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Projet;
use Illuminate\Support\Facades\Mail;
use App\Mail\SousTraitanceBudgetUpdated;
use Illuminate\Support\Facades\Log;

class RoleSousTraitancesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-sous-traitances');
    }

    private function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        return method_exists($user, 'hasPermission') ? $user->hasPermission($permission) : false;
    }

    private function entrepriseId(): ?int
    {
        $activeData = \App\Helpers\SessionHelper::getActiveSessionData();
        if (!empty($activeData['entreprise_id'])) {
            return (int) $activeData['entreprise_id'];
        }

        $user = Auth::user();
        return $user->entreprise_id ?? $user->id_entreprise ?? null;
    }

    public function index()
    {
        $entrepriseId = $this->entrepriseId();
        
        $query = SousTraitance::with('projet');
        
        // Filter by company projects if necessary
        if ($entrepriseId) {
            $query->whereHas('projet', function($q) use ($entrepriseId) {
                $q->where('entreprise_id', $entrepriseId);
            });
        }

        $sousTraitances = $query->latest()->get();
        return view('role-dynamique.sous-traitances.index', compact('sousTraitances'));
    }

    public function show($id)
    {
        $entrepriseId = $this->entrepriseId();
        
        $query = SousTraitance::with('projet');
        
        if ($entrepriseId) {
            $query->whereHas('projet', function($q) use ($entrepriseId) {
                $q->where('entreprise_id', $entrepriseId);
            });
        }

        $sousTraitance = $query->findOrFail($id);
        
        return view('role-dynamique.sous-traitances.show', compact('sousTraitance'));
    }
    public function create()
    {
        if (!Auth::user()->can('create-sous-traitances') && !$this->hasPermission('create-sous-traitances')) {
            abort(403, 'Accès refusé. Permission "create-sous-traitances" requise.');
        }

        $entrepriseId = $this->entrepriseId();
        
        $projetsQuery = Projet::query();
        if ($entrepriseId) {
            $projetsQuery->where('entreprise_id', $entrepriseId);
        }
        $projets = $projetsQuery->get();

        return view('role-dynamique.sous-traitances.create', compact('projets'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('create-sous-traitances') && !$this->hasPermission('create-sous-traitances')) {
            abort(403, 'Accès refusé. Permission "create-sous-traitances" requise.');
        }

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'nom_entreprise' => 'required|string|max:255',
            'contact_nom' => 'nullable|string|max:255',
            'contact_prenom' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_telephone' => 'nullable|string|max:50',
            'description_tache' => 'nullable|string',
            'nombre_employes' => 'nullable|integer|min:1',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'nullable|in:en_attente,en_cours,terminee,annule',
            'notes' => 'nullable|string',
        ]);

        $sousTraitance = SousTraitance::create([
            'projet_id' => $request->projet_id,
            'nom_entreprise' => $request->nom_entreprise,
            'contact_nom' => $request->contact_nom,
            'contact_prenom' => $request->contact_prenom,
            'contact_email' => $request->contact_email,
            'contact_telephone' => $request->contact_telephone,
            'description_tache' => $request->description_tache,
            'nombre_employes' => $request->nombre_employes ?? 1,
            'montant_contrat' => 0,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => $request->statut ?? 'en_attente',
            'notes' => $request->notes,
        ]);

        if ($sousTraitance->contact_email) {
            try {
                Mail::to($sousTraitance->contact_email)->send(new SousTraitanceBudgetUpdated($sousTraitance, 0, true));
            } catch (\Exception $e) {
                Log::error("Failed to send initial sous-traitance email to {$sousTraitance->contact_email}: " . $e->getMessage());
            }
        }

        return redirect()->route('role-dynamique.sous-traitances.index')->with('success', 'Sous-traitance ajoutée avec succès.');
    }

    public function edit($id)
    {
        if (!Auth::user()->can('edit-sous-traitances') && !$this->hasPermission('edit-sous-traitances')) {
            abort(403, 'Accès refusé. Permission "edit-sous-traitances" requise.');
        }

        $entrepriseId = $this->entrepriseId();
        
        $query = SousTraitance::with('projet');
        if ($entrepriseId) {
            $query->whereHas('projet', function($q) use ($entrepriseId) {
                $q->where('entreprise_id', $entrepriseId);
            });
        }
        $sousTraitance = $query->findOrFail($id);

        $projetsQuery = Projet::query();
        if ($entrepriseId) {
            $projetsQuery->where('entreprise_id', $entrepriseId);
        }
        $projets = $projetsQuery->get();

        return view('role-dynamique.sous-traitances.edit', compact('sousTraitance', 'projets'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('edit-sous-traitances') && !$this->hasPermission('edit-sous-traitances')) {
            abort(403, 'Accès refusé. Permission "edit-sous-traitances" requise.');
        }

        $entrepriseId = $this->entrepriseId();
        $query = SousTraitance::query();
        if ($entrepriseId) {
            $query->whereHas('projet', function($q) use ($entrepriseId) {
                $q->where('entreprise_id', $entrepriseId);
            });
        }
        $sousTraitance = $query->findOrFail($id);

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'nom_entreprise' => 'required|string|max:255',
            'contact_nom' => 'nullable|string|max:255',
            'contact_prenom' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_telephone' => 'nullable|string|max:50',
            'description_tache' => 'nullable|string',
            'nombre_employes' => 'nullable|integer|min:1',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'statut' => 'required|in:en_attente,en_cours,terminee,annule',
            'notes' => 'nullable|string',
        ]);

        $data = $request->except('montant_contrat');
        $sousTraitance->update($data);

        return redirect()->route('role-dynamique.sous-traitances.index')->with('success', 'Sous-traitance mise à jour avec succès.');
    }

    public function destroy($id)
    {
        if (!Auth::user()->can('delete-sous-traitances') && !$this->hasPermission('delete-sous-traitances')) {
            abort(403, 'Accès refusé. Permission "delete-sous-traitances" requise.');
        }

        $entrepriseId = $this->entrepriseId();
        $query = SousTraitance::query();
        if ($entrepriseId) {
            $query->whereHas('projet', function($q) use ($entrepriseId) {
                $q->where('entreprise_id', $entrepriseId);
            });
        }
        
        $sousTraitance = $query->findOrFail($id);
        $sousTraitance->delete();
        
        return back()->with('success', 'Sous-traitance supprimée avec succès.');
    }
}
