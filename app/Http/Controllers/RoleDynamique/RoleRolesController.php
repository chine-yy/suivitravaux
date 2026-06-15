<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RoleRolesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:web', 'permission:view-roles-permissions']);
    }

    private function isReservedRole(Role $role): bool
    {
        $nomLower = Str::lower($role->nom);
        return in_array($nomLower, ['administration', 'super admin', 'super administrateur'], true);
    }

    private function normalizeRoleName(?string $name): string
    {
        return preg_replace('/\s+/u', ' ', trim((string) $name)) ?? '';
    }

    private function roleNameExists(string $name, ?int $ignoreRoleId = null): bool
    {
        $normalized = Str::lower($this->normalizeRoleName($name));

        return Role::query()
            ->when($ignoreRoleId, fn($q) => $q->where('id', '!=', $ignoreRoleId))
            ->get(['nom'])
            ->contains(fn($role) => Str::lower($this->normalizeRoleName($role->nom)) === $normalized);
    }

    private function roleSlugFromName(string $name): string
    {
        return Str::slug($this->normalizeRoleName($name));
    }

    private function ensureRoleNameIsUnique(string $name, ?int $ignoreRoleId = null): void
    {
        if ($this->roleNameExists($name, $ignoreRoleId)) {
            throw ValidationException::withMessages([
                'nom' => 'Un rôle avec ce nom existe déjà.',
            ]);
        }
    }

    private function ensureRoleSlugIsUnique(string $name, ?int $ignoreRoleId = null): void
    {
        $slug = $this->roleSlugFromName($name);
        if (blank($slug)) {
            throw ValidationException::withMessages([
                'nom' => 'Le nom du rôle est invalide pour générer un slug.',
            ]);
        }

        $slugExists = Role::query()
            ->where('slug', $slug)
            ->when($ignoreRoleId, fn($q) => $q->where('id', '!=', $ignoreRoleId))
            ->exists();

        if ($slugExists) {
            throw ValidationException::withMessages([
                'nom' => 'Un rôle avec ce slug existe déjà. Choisissez un nom différent.',
            ]);
        }
    }

    public function index(Request $request)
    {
        $query = Role::withCount('users')->with('permissions')
            ->whereNotIn('nom', ['Administration', 'Administrateur Entreprise', User::ROLE_SUPER_ADMIN, 'Partenaire']);

        if ($request->filled('nom')) {
            $query->where('nom', 'like', '%' . $request->nom . '%');
        }

        $roles = $query->latest()->get();

        // Stats based on all roles
        $allRoles = Role::whereNotIn('nom', ['Administration', 'Administrateur Entreprise', User::ROLE_SUPER_ADMIN, 'Partenaire'])->get();
        $totalRoles = $allRoles->count();
        $totalUtilisateursAvecRole = \App\Models\User::whereIn('role_id', $allRoles->pluck('id'))
            ->count();
        $permissions = Permission::all();

        // Stats for charts based on filtered roles
        $rolesData = $roles->map(function ($r) {
            return ['nom' => $r->nom, 'users' => $r->users_count, 'permissions' => $r->permissions->count()];
        });

        return view('role-dynamique.roles.index', compact(
            'roles',
            'totalRoles',
            'totalUtilisateursAvecRole',
            'permissions',
            'rolesData'
        ));
    }

    public function show($id)
    {
        $role = Role::with(['permissions', 'users'])->findOrFail($id);
        return view('role-dynamique.roles.show', compact('role'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('role-dynamique.roles.create', compact('permissions'));
    }

    private function ensureNotReservedRole(string $nom): void
    {
        $nomLower = Str::lower(trim($nom));
        $reservedRoles = [
            'administrateur entreprise', 
            'administration', 
            'super admin', 
            'super administrateur',
            'admisitrateur entreprise' // typo commune
        ];

        // Seule la correspondance exacte (peu importe la casse) est bloquée pour 
        // permettre des noms comme "Administrateur Entreprise 2" ou "super admin 2"
        $isReserved = in_array($nomLower, $reservedRoles, true);

        if ($isReserved) {
            throw ValidationException::withMessages([
                'nom' => 'Ce nom de rôle est réservé ou déjà attribué par défaut. Veuillez choisir un autre nom. Par exemple : Administrateur Entreprise 2 ou super admin 2',
            ]);
        }
    }

    public function store(Request $request)
    {
        $normalizedNom = $this->normalizeRoleName($request->nom);
        $request->merge(['nom' => $normalizedNom]);

        $request->validate([
            'nom' => 'required|string|max:100',
            'permissions' => 'nullable|array',
        ], [
            'nom.required' => 'Le nom du rôle est obligatoire.',
        ]);

        $this->ensureNotReservedRole($normalizedNom);
        $this->ensureRoleNameIsUnique($normalizedNom);
        $this->ensureRoleSlugIsUnique($normalizedNom);

        $role = Role::create([
            'nom' => $normalizedNom,
            'slug' => $this->roleSlugFromName($normalizedNom),
            'created_by' => auth()->id(),
        ]);

        $role->permissions()->sync($request->permissions ?? []);

        $successMessage = "Rôle \"{$role->nom}\" créé.";

        return redirect()->route('role-dynamique.roles.index')
            ->with('success', $successMessage);
    }

    public function edit(Role $role)
    {
        // Keep the core system role protected, but allow editing "Administrateur Entreprise".
        if ($this->isReservedRole($role)) {
            return redirect()->route('role-dynamique.roles.index')
                ->with('error', 'Impossible de modifier ce rôle réservé.');
        }

        $permissions = Permission::all();
        $selectedPermissions = $role->permissions->pluck('id')->toArray();
        return view('role-dynamique.roles.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        // Allow editing "Administrateur Entreprise" but only permissions, not the name
        $isAdminEntreprise = Str::lower($role->nom) === 'administrateur entreprise';
        
        if ($this->isReservedRole($role)) {
            return back()->with('error', 'Impossible de modifier ce rôle réservé.');
        }

        // For "Administrateur Entreprise", only allow updating permissions
        if ($isAdminEntreprise) {
            $request->validate([
                'permissions' => 'nullable|array',
            ]);
            
            $role->permissions()->sync($request->permissions ?? []);
            
            return redirect()->route('role-dynamique.roles.index')
                ->with('success', "Permissions du rôle \"{$role->nom}\" mises à jour avec succès.");
        }

        $normalizedNom = $this->normalizeRoleName($request->nom);
        $request->merge(['nom' => $normalizedNom]);

        $request->validate([
            'nom' => 'required|string|max:100',
            'permissions' => 'nullable|array',
        ]);

        $this->ensureNotReservedRole($normalizedNom);
        $this->ensureRoleNameIsUnique($normalizedNom, $role->id);

        $payload = ['nom' => $normalizedNom];
        if ($normalizedNom !== $role->nom) {
            $this->ensureRoleSlugIsUnique($normalizedNom, $role->id);
            $payload['slug'] = $this->roleSlugFromName($normalizedNom);
        }

        $role->update($payload);

        $role->permissions()->sync($request->permissions ?? []);

        $successMessage = "Rôle \"{$role->nom}\" mis à jour.";

        return redirect()->route('role-dynamique.roles.index')
            ->with('success', $successMessage);
    }

    public function destroy(Role $role)
    {
        $nomLower = Str::lower($role->nom);
        if (in_array($nomLower, ['administration', 'administrateur entreprise', 'super admin', 'super administrateur'], true)) {
            return back()->with('error', "Impossible de supprimer ce rôle spécial.");
        }
        if ($role->users()->count() > 0) {
            return back()->with('error', "Impossible de supprimer ce rôle : des utilisateurs y sont associés.");
        }
        $nom = $role->nom;
        $role->delete();
        return redirect()->route('role-dynamique.roles.index')
            ->with('success', "Rôle \"{$nom}\" supprimé avec succès.");
    }

    public function clone(Request $request)
    {
        $normalizedNom = $this->normalizeRoleName($request->nom);
        $request->merge(['nom' => $normalizedNom]);

        $request->validate([
            'source_role_id' => 'required|exists:roles,id',
            'nom' => 'required|string|max:100',
        ]);

        $source = Role::with('permissions')->findOrFail($request->source_role_id);

        $this->ensureRoleNameIsUnique($normalizedNom);
        $this->ensureRoleSlugIsUnique($normalizedNom);

        $nouveau = Role::create([
            'nom' => $normalizedNom,
            'slug' => $this->roleSlugFromName($normalizedNom),
            'created_by' => auth()->id(),
        ]);

        // Copier toutes les permissions du rôle source
        $permissionIds = $source->permissions->pluck('id')->toArray();
        if (!empty($permissionIds)) {
            $nouveau->permissions()->sync($permissionIds);
        }

        return redirect()->route('role-dynamique.roles.index')
            ->with('success', "Rôle \"{$source->nom}\" dupliqué en \"{$nouveau->nom}\" avec " . count($permissionIds) . " permissions.");
    }

    public function quickRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $roleId = $request->role_id == '0' ? null : $request->role_id;

        if ($roleId && !Role::where('id', $roleId)->exists()) {
            return back()->with('error', "Le rôle sélectionné n'existe pas.");
        }

        $user->update(['role_id' => $roleId]);

        $message = $roleId
            ? "Rôle attribué à {$user->name} {$user->prenom} avec succès."
            : "Rôle retiré à {$user->name} {$user->prenom}.";

        return redirect()->route('role-dynamique.roles.index')->with('success', $message);
    }
}
