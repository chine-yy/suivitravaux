<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use App\Models\Rapport;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Exporter une liste générique en PDF
     */
    public function exportPdf(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'headers' => 'required|array',
            'rows' => 'required|array',
            'filename' => 'required|string',
            'include_roles' => 'sometimes|boolean',
            'include_project_info' => 'sometimes|boolean',
        ]);

        $viewData = [
            'title' => $data['title'],
            'headers' => $data['headers'],
            'rows' => $data['rows'],
        ];

        if (!empty($data['include_roles'])) {
            // Load roles with permissions and users count (exclude Administration role)
            $rolesRaw = \App\Models\Role::with('permissions')->withCount('users')->where('nom', '!=', 'Administration')->get();

            // Group by normalized name similar to historique controller
            $roles = $rolesRaw->groupBy(fn ($role) => mb_strtolower(preg_replace('/\s+/u', ' ', trim((string) $role->nom)) ?? ''))
                ->map(function ($group) {
                    $primary = $group->first();
                    $primary->setRelation('permissions', $group->flatMap(fn ($r) => $r->permissions)->unique('id')->values());
                    $primary->users_count = (int) $group->sum(fn ($r) => (int) ($r->users_count ?? 0));

                    // Group permissions by normalized module for cleaner PDF rendering
                    $groupedPermissions = \App\Models\Permission::getGroupedPermissions($primary->permissions);
                    $modules = collect($groupedPermissions)
                        ->flatMap(function ($moduleGroups) {
                            return collect($moduleGroups)->map(function ($moduleData) {
                                return [
                                    'module' => $moduleData['nom'] ?? 'Module',
                                    'permissions' => collect($moduleData['permissions'] ?? [])
                                        ->map(fn ($perm) => $perm->nom ?? '')
                                        ->filter()
                                        ->values(),
                                ];
                            });
                        })
                        ->filter(fn ($m) => !empty($m['permissions']) && $m['permissions']->count() > 0)
                        ->values();

                    $primary->setAttribute('grouped_modules', $modules);

                    return $primary;
                })->values();

            $viewData['roles'] = $roles;
            $viewData['roles_only_export'] = true;
        }

        // If requested, try to include project details when filename follows 'projet_{id}'
        if (!empty($data['include_project_info'])) {
            try {
                if (preg_match('/^projet_(\d+)$/i', $data['filename'], $m)) {
                    $projId = (int) $m[1];
                    $project = \App\Models\Projet::with(['admin', 'entreprise'])->find($projId);
                    if ($project) {
                        // load partenaires associated to this project (one or many)
                        $partenaires = \App\Models\Partenaire::where('projet_id', $project->id)->get();
                        $viewData['project'] = $project;
                        $viewData['partenaires'] = $partenaires;
                    }
                }
            } catch (\Throwable $e) {
                // ignore project loading errors, log for debugging
                \Log::warning('Failed to load project for PDF export: ' . $e->getMessage());
            }
        }

        $pdf = PDF::loadView('partials.pdf-table', $viewData);

        return $pdf->download($data['filename'] . '.pdf');
    }

    /**
     * Voir une liste générique en PDF
     */
    public function voirPdf(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'headers' => 'required|array',
            'rows' => 'required|array',
        ]);

        $pdf = PDF::loadView('partials.pdf-table', [
            'title' => $data['title'],
            'headers' => $data['headers'],
            'rows' => $data['rows'],
        ]);

        return $pdf->stream();
    }

    /**
     * Direct export helper for simple GET links like /export/pdf/{type}/{id}
     */
    public function directExportPdf($type, $id)
    {
        if ($type === 'rapport') {
            $user = auth()->user();
            $rapport = Rapport::with(['projet', 'auteur.role', 'auteur.entreprise', 'sousTache'])->findOrFail($id);

            $isAdmin = $user && ($user->isSuperAdmin() || $user->isAdminEntreprise());
            if (!$isAdmin && $rapport->auteur_id != auth()->id()) {
                abort(403, "Vous n'avez pas la permission de télécharger ce rapport.");
            }

            $pdf = PDF::loadView('partials.pdf-rapport', compact('rapport'));
            $filename = 'rapport_' . str_replace(' ', '_', $rapport->projet->nom ?? 'projet') . '_' . Carbon::parse($rapport->created_at)->format('Y-m-d') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'contrat') {
            $contrat = \App\Models\Contrat::with(['partenaire', 'projet'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-contrat', compact('contrat'));
            $filename = 'contrat_' . str_replace(' ', '_', $contrat->numero_contrat ?? 'contrat') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'projet') {
            $projet = \App\Models\Projet::with(['phases', 'taches', 'admin', 'entreprise'])->findOrFail($id);
            
            $currentYear = date('Y');
            $budget = \App\Models\Budget::where('annee', $currentYear)->first();
            $bp = $budget ? \App\Models\BudgetProjet::where('budget_id', $budget->id)->where('projet_id', $projet->id)->first() : null;
            
            $budgetTotal = $bp ? (float) $bp->montant_alloue : (float) ($projet->budget ?: 0);
            $budgetConsomme = $projet->getDynamicConsomme($budget ? $budget->id : null);
            $budgetRestant = max(0, $budgetTotal - $budgetConsomme);
            $budgetPourcentage = $budgetTotal > 0 ? round(($budgetConsomme / $budgetTotal) * 100, 2) : 0;
            
            $tachesStats = [
                'en_attente' => $projet->taches->where('statut', 'en_attente')->count(),
                'en_cours' => $projet->taches->where('statut', 'en_cours')->count(),
                'terminee' => $projet->taches->where('statut', 'terminee')->count(),
            ];
            $sousTachesStats = ['en_cours' => 0, 'terminee' => 0];
            if (method_exists($projet, 'sousTaches')) {
                $sousTachesStats = [
                    'en_cours' => $projet->sousTaches()->where('statut', 'en_cours')->count(),
                    'terminee' => $projet->sousTaches()->where('statut', 'terminee')->count(),
                ];
            }
            $partenaires = \App\Models\Partenaire::where('projet_id', $projet->id)->get();
            $pdf = PDF::loadView('partials.pdf-project', compact('projet', 'budgetTotal', 'budgetConsomme', 'budgetRestant', 'budgetPourcentage', 'tachesStats', 'sousTachesStats', 'partenaires'));
            $filename = 'projet_' . str_replace(' ', '_', $projet->nom ?? 'projet') . '.pdf';
            return $pdf->download($filename);
        }

if ($type === 'facture') {
            $facture = \App\Models\Facture::with(['partenaire', 'projet', 'createur'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-facture', compact('facture'));
            $filename = 'facture_' . str_replace(' ', '_', $facture->numero_facture ?? 'facture') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'satisfaction') {
            $satisfaction = \App\Models\Satisfaction::with(['partenaire', 'projet'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-satisfaction', compact('satisfaction'));
            $filename = 'satisfaction_' . ($satisfaction->id ?? 'enquete') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'intervention') {
            $intervention = \App\Models\Intervention::with(['partenaire', 'projet', 'technicien'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-intervention', compact('intervention'));
            $filename = 'intervention_' . ($intervention->numero ?? $intervention->id) . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'rendezvous') {
            $rendezvous = \App\Models\Rendezvous::with(['projet'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-rendezvous', compact('rendezvous'));
            $filename = 'rendezvous_' . $rendezvous->id . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'stock_list' || $type === 'stock_single') {
            $id = (int) $id;
            if ($id > 0) {
                $stock = \App\Models\Stock::with('fournisseur')->findOrFail($id);
                $pdf = PDF::loadView('partials.pdf-stock-single', compact('stock'));
                $filename = 'stock_' . str_replace(' ', '_', $stock->nom ?? ('id_' . $stock->id)) . '.pdf';
            } else {
                $stocks = \App\Models\Stock::with('fournisseur')->latest()->get();
                $pdf = PDF::loadView('partials.pdf-stocks', compact('stocks'));
                $filename = 'export_stocks_' . date('Y-m-d') . '.pdf';
            }

            return $pdf->download($filename);
        }

        if ($type === 'fournisseur_list') {
            $id = (int) $id;
            if ($id > 0) {
                $fournisseur = \App\Models\Fournisseur::withCount('stocks')->findOrFail($id);
                $pdf = PDF::loadView('partials.pdf-fournisseur-single', compact('fournisseur'));
                $filename = 'fournisseur_' . str_replace(' ', '_', $fournisseur->nom ?? ('id_' . $fournisseur->id)) . '.pdf';
            } else {
                $fournisseurs = \App\Models\Fournisseur::latest()->get();
                $pdf = PDF::loadView('partials.pdf-fournisseurs', compact('fournisseurs'));
                $filename = 'export_fournisseurs_' . date('Y-m-d') . '.pdf';
            }

            return $pdf->download($filename);
        }

        if ($type === 'soustraitance' || $type === 'soustraitance' || $type === 'sous_traitance' || $type === 'sous-traitance') {
            $sousTraitance = \App\Models\SousTraitance::with(['projet'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-sous-traitance', compact('sousTraitance'));
            $filename = 'sous_traitance_' . str_replace(' ', '_', $sousTraitance->nom_entreprise ?? 'sous-traitance') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'depense') {
            $depense = \App\Models\Depense::with(['projet'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-depense', compact('depense'));
            $filename = 'depense_' . $depense->id . '_' . date('Y-m-d') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'phase') {
            $phase = \App\Models\Phase::with(['projet', 'taches', 'projet.sousTraitances'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-phase', compact('phase'));
            $filename = 'phase_' . str_replace(' ', '_', $phase->nom ?? 'phase') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'tache') {
            $tache = \App\Models\Tache::with(['projet.partenaire', 'phase', 'sousTaches.user.role', 'sousTaches.personnels.role', 'personnels'])->findOrFail($id);
            $personnels = $tache->personnels;
            $sousTachePersonnels = $tache->sousTaches->map(fn($st) => $st->user)->filter()->unique('id');
            $pdf = PDF::loadView('partials.pdf-tache', compact('tache', 'personnels', 'sousTachePersonnels'));
            $filename = 'tache_' . str_replace(' ', '_', $tache->titre ?? 'tache') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'incident') {
            $incident = \App\Models\Incident::with(['projet', 'signalePar.role'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-incident', compact('incident'));
            $filename = 'incident_' . $incident->id . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'document') {
            $document = \App\Models\Document::with(['projet', 'user'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-document', compact('document'));
            $filename = 'document_' . str_replace(' ', '_', $document->nom ?? 'document') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'equipe') {
            $equipe = \App\Models\Equipe::with(['projet', 'users', 'role', 'chef'])->findOrFail($id);
            $pdf = PDF::loadView('partials.pdf-equipe', compact('equipe'));
            $filename = 'equipe_' . str_replace(' ', '_', $equipe->nom ?? 'equipe') . '.pdf';
            return $pdf->download($filename);
        }

        if ($type === 'sous-tache' || $type === 'soustache') {
            $sousTache = \App\Models\SousTache::with(['tache.projet', 'tache.phase', 'personnels.role'])->findOrFail($id);
            $personnels = $sousTache->personnels;
            $pdf = PDF::loadView('partials.pdf-sous-tache', compact('sousTache', 'personnels'));
            $filename = 'sous_tache_' . str_replace(' ', '_', $sousTache->titre ?? 'sous-tache') . '.pdf';
            return $pdf->download($filename);
        }

        abort(404);
    }

    /**
     * Exporter la liste des utilisateurs en PDF
     */
    public function exportUsersPdf()
    {
        $users = \App\Models\User::with(['role', 'entreprise'])
            ->whereHas('role', function ($query) {
                $query->whereNotIn('nom', ['Super Admin', 'Administrateur Entreprise']);
            })
            ->get();

        $pdf = PDF::loadView('partials.pdf-users', compact('users'));

        return $pdf->download('utilisateurs_' . date('Y-m-d') . '.pdf');
    }
}
