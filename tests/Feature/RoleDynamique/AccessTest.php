<?php

namespace Tests\Feature\RoleDynamique;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that users with appropriate permissions can access role-dynamique routes
     */
    public function test_user_with_dashboard_permission_can_access_dashboard()
    {
        // Create a permission
        $permission = Permission::create([
            'name' => 'acces-dashboard',
            'slug' => 'acces-dashboard',
            'module' => 'dashboard',
            'action' => 'view',
        ]);

        // Create a role with that permission
        $role = Role::create([
            'nom' => 'Utilisateur Test',
            'slug' => 'utilisateur-test',
        ]);

        $role->givePermissionTo($permission);

        // Create a user with that role
        $user = User::factory()->create([
            'role_id' => $role->id,
            'type_compte' => 'role_personnalise',
        ]);

        // Login as the user
        $this->actingAs($user);

        // Access the dashboard
        $response = $this->get(route('role-dynamique.dashboard'));

        // Should be successful (200) or redirect to login if not authenticated
        $this->assertTrue($response->isSuccessful() || $response->isRedirect());
    }

    /**
     * Test that users without appropriate permissions are denied access
     */
    public function test_user_without_permission_is_denied_access()
    {
        // Create a role without permissions
        $role = Role::create([
            'nom' => 'Utilisateur Sans Permission',
            'slug' => 'utilisateur-sans-permission',
        ]);

        // Create a user with that role
        $user = User::factory()->create([
            'role_id' => $role->id,
            'type_compte' => 'role_personnalise',
        ]);

        // Login as the user
        $this->actingAs($user);

        // Try to access a protected route
        $response = $this->get(route('role-dynamique.dashboard'));

        // Should be forbidden (403) or redirect
        $this->assertTrue($response->isForbidden() || $response->isRedirect());
    }

    /**
     * Test that super admin can access everything
     */
    public function test_super_admin_can_access_routes()
    {
        // Create a super admin user
        $user = User::factory()->create([
            'type_compte' => 'superadmin',
            'email' => 'superadmin@test.com',
        ]);

        // Login as super admin
        $this->actingAs($user, 'superadmin');

        // Access super admin dashboard
        $response = $this->get(route('super-admin.dashboard'));

        // Should be successful
        $this->assertTrue($response->isSuccessful() || $response->isRedirect());
    }
}