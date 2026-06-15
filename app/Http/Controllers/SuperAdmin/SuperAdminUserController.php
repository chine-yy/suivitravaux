<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Rules\UniqueEmailAcrossSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Mail\UserCreatedMail;

/**
 * Compatible Laravel 8+ (pas de Str::password())
 */


use App\Mail\PasswordResetMail;

class SuperAdminUserController extends Controller
{
    private function isProtectedEntrepriseAdmin(User $user): bool
    {
        return $user->isAdminEntreprise();
    }

    private function isProtectedSystemUser(User $user): bool
    {
        return $user->isSuperAdmin() || $this->isProtectedEntrepriseAdmin($user);
    }

    /**
     * Voir les fonctionnalités d'un Administrateur
     */
    public function editAdminPermissions($adminId)
    {
        $admin = User::entrepriseAdmins()->findOrFail($adminId);
        $permissions = Permission::all();
        $adminPermissions = $admin->role ? $admin->role->permissions()->pluck('permissions.id')->toArray() : [];

        return view('super-admin.users.assign-permissions', compact('admin', 'permissions', 'adminPermissions'));
    }

    /**
     * Mettre à jour les fonctionnalités d'un Administrateur
     * Les permissions sont attribuées directement sans intermédiaire de rôle.
     */
    public function updateAdminPermissions(Request $request, $adminId)
    {
        $admin = User::entrepriseAdmins()->findOrFail($adminId);
        $selectedPermissions = $request->input('permissions', []);

        // Valider les IDs de permissions
        $validPermissionIds = Permission::pluck('id')->toArray();
        $selectedPermissions = array_filter($selectedPermissions, function($id) use ($validPermissionIds) {
            return in_array($id, $validPermissionIds);
        });

        if ($admin->role) {
            $admin->role->permissions()->sync($selectedPermissions);
        }

        return redirect()->route('super-admin.users.index')
            ->with('success', 'Permissions de l\'administrateur mises à jour avec succès.');
    }

    public function index(Request $request)
    {
        $roles = Role::query()
            ->whereNotIn('nom', ['Administration', User::ROLE_SUPER_ADMIN, 'Partenaire'])
            ->withCount('users')
            ->get();

        $query = User::with('role')
            ->whereDoesntHave('role', function ($q) {
                $q->whereIn('nom', [User::ROLE_SUPER_ADMIN, 'Partenaire']);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        $users = $query->latest()->get();

        $totalUsers      = $users->count();
        $usersActifs     = $users->where('is_active', true)->count();
        $usersInactifs   = $users->where('is_active', false)->count();
        $rolesCount      = $roles->count();

        // Stats par rôle pour graphique
        $usersParRole = $roles->map(function($r) {
            return ['nom' => $r->nom, 'count' => $r->users_count];
        });

        return view('super-admin.users.index', compact(
            'users', 'roles', 'totalUsers', 'usersActifs', 'usersInactifs', 'rolesCount', 'usersParRole'
        ));
    }

    public function create()
    {
        $roles = Role::with('permissions')
            ->whereNotIn('nom', ['Administration', 'Administrateur Entreprise', User::ROLE_SUPER_ADMIN, 'Partenaire'])
            ->get();
        $permissions = Permission::all();
        return view('super-admin.users.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => ['required', 'email', new UniqueEmailAcrossSystem()],
            'telephone' => 'nullable|string|max:20',
            'role_id'   => 'required|exists:roles,id',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
        ]);

        // Générer mot de passe automatiquement (compatible Laravel 8)
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
        $plainPassword = substr(str_shuffle(str_repeat($chars, 4)), 0, 12);

        $role = Role::whereNotIn('nom', ['Administration', 'Administrateur Entreprise', User::ROLE_SUPER_ADMIN, 'Partenaire'])
            ->findOrFail($request->role_id);

        $user = User::create([
            'name'       => $request->name,
            'prenom'     => $request->prenom,
            'email'      => $request->email,
            'telephone'  => $request->telephone,
            'password'   => Hash::make($plainPassword),
            'role_id'    => $role->id,
            'is_active'  => true,
        ]);

        // Envoyer l'email
        try {
            Mail::to($user->email)->send(new UserCreatedMail($user->name . ' ' . $user->prenom, $user->email, $plainPassword));
        } catch (\Exception $e) {
            // Log error or just continue
        }

        return redirect()->route('super-admin.users.index')
            ->with('success', "Utilisateur \"{$user->name} {$user->prenom}\" créé avec succès.")
            ->with('generated_password', $plainPassword)
            ->with('new_user_email', $user->email)
            ->with('new_user_name', $user->name . ' ' . $user->prenom);
    }

    public function show(User $user)
    {
        $user->load('role.permissions');
        return view('super-admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.users.index')
                ->with('error', 'Le compte Super Admin est exclu de cette gestion.');
        }

        $roles = Role::with('permissions')
            ->whereNotIn('nom', ['Administration', User::ROLE_SUPER_ADMIN, 'Partenaire'])
            ->get();
        $permissions = Permission::all();
        return view('super-admin.users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.users.index')
                ->with('error', 'Le compte Super Admin est exclu de cette gestion.');
        }

