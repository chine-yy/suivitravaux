<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PartenaireSeeder extends Seeder
{
    public function run(): void
    {
        $partenaireRole = \App\Models\Role::find(2);
        if (!$partenaireRole) {
            $partenaireRole = \App\Models\Role::firstOrCreate(
                ['nom' => 'Partenaire'],
                ['slug' => 'partenaire']
            );
            \App\Models\Role::unguard();
            $partenaireRole->id = 2;
            $partenaireRole->save();
            \App\Models\Role::reguard();
        }
    }
}