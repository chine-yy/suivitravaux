<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Rapport;
use App\Models\Projet;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RoleRapportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-rapports');
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

    private function hasRecipientColumns(): bool
    {
        return \Illuminate\Support\Facades\Schema::hasColumn('rapports', 'destinataire_type')
            && \Illuminate\Support\Facades\Schema::hasColumn('rapports', 'destinataire_id');
    }

    private function hasUserEntrepriseColumn(): bool
    {
        return \Illuminate\Support\Facades\Schema::hasColumn('users', 'entreprise_id');
    }

    private function isRolePersonnaliseHorsPartenaireSuperAdmin($user): bool
    {
        if (!$user) {
            return false;
        }

        return !$user->isSuperAdmin()
            && !$user->isAdminEntreprise()
            && !$user->isPartenaire()
            && $user->role_id !== null;
    }

    private function rapportsRecusEntrepriseAdminQuery($user)
    {
        $query = Rapport::query()->where('auteur_id', '<>', $user->id);

        if (!$this->hasRecipientColumns()) {
            return $query;
        }

        $entrepriseUserIds = [];
        if ($this->hasUserEntrepriseColumn() && $user->entreprise_id !== null) {
            $entrepriseUserIds = \App\Models\User::where('entreprise_id', $user->entreprise_id)->pluck('id')->toArray();
        } else {
            $entrepriseUserIds = \App\Models\User::pluck('id')->toArray();
        }

        return $query->where(function ($q) use ($entrepriseUserIds) {
            $q->where(function ($targeted) use ($entrepriseUserIds) {
                $targeted->where('destinataire_type', 'App\\Models\\User')
                    ->whereIn('destinataire_id', $entrepriseUserIds);
            })
            // Rapports globaux (sans destinataire précis) visibles par tous.
            ->orWhere(function ($global) {
                $global->whereNull('destinataire_type')->whereNull('destinataire_id');
            });
        });
    }

    private function rapportsAccessiblesRolePersonnaliseQuery($user)
    {
        $query = Rapport::query();

        if (!$this->hasRecipientColumns()) {
            return $query->where('auteur_id', $user->id);
        }

        return $query->where(function ($q) use ($user) {
            $q->where('auteur_id', $user->id)
                ->orWhere(function ($targeted) use ($user) {
                    $targeted->where('destinataire_type', 'App\\Models\\User')
                        ->where('destinataire_id', $user->id);
                })
                // Rapports globaux (sans destinataire précis) visibles par tous.
                ->orWhere(function ($global) {
                    $global->whereNull('destinataire_type')->whereNull('destinataire_id');
                });
        });
    }

