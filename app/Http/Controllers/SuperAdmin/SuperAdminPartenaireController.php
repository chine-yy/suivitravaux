<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use App\Models\User;
use App\Models\Role;
use App\Rules\UniqueEmailAcrossSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\PartenaireInscribedMail;
use App\Mail\PasswordResetMail;

class SuperAdminPartenaireController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role'])
            ->whereHas('role', function ($q) {
                $q->where('nom', 'Partenaire');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $partenaires = $query->latest()->get();
        $projets = Projet::all();

        return view('super-admin.partenaires.index', compact('partenaires', 'projets'));
    }

    public function show(User $partenaire)
    {
        $partenaire->load('role');
        
        if (!$partenaire->role || $partenaire->role->nom !== 'Partenaire') {
            abort(404);
        }
        
        return view('super-admin.partenaires.show', compact('partenaire'));
    }

    public function create(Request $request)
    {
        $count = max(1, min((int) $request->get('count', 1), 10));
        
        $projets = Projet::whereDoesntHave('partenaires')
            ->whereNull('partenaire_id')
            ->orderBy('nom')
            ->get();
            
        return view('super-admin.partenaires.create', compact('projets', 'count'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|array',
            'nom.*'     => 'required|string|max:100',
            'prenom'    => 'required|array',
            'prenom.*'  => 'required|string|max:100',
            'email'     => 'required|array',
            'email.*'   => ['required', 'email', 'distinct', new UniqueEmailAcrossSystem()],
            'telephone' => 'nullable|array',
            'projet_id' => 'required|exists:projets,id',
        ], [
            'email.*.required' => 'L\'email est requis',
            'email.*.email' => 'L\'email doit être valide',
            'email.*.distinct' => 'Chaque partenaire doit avoir un email différent.',
        ]);

        $projet = Projet::findOrFail($request->projet_id);
        $chefProjet = null;
        
        $partenaireRole = Role::where('nom', 'Partenaire')->first();
        
        if (!$partenaireRole) {
            $partenaireRole = Role::create([
                'nom' => 'Partenaire',
                'slug' => 'partenaire',
                'statut' => true,
            ]);
        }

        $entrepriseId = $projet->entreprise_id ?? 1;
        $createdPartenaires = [];
        $allCreated = [];

        try {
            DB::beginTransaction();

            foreach ($request->nom as $index => $nom) {
                $plainPassword = Str::random(12);
                $email = strtolower(trim((string) ($request->email[$index] ?? '')));

                $partenaire = User::create([
                    'name'       => trim((string) $nom),
                    'prenom'    => trim((string) ($request->prenom[$index] ?? '')),
                    'email'     => $email,
                    'telephone' => $request->telephone[$index] ?? null,
                    'password'  => Hash::make($plainPassword),
                    'role_id'   => $partenaireRole->id,
                    'entreprise_id' => $entrepriseId,
                    'is_active' => true,
                    'projet_id' => $projet->id,
                ]);

                $allCreated[] = $partenaire;

                $createdPartenaires[] = [
                    'name' => "{$partenaire->name} {$partenaire->prenom}",
                    'password' => $plainPassword
                ];

                try {
                    Mail::to($partenaire->email)->send(new PartenaireInscribedMail([
                        'nom'                => $partenaire->name,
                        'prenom'             => $partenaire->prenom,
                        'email'              => $partenaire->email,
                        'projet_nom'         => $projet->nom,
                        'mot_de_passe'       => $plainPassword,
                        'chef_equipe_nom'    => '',
                        'chef_equipe_prenom' => '',
                        'chef_projet_nom'    => '',
                        'chef_projet_prenom' => '',
                    ]));
                } catch (\Exception $e) {
                    // Ignore email errors
                }
            }

            $projet->partenaires()->sync($allCreated);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('super-admin.partenaires.index')
                ->with('error', 'Erreur lors de l\'inscription des partenaires. Veuillez réessayer.');
        }

        $successMessage = count($createdPartenaires) > 1
            ? count($createdPartenaires) . " partenaires inscrits et liés au projet {$projet->nom}."
            : "Partenaire inscrit et lié au projet {$projet->nom}.";

        return redirect()->route('super-admin.partenaires.index')
            ->with('success', $successMessage)
            ->with('created_partenaires', $createdPartenaires);
    }

    public function edit(User $partenaire)
    {
        $partenaire->load('role');
        
        if (!$partenaire->role || $partenaire->role->nom !== 'Partenaire') {
            abort(404);
        }
        
        // Find the currently linked project
        $currentProjet = Projet::where('partenaire_id', $partenaire->id)
            ->orWhereHas('partenaires', fn($q) => $q->where('user_id', $partenaire->id))
            ->first();

        $partenaire->projet_id = $currentProjet?->id;

        $projets = Projet::whereNull('partenaire_id')
                        ->orWhere('partenaire_id', $partenaire->id)
                        ->get();
        return view('super-admin.partenaires.edit', compact('partenaire', 'projets'));
    }

    public function update(Request $request, User $partenaire)
    {
        $partenaire->load('role');
        
        if (!$partenaire->role || $partenaire->role->nom !== 'Partenaire') {
            abort(404);
        }
 
        $request->validate([
            'name'       => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => ['required', 'email', new UniqueEmailAcrossSystem($partenaire->id, 'users')],
            'telephone' => 'nullable|string|max:20',
            'projet_id' => 'required|exists:projets,id',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
        ]);
 
        $projet = Projet::findOrFail($request->projet_id);
 
        $partenaire->update([
            'name'       => $request->name,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'projet_id' => $request->projet_id,
        ]);
 
        // Unlink from previous projects (if any) and link to the new one
        Projet::where('partenaire_id', $partenaire->id)->update(['partenaire_id' => null]);
        $projet->partenaires()->detach($partenaire->id); // Also detach from Many-to-Many
        
        $projet->update(['partenaire_id' => $partenaire->id]);
 
        return redirect()->route('super-admin.partenaires.index')
            ->with('success', "Partenaire mis à jour avec succès.");
    }

    public function destroy(User $partenaire)
    {
        $partenaire->load('role');
        
        if (!$partenaire->role || $partenaire->role->nom !== 'Partenaire') {
            abort(404);
        }
 
        Projet::where('partenaire_id', $partenaire->id)->update(['partenaire_id' => null]);
        
        $partenaire->update([
            'role_id' => null,
            'is_active' => false,
        ]);
 
        return redirect()->route('super-admin.partenaires.index')
            ->with('success', "Partenaire supprimé avec succès.");
    }

    public function resetPassword(User $partenaire)
    {
        $partenaire->load('role');
        
        if (!$partenaire->role || $partenaire->role->nom !== 'Partenaire') {
            abort(404);
        }
 
        $plainPassword = Str::random(12);
        $partenaire->update(['password' => Hash::make($plainPassword)]);
 
        try {
            Mail::to($partenaire->email)->send(new PasswordResetMail(
                trim(($partenaire->prenom ?? '') . ' ' . ($partenaire->name ?? '')),
                $partenaire->email,
                $plainPassword
            ));
        } catch (\Throwable $e) {
            // Ignore email errors
        }
 
        return redirect()->route('super-admin.partenaires.index')
            ->with('success', "Mot de passe réinitialisé pour {$partenaire->prenom} {$partenaire->name}. Nouveau MDP : {$plainPassword}");
    }
}