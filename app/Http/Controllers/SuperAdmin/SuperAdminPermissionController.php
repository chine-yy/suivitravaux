<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;

class SuperAdminPermissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::withCount('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $permissions = $query->latest()->get();
        $allPermissions = Permission::withCount('roles')->latest()->get();
        $groupedPermissions = Permission::getGroupedPermissions($permissions);
        $roles = Role::with('permissions')
            ->whereNotIn('nom', ['Administration', 'Super Admin', 'Partenaire'])
            ->get();

        $totalPermissions = collect($groupedPermissions)->sum(
            fn (array $modules) => collect($modules)->sum(fn (array $module) => count($module['permissions']))
        );
        $totalRoles = $roles->count();

        $permissionsData = $allPermissions->map(function($p) {
            return ['nom' => $p->nom, 'roles' => $p->roles_count];
        });

        return view('super-admin.permissions.index', compact(
            'permissions', 'groupedPermissions', 'roles', 'totalPermissions', 'totalRoles', 'permissionsData'
        ));
    }

    public function show($id)
    {
        $permission = Permission::with('roles')->findOrFail($id);
        return view('super-admin.permissions.show', compact('permission'));
    }


    /**
     * Création de permissions désactivée — les permissions sont gérées en dur.
     */
    public function store(Request $request)
    {
        abort(403, 'La création de permissions est désactivée.');
    }


    public function update(Request $request, Permission $permission)
    {
        $request->validate(['nom' => 'required|string|max:100']);
        $permission->update(['nom' => $request->nom]);
        return redirect()->route('super-admin.permissions.index')
            ->with('success', "Permission mise à jour avec succès.");
    }

    public function destroy(Permission $permission)
    {
        $nom = $permission->nom;
        $permission->delete();
        return redirect()->route('super-admin.permissions.index')
            ->with('success', "Permission \"{$nom}\" supprimée avec succès.");
    }

    public function assignToRole(Request $request, Role $role)
    {
        $request->validate(['permissions' => 'nullable|array']);
        $role->permissions()->sync($request->permissions ?? []);
        return redirect()->route('super-admin.permissions.index')
            ->with('success', "Permissions du rôle \"{$role->nom}\" mises à jour.");
    }
}
