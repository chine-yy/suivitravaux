<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate(
            ['nom' => User::ROLE_SUPER_ADMIN],
            ['slug' => 'super-admin']
        );
        Role::unguard();
        $superAdminRole->id = 1;
        $superAdminRole->save();
        Role::reguard();

        User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Admin',
                'prenom' => 'Super',
                'role_id' => 1,
                'type_compte' => 'role_personnalise',
                'telephone' => null,
                'photo' => null,
                'is_active' => true,
                'password' => Hash::make('superadmin@gmail.com'),
            ]
        );
    }
}
