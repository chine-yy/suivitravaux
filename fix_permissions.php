<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Delete old IA Chat and Messagerie actions that are incorrect
DB::table('permissions')->where('module', 'ia-chat')->delete();
DB::table('permissions')->where('slug', 'view-ia-chat-box')->delete();

// Make sure 'activer-ia-chat-box' is there
$exists1 = DB::table('permissions')->where('slug', 'activer-ia-chat-box')->exists();
if (!$exists1) {
    DB::table('permissions')->insert([
        'nom' => 'Activer IA Chat Box',
        'slug' => 'activer-ia-chat-box',
        'module' => 'ia-chat-box',
        'action' => 'activer',
        'group' => 'Communication',
        'icon' => 'chat-square-quote',
        'color' => 'success',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

$exists2 = DB::table('permissions')->where('slug', 'activer-messagerie')->exists();
if (!$exists2) {
    DB::table('permissions')->insert([
        'nom' => 'Activer Messagerie',
        'slug' => 'activer-messagerie',
        'module' => 'messagerie',
        'action' => 'activer',
        'group' => 'Communication',
        'icon' => 'chat-dots',
        'color' => 'success',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// Give all roles these new permissions if they are 'Administrateur Entreprise'
$adminRoles = DB::table('roles')->where('nom', 'Administrateur Entreprise')->get();
$perm1 = DB::table('permissions')->where('slug', 'activer-ia-chat-box')->first();
$perm2 = DB::table('permissions')->where('slug', 'activer-messagerie')->first();

foreach ($adminRoles as $role) {
    if ($perm1) {
        DB::table('role_permissions')->updateOrInsert(['role_id' => $role->id, 'permission_id' => $perm1->id]);
    }
    if ($perm2) {
        DB::table('role_permissions')->updateOrInsert(['role_id' => $role->id, 'permission_id' => $perm2->id]);
    }
}

echo "Permissions fixed successfully!\n";
