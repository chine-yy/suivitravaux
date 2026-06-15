<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\User;
use App\Models\Role;
use App\Rules\UniqueEmailAcrossSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EntrepriseController extends Controller
{
    /**
     * Display a listing of the entreprises.
     */
    public function index()
    {
        $entreprises = Entreprise::with('administrateurs')->paginate(10);
        return view('entreprises.index', compact('entreprises'));
    }

    /**
     * Show the form for creating a new entreprise.
     */
    public function create()
    {
        return view('entreprises.create');
    }

    /**
     * Store a newly created entreprise and its administrator.
     * Uses transaction to ensure both records are created together.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Entreprise fields
            'nom_entreprise' => ['required', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:500'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'site_web' => ['nullable', 'url', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
            'pays' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],

            // Administrateur fields
            'administrateur_nom' => ['required', 'string', 'max:255'],
            'administrateur_prenom' => ['required', 'string', 'max:255'],
            'administrateur_email' => ['required', 'email', 'max:255', new UniqueEmailAcrossSystem()],
            'administrateur_telephone' => ['nullable', 'string', 'max:20'],
            'administrateur_poste' => ['nullable', 'string', 'max:100'],
        ], [
            'administrateur_email.required' => 'L\'email de l\'administrateur est requis',
            'administrateur_email.email' => 'L\'email de l\'administrateur doit être valide',
        ]);

        try {
            $result = DB::transaction(function () use ($validated) {
                // Create the entreprise
                $entreprise = Entreprise::create([
                    'id_entreprise' => Entreprise::generateEntrepriseId(),
                    'nom_entreprise' => $validated['nom_entreprise'],
                    'adresse' => $validated['adresse'] ?? null,
                    'telephone' => $validated['telephone'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'site_web' => $validated['site_web'] ?? null,
                    'ville' => $validated['ville'] ?? null,
                    'pays' => $validated['pays'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'statut' => true,
                ]);

                // Create the administrator linked to the entreprise
                // Create an admin role for this entreprise without default permissions
                $role = Role::create([
                    'nom' => 'Administrateur Entreprise',
                    'slug' => Str::slug('Administrateur Entreprise-' . $entreprise->id),
                    'statut' => true,
                ]);

                 $administrateur = User::create([
                     'entreprise_id' => $entreprise->id,
                     'role_id' => $role->id,
                     'name' => $validated['administrateur_nom'],
                     'prenom' => $validated['administrateur_prenom'],
                     'email' => $validated['administrateur_email'],
                     'telephone' => $validated['administrateur_telephone'] ?? null,
                     'is_active' => true,
                     'password' => Hash::make(Str::random(32)), // temporary password
                 ]);

                return ['entreprise' => $entreprise, 'administrateur' => $administrateur];
            });

            return redirect()->route('entreprises.index')
                ->with('success', 'Entreprise et administrateur créés avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de l\'entreprise: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la création.')->withInput();
        }
    }

    /**
     * Display the specified entreprise.
     */
    public function show(Entreprise $entreprise)
    {
        $entreprise->load('administrateurs');
        return view('entreprises.show', compact('entreprise'));
    }

    /**
     * Show the form for editing the specified entreprise.
     */
    public function edit(Entreprise $entreprise)
    {
        $entreprise->load('administrateurs');
        return view('entreprises.edit', compact('entreprise'));
    }

    /**
     * Update the specified entreprise and its administrator.
     */
    public function update(Request $request, Entreprise $entreprise)
    {
        $validated = $request->validate([
            // Entreprise fields
            'nom_entreprise' => ['required', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:500'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'site_web' => ['nullable', 'url', 'max:255'],
            'ville' => ['nullable', 'string', 'max:100'],
            'pays' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'statut' => ['boolean'],

            // Administrateur fields (optional for update)
            'administrateur_id' => ['nullable', 'exists:administrateurs_entreprises,id'],
            'administrateur_nom' => ['nullable', 'string', 'max:255'],
            'administrateur_prenom' => ['nullable', 'string', 'max:255'],
            'administrateur_email' => ['nullable', 'email', 'max:255', new UniqueEmailAcrossSystem($request->administrateur_id, 'users')],
            'administrateur_telephone' => ['nullable', 'string', 'max:20'],
            'administrateur_poste' => ['nullable', 'string', 'max:100'],
        ], [
            'administrateur_email.email' => 'L\'email de l\'administrateur doit être valide',
        ]);

        try {
            DB::transaction(function () use ($validated, $entreprise) {
                // Update the entreprise
                $entreprise->update([
                    'nom_entreprise' => $validated['nom_entreprise'],
                    'adresse' => $validated['adresse'] ?? null,
                    'telephone' => $validated['telephone'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'site_web' => $validated['site_web'] ?? null,
                    'ville' => $validated['ville'] ?? null,
                    'pays' => $validated['pays'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'statut' => $validated['statut'] ?? true,
                ]);

                 // Update administrator if provided
                 if (!empty($validated['administrateur_id'])) {
                     $administrateur = User::entrepriseAdmins()
                         ->where('entreprise_id', $entreprise->id)
                         ->where('id', $validated['administrateur_id'])
                         ->first();
                     if ($administrateur) {
                         $administrateur->update([
                             'name' => $validated['administrateur_nom'] ?? $administrateur->name,
                             'prenom' => $validated['administrateur_prenom'] ?? $administrateur->prenom,
                             'email' => $validated['administrateur_email'] ?? $administrateur->email,
                             'telephone' => $validated['administrateur_telephone'] ?? $administrateur->telephone,
                         ]);
                     }
                 }
            });

            return redirect()->route('entreprises.show', $entreprise->id)
                ->with('success', 'Entreprise mise à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'entreprise: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour.')->withInput();
        }
    }

    /**
     * Remove the specified entreprise and its administrators.
     */
    public function destroy(Entreprise $entreprise)
    {
        try {
            DB::transaction(function () use ($entreprise) {
                // Delete administrators first
                User::entrepriseAdmins()
                    ->where('entreprise_id', $entreprise->id)
                    ->delete();
                // Delete the entreprise
                $entreprise->delete();
            });

            return redirect()->route('entreprises.index')
                ->with('success', 'Entreprise supprimée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'entreprise: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Add a new administrator to an existing entreprise.
     */
    public function addAdministrateur(Request $request, Entreprise $entreprise)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', new UniqueEmailAcrossSystem()],
            'telephone' => ['nullable', 'string', 'max:20'],
            'poste' => ['nullable', 'string', 'max:100'],
        ]);

        try {
            // Find or create admin role for this entreprise
            $role = Role::firstOrCreate(
                ['entreprise_id' => $entreprise->id, 'nom' => 'Administrateur Entreprise'],
                [
                    'slug' => Str::slug('Administrateur Entreprise-' . $entreprise->id),
                    'statut' => true,
                ]
            );
            // Keep the role without default permissions.

            $administrateur = User::create([
                'entreprise_id' => $entreprise->id,
                'role_id' => $role->id,
                'name' => $validated['nom'],
                'prenom' => $validated['prenom'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'] ?? null,
                'is_active' => true,
                'password' => Hash::make(Str::random(32)), // temporary password
            ]);

            return back()->with('success', 'Administrateur ajouté avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout de l\'administrateur: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout de l\'administrateur.');
        }
    }
}
