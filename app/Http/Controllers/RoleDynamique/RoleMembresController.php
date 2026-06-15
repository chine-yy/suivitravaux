<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Mail\PartenaireInscribedMail;
use App\Mail\UserCreatedMail;
use App\Models\Partenaire;
use App\Models\Permission;
use App\Models\Projet;
use App\Models\Role;
use App\Models\User;
use App\Rules\UniqueEmailAcrossSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RoleMembresController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-utilisateurs');
        $this->middleware('permission:reset-password-utilisateurs')->only(['usersResetPassword']);
    }

    public function index(Request $request)
    {
        if ($request->routeIs('role-dynamique.partenaires.*')) {
            return $this->partenairesIndex($request);
        }

        return $this->usersIndex($request);
    }

    public function partenairesIndex(Request $request)
    {
        $query = Partenaire::with('projet');

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', '%' . $search . '%')
                    ->orWhere('prenom', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $partenaires = $query->paginate(10)->appends($request->query());

        return view('role-dynamique.partenaires.index', compact('partenaires'));
    }

    public function partenairesCreate(Request $request)
    {
        $count = max(1, min((int) $request->get('count', 1), 10));
        $projets = Projet::whereNull('partenaire_id')->orderBy('nom')->get();

        return view('role-dynamique.partenaires.create', compact('projets', 'count'));
    }

    public function partenairesStore(Request $request)
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
        $createdPartenaires = [];
        $firstPartenaire = null;

        foreach ($request->nom as $index => $nom) {
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
            $plainPassword = substr(str_shuffle(str_repeat($chars, 4)), 0, 10);

            $partenaire = Partenaire::create([
                'projet_id' => $projet->id,
                'nom' => $nom,
                'prenom' => $request->prenom[$index],
                'email' => $request->email[$index],
                'telephone' => $request->telephone[$index] ?? null,
                'password' => $plainPassword,
            ]);

            if (!$firstPartenaire) {
                $firstPartenaire = $partenaire;
            }

            $createdPartenaires[] = [
                'name' => trim($partenaire->nom . ' ' . $partenaire->prenom),
                'password' => $plainPassword,
            ];

            try {
                Mail::to($partenaire->email)->send(new PartenaireInscribedMail([
                    'nom' => $partenaire->nom,
                    'prenom' => $partenaire->prenom,
                    'email' => $partenaire->email,
                    'projet_nom' => $projet->nom,
                    'mot_de_passe' => $plainPassword,
                    'chef_equipe_nom' => '',
                    'chef_equipe_prenom' => '',
                    'chef_projet_nom' => $chefProjet->name ?? '',
                    'chef_projet_prenom' => $chefProjet->prenom ?? '',
                ]));
            } catch (\Throwable $e) {
                // Ignore email errors, account creation remains successful.
            }
        }

        if ($firstPartenaire && !$projet->partenaire_id) {
            $projet->update(['partenaire_id' => $firstPartenaire->id]);
        }

        $successMessage = count($createdPartenaires) > 1
            ? count($createdPartenaires) . " partenaires inscrits et liés au projet {$projet->nom}."
            : "Partenaire inscrit et lié au projet {$projet->nom}.";

        return redirect()->route('role-dynamique.partenaires.index')
            ->with('success', $successMessage)
            ->with('created_partenaires', $createdPartenaires);
    }

    public function partenairesShow(Partenaire $partenaire)
    {
        $partenaire->load('projet');

        return view('role-dynamique.partenaires.show', compact('partenaire'));
    }

    public function partenairesEdit(Partenaire $partenaire)
    {
        $projets = Projet::where(function ($query) use ($partenaire) {
            $query->whereNull('partenaire_id')
                ->orWhere('id', $partenaire->projet_id);
        })->orderBy('nom')->get();

        return view('role-dynamique.partenaires.edit', compact('partenaire', 'projets'));
    }

    public function partenairesUpdate(Request $request, Partenaire $partenaire)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => ['required', 'email', new UniqueEmailAcrossSystem($partenaire->id, 'partenaires')],
            'telephone' => 'nullable|string|max:20',
            'projet_id' => 'required|exists:projets,id',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
        ]);

        $projet = Projet::findOrFail($request->projet_id);

        $partenaire->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'projet_id' => $projet->id,
        ]);

        return redirect()->route('role-dynamique.partenaires.index')
            ->with('success', "Partenaire mis à jour avec succès.");
    }

    public function partenairesDestroy(Partenaire $partenaire)
    {
        Projet::where('partenaire_id', $partenaire->id)->update(['partenaire_id' => null]);
        $partenaire->delete();

        return redirect()->route('role-dynamique.partenaires.index')
            ->with('success', "Partenaire supprimé avec succès.");
    }

    public function usersIndex(Request $request)
    {
        $query = User::with('role')
            ->nonEntrepriseAdmins()
            ->whereDoesntHave('role', function ($q) {
                $q->whereIn('nom', [User::ROLE_SUPER_ADMIN, 'Partenaire']);
            });

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('prenom', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', (int) $request->role_id);
        }

        $users = $query->latest()->get();
        $roles = Role::with('permissions')
            ->whereNotIn('nom', ['Administration', 'Administrateur Entreprise', User::ROLE_SUPER_ADMIN, 'Partenaire'])
            ->orderBy('nom')
            ->get();

        $totalUsers = $users->count();
        $usersActifs = $users->where('is_active', true)->count();
        $usersInactifs = $users->where('is_active', false)->count();
        $rolesCount = $roles->count();

        $usersParRole = $roles->map(function ($role) {
            return [
                'nom' => $role->nom,
                'count' => User::nonEntrepriseAdmins()
                    ->whereDoesntHave('role', function ($q) {
                        $q->whereIn('nom', [User::ROLE_SUPER_ADMIN, 'Partenaire']);
                    })
                    ->where('role_id', $role->id)
                    ->count(),
            ];
        })->filter(fn($row) => $row['count'] > 0)->values();

        return view('role-dynamique.users.index', compact(
            'users',
            'roles',
            'totalUsers',
            'usersActifs',
            'usersInactifs',
            'rolesCount',
            'usersParRole'
        ));
    }

    public function usersCreate()
    {
        $roles = Role::with('permissions')
            ->whereNotIn('nom', ['Administration', 'Administrateur Entreprise', User::ROLE_SUPER_ADMIN, 'Partenaire'])
            ->orderBy('nom')
            ->get();

        $permissions = Permission::all();

        return view('role-dynamique.users.create', compact('roles', 'permissions'));
    }

    public function usersStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => ['required', 'email', new UniqueEmailAcrossSystem()],
            'telephone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
        ]);

        $role = Role::whereNotIn('nom', ['Administration', 'Administrateur Entreprise', User::ROLE_SUPER_ADMIN, 'Partenaire'])
            ->findOrFail($request->role_id);

        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
        $plainPassword = substr(str_shuffle(str_repeat($chars, 4)),0, 12);

        $user = User::create([
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($plainPassword),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        try {
            Mail::to($user->email)->send(new UserCreatedMail(
                trim($user->name . ' ' . $user->prenom),
                $user->email,
                $plainPassword
            ));
        } catch (\Throwable $e) {
            // Ignore email errors, account creation remains successful.
        }

        return redirect()->route('role-dynamique.users.index')
            ->with('success', "Utilisateur \"{$user->name} {$user->prenom}\" créé avec succès.")
            ->with('generated_password', $plainPassword)
            ->with('new_user_email', $user->email)
            ->with('new_user_name', trim($user->name . ' ' . $user->prenom));
    }

    public function usersShow(User $user)
    {
        $user->load('role.permissions');

        return view('role-dynamique.users.show', compact('user'));
    }

    public function usersEdit(User $user)
    {
        $roles = Role::with('permissions')
            ->whereNotIn('nom', ['Administration', 'Administrateur Entreprise', User::ROLE_SUPER_ADMIN, 'Partenaire'])
            ->orderBy('nom')
            ->get();

        $permissions = Permission::all();

        return view('role-dynamique.users.edit', compact('user', 'roles', 'permissions'));
    }

    public function usersUpdate(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $role = Role::whereNotIn('nom', ['Administration', 'Administrateur Entreprise', User::ROLE_SUPER_ADMIN, 'Partenaire'])
            ->findOrFail($request->role_id);

        $updateData = [
            'name' => $request->name,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'role_id' => $role->id,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                $oldPath = ltrim($user->photo, '/');
                if (str_starts_with($oldPath, 'storage/')) {
                    $oldPath = substr($oldPath, strlen('storage/'));
                }
                Storage::disk('public')->delete($oldPath);
            }
            $photo = $request->file('photo');
            $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $updateData['photo'] = $photo->storeAs('uploads/profil-images', $filename, 'public');
        }

        $user->update($updateData);

        return redirect()->route('role-dynamique.users.index')
            ->with('success', "Utilisateur \"{$user->name}\" mis à jour avec succès.");
    }

    public function usersDestroy(User $user)
    {
        if ((int) auth()->id() === (int) $user->id) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        if ($user->isAdminEntreprise() || $user->isSuperAdmin()) {
            return back()->with('error', 'Ce compte protégé ne peut pas être supprimé.');
        }

        $nom = trim($user->name . ' ' . $user->prenom);
        $user->delete();

        return redirect()->route('role-dynamique.users.index')
            ->with('success', "Utilisateur \"{$nom}\" supprimé avec succès.");
    }

    public function usersResetPassword(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Le compte Super Admin est exclu de cette gestion.');
        }

        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
        $plainPassword = substr(str_shuffle(str_repeat($chars, 4)), 0, 12);
        $user->update(['password' => Hash::make($plainPassword)]);

        try {
            Mail::to($user->email)->send(new UserCreatedMail(
                trim($user->name . ' ' . $user->prenom),
                $user->email,
                $plainPassword
            ));
        } catch (\Throwable $e) {
            // Ignore email errors, reset remains successful.
        }

        return redirect()->route('role-dynamique.users.index')
            ->with('success', "Mot de passe réinitialisé pour {$user->name}. Nouveau MDP : {$plainPassword}");
    }
}