        $request->validate([
            'name'      => 'required|string|max:100',
            'prenom'    => 'required|string|max:100',
            'email'     => ['required', 'email', new UniqueEmailAcrossSystem($user->id, 'users')],
            'telephone' => 'nullable|string|max:20',
            'role_id'   => 'required|exists:roles,id',
            'is_active' => 'nullable|boolean',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
        ]);

        $updateData = [
            'name'      => $request->name,
            'prenom'    => $request->prenom,
            'email'     => $request->email,
            'telephone' => $request->telephone,
            'role_id'   => $request->role_id,
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

        return redirect()->route('super-admin.users.index')
            ->with('success', "Utilisateur \"{$user->name}\" mis à jour avec succès.");
    }

    public function destroy(User $user)
    {
        if ($this->isProtectedSystemUser($user)) {
            return back()->with('error', "Ce compte protégé ne peut pas être supprimé.");
        }

        $nom = $user->name . ' ' . $user->prenom;
        $user->delete();
        return redirect()->route('super-admin.users.index')
            ->with('success', "Utilisateur \"{$nom}\" supprimé avec succès.");
    }

    public function resetPassword(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Le mot de passe du compte Super Admin ne peut pas être modifié ici.');
        }

        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
        $plainPassword = substr(str_shuffle(str_repeat($chars, 4)), 0, 12);
        $user->update(['password' => Hash::make($plainPassword)]);

        // Envoyer l'email de nouveau mot de passe
        try {
            Mail::to($user->email)->send(new UserCreatedMail($user->name . ' ' . $user->prenom, $user->email, $plainPassword));
        } catch (\Exception $e) {
            //
        }

        return redirect()->route('super-admin.users.index')
            ->with('success', "Mot de passe réinitialisé pour {$user->name}. Nouveau MDP : {$plainPassword}");
    }

    /**
     * Supprimer un Administrateur Entreprise
     */
    public function destroyAdmin($id)
    {
        $admin = User::entrepriseAdmins()->findOrFail($id);

        if ($this->isProtectedEntrepriseAdmin($admin)) {
            return back()->with('error', "L'Administrateur Entreprise créé automatiquement ne peut pas être supprimé.");
        }

        $name = $admin->name;
        $admin->delete();

        return redirect()->route('super-admin.dashboard')
            ->with('success', "Administrateur \"{$name}\" supprimé avec succès.");
    }

    /**
     * Réinitialiser le mot de passe d'un Administrateur Entreprise
     */
    public function resetAdminPassword($id)
    {
        $admin = User::entrepriseAdmins()->findOrFail($id);

        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
        $plainPassword = substr(str_shuffle(str_repeat($chars, 4)), 0, 12);

        $admin->update([
            'password' => Hash::make($plainPassword)
        ]);

        // Envoyer l'email
        try {
            Mail::to($admin->email)->send(new PasswordResetMail($admin->name, $admin->email, $plainPassword));
        } catch (\Exception $e) {
            // Log silent error
        }

        return redirect()->route('super-admin.dashboard')
            ->with('success', "Le mot de passe de l'administrateur \"{$admin->name}\" a été réinitialisé. Email : {$admin->email}, Nouveau MDP : {$plainPassword}");
    }
}