public function index()
    {
        $utilisateurs = collect();
        $rapportsParStatut = ['en_attente' => 0, 'valide' => 0, 'rejete' => 0, 'non_lu' => 0];
        $rapportsParProjet = [];
        $projets = collect();
        $totalRapports = 0;
        $totalMesRapports = null;
        $totalRapportsRecus = null;
        $totalRapportsLabel = 'Total Rapports';

        $user = auth()->user();
        if ($user && ($user->isSuperAdmin() || $user->isAdminEntreprise())) {
                $mesRapports = null;
                $recusRapports = null;
            if ($user->isSuperAdmin()) {
                $query = Rapport::query();
                if (request('filter') === 'non_lu') {
                    $query->where('statut', 'soumis');
                }
                $rapports = $query->with(['projet', 'auteur.role', 'destinataire'])->latest()->paginate(10);

                $allRapportsQuery = Rapport::query();
                $pendingCount = (clone $allRapportsQuery)->where('statut', 'soumis')->count();
                $rapportsParStatut = [
                    'en_attente' => $pendingCount,
                    'valide' => (clone $allRapportsQuery)->where('statut', 'valide')->count(),
                    'rejete' => (clone $allRapportsQuery)->where('statut', 'rejete')->count(),
                    'non_lu' => $pendingCount,
                ];

                $projets = Projet::all();
                foreach ($projets as $projet) {
                    $rapportsParProjet[$projet->nom] = Rapport::where('projet_id', $projet->id)->count();
                }

                $totalRapports = Rapport::count();
                $utilisateurs = \App\Models\User::all();
            } else {
                if (request('filter') === 'non_lu') {
                    $mesQuery = Rapport::where('auteur_id', $user->id)->where('statut', 'soumis');
                    $recusQuery = $this->rapportsRecusEntrepriseAdminQuery($user)->where('statut', 'soumis');
                } else {
                    $mesQuery = Rapport::where('auteur_id', $user->id);
                    $recusQuery = $this->rapportsRecusEntrepriseAdminQuery($user);
                }

                $mesRapports = $mesQuery
                    ->with(['projet', 'auteur.role', 'destinataire'])
                    ->latest()
                    ->paginate(10, ['*'], 'mes_page');

                $recusRapports = $recusQuery
                    ->with(['projet', 'auteur.role', 'destinataire'])
                    ->latest()
                    ->paginate(10, ['*'], 'recus_page');

                // Keep $rapports for backwards compatibility in the view.
                $rapports = $mesRapports;

                $accessQuery = Rapport::query()->where(function ($q) use ($user) {
                    $q->where('auteur_id', $user->id)
                        ->orWhere(function ($recus) use ($user) {
                            $recus->where('auteur_id', '<>', $user->id);

                            if ($this->hasRecipientColumns()) {
                                $entrepriseUserIds = [];
                                if ($this->hasUserEntrepriseColumn() && $user->entreprise_id !== null) {
                                    $entrepriseUserIds = \App\Models\User::where('entreprise_id', $user->entreprise_id)->pluck('id')->toArray();
                                } else {
                                    $entrepriseUserIds = \App\Models\User::pluck('id')->toArray();
                                }

                                $recus->where(function ($r) use ($entrepriseUserIds) {
                                    $r->where(function ($targeted) use ($entrepriseUserIds) {
                                        $targeted->where('destinataire_type', 'App\\Models\\User')
                                            ->whereIn('destinataire_id', $entrepriseUserIds);
                                    })->orWhere(function ($global) {
                                        $global->whereNull('destinataire_type')
                                            ->whereNull('destinataire_id');
                                    });
                                });
                            }
                        });
                });

                $pendingCount = (clone $accessQuery)->where('statut', 'soumis')->count();
                $rapportsParStatut = [
                    'en_attente' => $pendingCount,
                    'valide' => (clone $accessQuery)->where('statut', 'valide')->count(),
                    'rejete' => (clone $accessQuery)->where('statut', 'rejete')->count(),
                    'non_lu' => $pendingCount,
                ];

                $projets = Projet::all();
                foreach ($projets as $projet) {
                    $rapportsParProjet[$projet->nom] = (clone $accessQuery)->where('projet_id', $projet->id)->count();
                }

                $totalMesRapports = Rapport::where('auteur_id', $user->id)->count();
                $totalRapportsRecus = $this->rapportsRecusEntrepriseAdminQuery($user)->count();
                $totalRapports = $totalMesRapports + $totalRapportsRecus;
                $totalRapportsLabel = 'Total Rapports accessibles';

                $admins = \App\Models\User::entrepriseAdmins()->nonPartenaires()->get();
                $users = \App\Models\User::nonPartenaires()->get();
                $utilisateurs = $admins->concat($users);
            }
        } else {
            $userId = auth()->id();
            $query = Rapport::where('auteur_id', $userId);

            if (request('filter') === 'non_lu') {
                $query->where('statut', 'soumis');
            }

            $rapports = $query->with(['projet', 'auteur.role', 'destinataire'])
                ->latest()
                ->paginate(10);

            $pendingCount = Rapport::where('auteur_id', $userId)->where('statut', 'soumis')->count();
            $rapportsParStatut = [
                'en_attente' => $pendingCount,
                'valide' => Rapport::where('auteur_id', $userId)->where('statut', 'valide')->count(),
                'rejete' => Rapport::where('auteur_id', $userId)->where('statut', 'rejete')->count(),
                'non_lu' => $pendingCount,
            ];

        

            $totalRapports = Rapport::where('auteur_id', $userId)->count();
            $totalRapportsLabel = 'Mes rapports';
        }

        // Déclarer les variables par défaut pour éviter l'erreur compact()
        $mesRapports = $mesRapports ?? collect();
        $recusRapports = $recusRapports ?? collect();
        $utilisateurs = $utilisateurs ?? collect();

        // determine current tab and items for AJAX tab requests
        $currentTab = request('tab', 'mes');
        if (isset($mesRapports) && isset($recusRapports) && $user && $user->isAdminEntreprise() && !$user->isSuperAdmin()) {
            $items = $currentTab === 'recus' ? $recusRapports : $mesRapports;
        } else {
            $items = $rapports ?? $mesRapports ?? $recusRapports ?? collect();
        }

        if (request()->ajax()) {
            $html = view('role-dynamique.rapports._table', compact('items', 'currentTab'))->render();
            return response($html);
        }

        return view('role-dynamique.rapports.index', compact(
            'rapports',
            'mesRapports',
            'recusRapports',
            'utilisateurs',
            'rapportsParStatut',
            'rapportsParProjet',
            'projets',
            'totalRapports',
            'totalMesRapports',
            'totalRapportsRecus',
            'totalRapportsLabel'
        ));
    }

    public function create()
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'isAdminEntreprise') && $user->isAdminEntreprise()) {
            $admin = $user;
            $isFullAdmin = $admin->isAdminEntreprise();
            $userPermissions = $admin->role ? $admin->role->permissions()->pluck('slug')->toArray() : [];

            if (!$isFullAdmin && !in_array('create-rapports', $userPermissions)) {
                abort(403, "Vous n'avez pas la permission de créer des rapports.");
            }
            $projets = Projet::orderBy('nom')->get();
        } elseif ($user && $user->hasPermission('view-projets')) {
            $projets = Projet::orderBy('nom')->get();
        } else {
            $projets = Projet::whereHas('equipes', fn($q) => $q->where('user_id', auth()->id()))->orderBy('nom')->get();
        }

        $admins = \App\Models\User::entrepriseAdmins()->nonPartenaires()->get();
        $users = \App\Models\User::nonPartenaires()->get();
        $utilisateurs = $admins->concat($users);
        return view('role-dynamique.rapports.create', compact('projets', 'utilisateurs'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user && method_exists($user, 'isAdminEntreprise') && $user->isAdminEntreprise()) {
            $admin = $user;
            $isFullAdmin = $admin->isAdminEntreprise();
            $userPermissions = $admin->role ? $admin->role->permissions()->pluck('slug')->toArray() : [];

            if (!$isFullAdmin && !in_array('create-rapports', $userPermissions)) {
                abort(403, "Vous n'avez pas la permission de créer des rapports.");
            }
        }

        $request->validate([
            'projet_id' => 'required|exists:projets,id',
            'titre' => 'required|string|max:255',
            'type' => 'required|in:journalier,hebdomadaire,mensuel,incident,fin_tache,sous_tache',
            'contenu' => 'nullable|string',
            'statut' => 'required|in:soumis,en_revision,valide,rejete,brouillon,en_revue,approuve',
            'destinataire_id' => 'nullable|integer',
            'destinataire_type' => 'nullable|string|in:App\\Models\\Admin,App\\Models\\User',
            'est_envoye' => 'nullable|boolean',
        ]);

        $statutMap = [
            'brouillon' => 'soumis',
            'en_revue' => 'en_revision',
            'approuve' => 'valide',
        ];

        $data = [
            'projet_id' => $request->projet_id,
            'auteur_id' => auth()->id(),
            'type' => $request->type,
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'statut' => $statutMap[$request->statut] ?? $request->statut,
        ];

        if ($request->filled('destinataire_id') && $request->filled('destinataire_type')) {
            $data['destinataire_id'] = $request->destinataire_id;
            $data['destinataire_type'] = $request->destinataire_type;
            $data['est_envoye'] = $request->est_envoye ?? false;
            $data['date_envoi'] = $data['est_envoye'] ? now() : null;
        }

        Rapport::create($data);

        return redirect()->route('role-dynamique.rapports.index')
            ->with('success', 'Rapport créé avec succès.');
    }

    public function show($id)
    {
        $user = auth()->user();
        $rapport = Rapport::with(['projet', 'auteur.role'])->findOrFail($id);

        $isAdmin = $user && ($user->isSuperAdmin() || $user->isAdminEntreprise());
        if (!$isAdmin && $rapport->auteur_id != auth()->id()) {
            abort(403, "Vous n'avez pas la permission de consulter ce rapport.");
        }

        return redirect()->route('role-dynamique.rapports.index')
            ->with('open_modal', 'viewRapportModal' . $id);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $rapport = Rapport::findOrFail($id);

        $isAdmin = $user && ($user->isSuperAdmin() || $user->isAdminEntreprise());
        if (!$isAdmin && $rapport->auteur_id != auth()->id()) {
            abort(403, "Vous n'avez pas la permission de modifier ce rapport.");
        }

        $projets = Projet::orderBy('nom')->get();
        $canOnlyEditStatus = $isAdmin && $rapport->auteur_id != auth()->id();

        return view('role-dynamique.rapports.edit', compact('rapport', 'projets', 'canOnlyEditStatus'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $rapport = Rapport::findOrFail($id);

        $isAdmin = $user && ($user->isSuperAdmin() || $user->isAdminEntreprise());
        if (!$isAdmin && $rapport->auteur_id != auth()->id()) {
            abort(403, "Vous n'avez pas la permission de modifier ce rapport.");
        }

        $statutMap = [
            'brouillon' => 'soumis',
            'en_revue' => 'en_revision',
            'approuve' => 'valide',
        ];

        $isNonOwnerAdmin = $isAdmin && $rapport->auteur_id != auth()->id();

        if ($isNonOwnerAdmin) {
            $request->validate([
                'statut' => 'required|in:soumis,en_revision,valide,rejete,brouillon,en_revue,approuve',
            ]);

            $rapport->update([
                'statut' => $statutMap[$request->statut] ?? $request->statut,
            ]);
        } else {
            $request->validate([
                'projet_id' => 'required|exists:projets,id',
                'titre' => 'required|string|max:255',
                'type' => 'required|in:journalier,hebdomadaire,mensuel,incident,fin_tache,sous_tache',
                'contenu' => 'nullable|string',
                'statut' => 'nullable|in:soumis,en_revision,valide,rejete,brouillon,en_revue,approuve',
            ]);

            $updateData = [
                'projet_id' => $request->projet_id,
                'type' => $request->type,
                'titre' => $request->titre,
                'contenu' => $request->contenu,
            ];

            // Seul l'admin peut modifier le statut
            if ($isAdmin) {
                $updateData['statut'] = $statutMap[$request->statut] ?? $request->statut;
            }

            $rapport->update($updateData);
        }

        return redirect()->route('role-dynamique.rapports.index')
            ->with('success', 'Rapport mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $rapport = Rapport::findOrFail($id);

        $isAdmin = $user && ($user->isSuperAdmin() || $user->isAdminEntreprise());
        if (!$isAdmin && $rapport->auteur_id != auth()->id()) {
            abort(403, "Vous n'avez pas la permission de supprimer ce rapport.");
        }

        $rapport->delete();
        return back()->with('success', 'Rapport supprimé avec succès.');
    }

    /**
     * Envoyer un rapport à un utilisateur.
     */
    public function envoyer(Request $request, $id)
    {
        $user = auth()->user();
        $rapport = Rapport::findOrFail($id);

        $isAdmin = $user && ($user->isSuperAdmin() || $user->isAdminEntreprise());
        if (!$isAdmin && $rapport->auteur_id != auth()->id()) {
            abort(403, "Vous n'avez pas la permission d'envoyer ce rapport.");
        }

        $request->validate([
            'destinataire_id' => 'required|integer',
            'destinataire_type' => 'required|string|in:App\\Models\\Admin,App\\Models\\User',
        ]);

        $rapport->update([
            'destinataire_id' => $request->destinataire_id,
            'destinataire_type' => $request->destinataire_type,
            'est_envoye' => true,
            'date_envoi' => now(),
        ]);

        return back()->with('success', 'Rapport envoyé avec succès.');
    }

    /**
     * Voir le PDF du rapport.
     */
    public function voirPdf($id)
    {
        $user = auth()->user();
        $rapport = Rapport::with(['projet', 'auteur.role'])->findOrFail($id);

        $isAdmin = $user && ($user->isSuperAdmin() || $user->isAdminEntreprise());
        if (!$isAdmin && $rapport->auteur_id != auth()->id()) {
             abort(403, "Vous n'avez pas la permission de consulter ce rapport.");
        }

        $pdf = Pdf::loadView('partials.pdf-rapport', compact('rapport'));
        return $pdf->stream();
    }

    /**
     * Télécharger le PDF du rapport.
     */
    public function telechargerPdf($id)
    {
        $user = auth()->user();
        $rapport = Rapport::with(['projet', 'auteur.role'])->findOrFail($id);

        $isAdmin = $user && ($user->isSuperAdmin() || $user->isAdminEntreprise());
        if (!$isAdmin && $rapport->auteur_id != auth()->id()) {
             abort(403, "Vous n'avez pas la permission de télécharger ce rapport.");
        }

        $pdf = Pdf::loadView('partials.pdf-rapport', compact('rapport'));
        $filename = 'rapport_' . str_replace(' ', '_', $rapport->projet->nom ?? 'projet') . '_' . Carbon::parse($rapport->created_at)->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Envoyer un rapport au(x) partenaire(s) du projet.
     */
    public function envoyerPartenaire(Request $request, $id)
    {
        $user = auth()->user();
        $rapport = Rapport::with('projet')->findOrFail($id);

        // Autorise : l'auteur du rapport OU un admin/super admin
        $isAdmin = $user && ($user->isSuperAdmin() || $user->isAdminEntreprise());
        $isAuteur = $rapport->auteur_id == auth()->id();

        if (!$isAdmin && !$isAuteur) {
            abort(403, "Vous n'avez pas la permission d'envoyer ce rapport.");
        }

        if (!$rapport->projet) {
            return redirect()->back()->with('error', 'Aucun projet trouvé pour ce rapport.');
        }

        $projet = $rapport->projet;
        $partenaireIds = [];

        if ($projet->partenaire_id) {
            $partenaireIds[] = $projet->partenaire_id;
        }

        $additionalPartenaires = $projet->partenaires()->pluck('users.id')->toArray();
        $partenaireIds = array_unique(array_merge($partenaireIds, $additionalPartenaires));

        if (empty($partenaireIds)) {
            return redirect()->back()->with('error', 'Aucun partenaire trouvé pour ce projet.');
        }

        $rapport->update([
            'est_envoye' => true,
            'date_envoi' => now(),
            'envoye_par_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Le rapport a été envoyé à ' . count($partenaireIds) . ' partenaire(s) avec succès.');
    }
}
