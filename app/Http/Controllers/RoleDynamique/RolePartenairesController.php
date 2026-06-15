<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Mail\PartenaireInscribedMail;
use App\Models\User;
use App\Models\Projet;
use App\Models\Role;
use App\Rules\UniqueEmailAcrossSystem;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class RolePartenairesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function index(Request $request)
    {
        $query = User::with(['role', 'projets'])
            ->whereHas('role', function ($q) {
                $q->where('nom', 'Partenaire');
            });

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('prenom', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('projet_id')) {
            $query->whereHas('projets', function ($q) use ($request) {
                $q->where('id', (int) $request->projet_id);
            });
        }

        $partenaires = $query->latest()->get();
        $projets = Projet::orderBy('nom')->get();

        return view('role-dynamique.partenaires.index', compact('partenaires', 'projets'));
    }

    public function create(Request $request)
    {
        $count = max(1, min((int) $request->get('count', 1), 10));
        
        $projets = Projet::whereDoesntHave('partenaires')
            ->whereNull('partenaire_id')
            ->orderBy('nom')
            ->get();

        return view('role-dynamique.partenaires.create', compact('projets', 'count'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|array|min:1',
            'nom.*' => 'required|string|max:100',
            'prenom' => 'required|array|min:1',
            'prenom.*' => 'required|string|max:100',
            'email' => 'required|array|min:1',
            'email.*' => ['required', 'email', 'distinct', new UniqueEmailAcrossSystem()],
            'telephone' => 'nullable|array',
            'projet_id' => 'required|exists:projets,id',
        ], [
            'email.*.required' => 'L\'email est requis',
            'email.*.email' => 'L\'email doit être valide',
        ]);

        $projet = Projet::findOrFail($request->projet_id);
        $chefProjet = $projet->admin;
        
        $partenaireRole = Role::where('nom', 'Partenaire')->first();
        
        if (!$partenaireRole) {
            $partenaireRole = Role::create([
                'nom' => 'Partenaire',
                'slug' => 'partenaire',
                'statut' => true,
            ]);
        }

        $createdPartenaires = [];
        $allCreated = [];

        try {
            DB::beginTransaction();

            foreach ($request->nom as $index => $nom) {
                $plainPassword = Str::random(12);
                
                $partenaire = User::create([
                    'name' => $nom,
                    'prenom' => $request->prenom[$index],
                    'email' => $request->email[$index],
                    'telephone' => $request->telephone[$index] ?? null,
                    'password' => Hash::make($plainPassword),
                    'role_id' => $partenaireRole->id,
                    'is_active' => true,
                ]);

                $allCreated[] = $partenaire;

                $createdPartenaires[] = [
                    'name' => $nom . ' ' . $request->prenom[$index],
                    'email' => $request->email[$index],
                    'password' => $plainPassword,
                    'partenaire' => $partenaire,
                ];

                try {
                    Mail::to($partenaire->email)->send(new PartenaireInscribedMail($partenaire, $plainPassword, $chefProjet));
                } catch (\Exception $e) {
                    // Silently fail if email doesn't send
                }
            }

            $projet->partenaires()->sync($allCreated);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('role-dynamique.partenaires.index')
                ->with('error', 'Erreur lors de l\'inscription des partenaires. Veuillez réessayer.');
        }

        $successMessage = count($createdPartenaires) > 1
            ? count($createdPartenaires) . ' partenaires ont été créés avec succès.'
            : 'Le partenaire a été créé avec succès.';

        return redirect()->route('role-dynamique.partenaires.index')
            ->with('success', $successMessage)
            ->with('created_partenaires', $createdPartenaires);
    }

    public function show(User $partenaire)
    {
        $partenaire->load('role');
        
        if (!$partenaire->role || $partenaire->role->nom !== 'Partenaire') {
            abort(404);
        }
        
        $partenaire->load(['projets', 'projet']);
        return view('role-dynamique.partenaires.show', compact('partenaire'));
    }

    public function edit(User $partenaire)
    {
        $partenaire->load('role');

        if (!$partenaire->role || $partenaire->role->nom !== 'Partenaire') {
            abort(404);
        }

        $currentProjetId = Projet::where('partenaire_id', $partenaire->id)->value('id');
        
        $projets = Projet::where(function ($query) use ($currentProjetId) {
                $query->whereNull('partenaire_id')
                    ->orWhere('id', $currentProjetId);
            })
            ->orderBy('nom')
            ->get();

        return view('role-dynamique.partenaires.edit', compact('partenaire', 'projets'));
    }

    public function update(Request $request, User $partenaire)
    {
        $partenaire->load('role');
        
        if (!$partenaire->role || $partenaire->role->nom !== 'Partenaire') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => ['required', 'email', new UniqueEmailAcrossSystem($partenaire->id, 'users')],
            'telephone' => 'nullable|string|max:20',
            'projet_id' => 'required|exists:projets,id',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
        ]);

        $projet = Projet::findOrFail($request->projet_id);

        $partenaire->update([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        Projet::where('partenaire_id', $partenaire->id)->update(['partenaire_id' => null]);
        $projet->update(['partenaire_id' => $partenaire->id]);

        return redirect()->route('role-dynamique.partenaires.index')
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

        return redirect()->route('role-dynamique.partenaires.index')
            ->with('success', "Partenaire supprimé avec succès.");
    }

    public function resetPassword(User $partenaire)
    {
        $partenaire->load('role');
        
        if (!$partenaire->role || $partenaire->role->nom !== 'Partenaire') {
            abort(404);
        }

        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
        $plainPassword = substr(str_shuffle(str_repeat($chars, 4)), 0, 12);

        $partenaire->update([
            'password' => Hash::make($plainPassword),
        ]);

        try {
            Mail::to($partenaire->email)->send(new PasswordResetMail(
                trim(($partenaire->prenom ?? '') . ' ' . ($partenaire->name ?? '')),
                $partenaire->email,
                $plainPassword
            ));
        } catch (\Throwable $e) {
            // Ignore email errors, reset remains successful.
        }

        return redirect()->route('role-dynamique.partenaires.index')
            ->with('success', "Mot de passe réinitialisé pour {$partenaire->prenom} {$partenaire->name}. Nouveau MDP : {$plainPassword}");
    }
}
