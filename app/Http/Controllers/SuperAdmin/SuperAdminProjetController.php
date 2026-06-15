<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\Partenaire;

use Illuminate\Http\Request;
use App\Services\PhpMailerService;
use Illuminate\Support\Facades\View;

class SuperAdminProjetController extends Controller
{
    public function index(Request $request)
    {
        $query = Projet::with(['partenaire']);

        if ($request->filled('nom')) {
            $query->where('nom', 'like', '%' . $request->nom . '%');
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('date')) {
            $query->whereDate('date_debut', $request->date);
        }

        $projets = $query->latest()->get();
        return view('super-admin.projets.index', compact('projets'));
    }

    public function create()
    {
        $partenaires = \App\Models\User::where('type_compte', 'partenaire')->orderBy('name')->get();
        return view('super-admin.projets.create', compact('partenaires'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:projets,nom',
            'partenaire_id' => 'nullable|exists:partenaires,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'description' => 'nullable|string',
            'statut' => 'required|in:en_attente,en_cours,termine,en_retard',
            'avancement' => 'required|numeric|min:0|max:100',
        ]);

        $projet = Projet::create([
            'nom' => $request->nom,
            'partenaire' => null,
            'partenaire_id' => $request->partenaire_id,
            'date_debut' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin,
            'budget' => 0,
            'budget_consomme' => 0,
            'description' => $request->description,
            'statut' => $request->statut,
            'avancement' => $request->avancement,
        ]);


        return redirect()->route('super-admin.projets.index')->with('success', 'Projet créé avec succès');
    }

    public function show($id)
    {
        $projet = Projet::with(['phases', 'taches.sousTaches', 'sousTraitances', 'budgetProjets', 'partenaire', 'rapports.auteur'])->findOrFail($id);

        // Budget stats
        $budgetTotal = $projet->budgetProjets->sum('montant_alloue');
        $budgetConsomme = $projet->getDynamicConsomme();
        $budgetRestant = $budgetTotal - $budgetConsomme;
        $budgetPourcentage = $budgetTotal > 0 ? round(($budgetConsomme / $budgetTotal) * 100) : 0;

        // Data for charts
        $tachesStats = [
            'en_attente' => $projet->taches->where('statut', 'en_attente')->count(),
            'en_cours' => $projet->taches->where('statut', 'en_cours')->count(),
            'terminee' => $projet->taches->where('statut', 'terminee')->count(),
        ];

        $sousTachesStats = [
            'en_cours' => \App\Models\SousTache::whereIn('tache_id', $projet->taches->pluck('id'))->where('statut', 'en_cours')->count(),
            'terminee' => \App\Models\SousTache::whereIn('tache_id', $projet->taches->pluck('id'))->where('statut', 'terminee')->count(),
        ];

        $partenaires = \App\Models\User::whereHas('role', function($q) {
            $q->where('nom', 'Partenaire');
        })->where('projet_id', $projet->id)->get();

        return view('super-admin.projets.show', compact(
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

    public function edit($id)
    {
        $projet = Projet::findOrFail($id);
        $partenaires = \App\Models\User::where('type_compte', 'partenaire')->orderBy('name')->get();
        return view('super-admin.projets.edit', compact('projet', 'partenaires'));
    }

    public function update(Request $request, $id)
    {
        $projet = Projet::findOrFail($id);
        $request->validate([
            'nom' => 'required|string|max:255|unique:projets,nom,' . $id,
            'partenaire_id' => 'nullable|exists:partenaires,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'date_fin_reelle' => 'nullable|date',
            'statut' => 'required|in:en_attente,en_cours,termine,en_retard',
            'avancement' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ], [
            'nom.unique' => 'Un projet avec ce nom existe déjà.',
        ]);

        $projet->update([
            'nom' => $request->nom,
            'partenaire_id' => $request->partenaire_id,
            'date_debut' => $request->date_debut,
            'date_fin_prevue' => $request->date_fin,
            'date_fin_reelle' => $request->date_fin_reelle ?? null,
            'statut' => $request->statut,
            'avancement' => $request->avancement,
            'description' => $request->description,
        ]);

        return redirect()->route('super-admin.projets.index')->with('success', 'Projet mis à jour');
    }

    public function destroy($id)
    {
        $projet = Projet::with(['partenaire'])->findOrFail($id);

        $recipients = [];

        // 1. Super Admin (tous les admins avec super_admin role)
        $superAdmins = \App\Models\User::whereHas('role', function ($q) {
            $q->where('nom', \App\Models\User::ROLE_SUPER_ADMIN);
        })->where('is_active', true)->get();
        foreach ($superAdmins as $sa) {
            if ($sa->email) {
                $recipients[] = [
                    'email' => $sa->email,
                    'name' => trim(($sa->prenom ?? '') . ' ' . ($sa->nom ?? '')) ?: ($sa->name ?? 'Super Admin'),
                    'role' => 'Super Administrateur',
                ];
            }
        }


        // 3. Utilisateurs avec permission "projet" (role dynamique)
        $usersWithProjetPerm = \App\Models\User::whereHas('role.permissions', function($q) {
            $q->where('slug', 'projet');
        })->where('is_active', true)->get();
        foreach ($usersWithProjetPerm as $user) {
            if ($user->email && !collect($recipients)->contains('email', $user->email)) {
                $recipients[] = [
                    'email' => $user->email,
                    'name' => $user->name,
                    'role' => $user->role->nom ?? 'Utilisateur',
                ];
            }
        }

        // Envoyer les emails
        $emailSent = 0;
        foreach ($recipients as $recipient) {
            try {
                \Illuminate\Support\Facades\Mail::to($recipient['email'])->send(new \App\Mail\ProjectDeletedMail([
                    'prenom' => $recipient['name'],
                    'projet_nom' => $projet->nom,
                    'role_label' => $recipient['role'],
                    'projet_budget' => $projet->budget,
                    'salaire' => 0,
                    'chef_projet_nom' => 'Super Admin',
                    'chef_projet_prenom' => '',
                ]));
                $emailSent++;
            } catch (\Exception $e) {
                // Continue even if one fails
            }
        }

        $projet->delete();
        return back()->with('success', "Projet supprimé. {$emailSent} notification(s) envoyée(s) aux personnes concernées.");
    }
}
