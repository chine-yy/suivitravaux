<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ChefEquipe;
use App\Models\User;
use App\Rules\UniqueEmailAcrossSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\PhpMailerService;
use Illuminate\Support\Facades\View;

class SuperAdminChefEquipeController extends Controller
{
    public function index()
    {
        $chefEquipes = ChefEquipe::all();
        return view('super-admin.chef-equipes.index', compact('chefEquipes'));
    }

    public function create()
    {
        return view('super-admin.chef-equipes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', new UniqueEmailAcrossSystem()],
            'telephone' => 'nullable|string|max:20',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
        ]);

        $password = Str::random(10);

        $chefEquipe = ChefEquipe::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($password),
            'statut' => true,
        ]);

        User::create([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => Hash::make($password),
        ]);

        // Send Email
        $mailer = app(PhpMailerService::class);
        $emailBody = View::make('superadmin-emails.chef-equipe-created', [
            'userName' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => $password,
        ])->render();

        $mailer->send([
            'to' => $request->email,
            'to_name' => $request->prenom . ' ' . $request->nom,
            'subject' => 'Vos identifiants Chef d\'Équipe ' . config('app.name'),
            'body' => $emailBody,
            'alt_body' => "Votre compte a été créé. Email: {$request->email}, Mot de passe: {$password}",
            'is_html' => true,
        ]);

        return redirect()->route('super-admin.chef-equipes.index')->with('success', 'Chef d\'équipe ajouté et email envoyé');
    }

    public function edit($id)
    {
        $chefEquipe = ChefEquipe::findOrFail($id);
        return view('super-admin.chef-equipes.edit', compact('chefEquipe'));
    }

    public function update(Request $request, $id)
    {
        $chefEquipe = ChefEquipe::findOrFail($id);
        $oldEmail = $chefEquipe->email;

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', new UniqueEmailAcrossSystem($id, 'chef_equipes')],
            'telephone' => 'nullable|string|max:20',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
        ]);

        $chefEquipe->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        // Synchroniser le compte utilisateur lié
        $user = User::where('email', $oldEmail)->first();
        if (!$user) {
            $user = User::where('email', $request->email)->first();
        }

        if ($user) {
            $user->update([
                'name' => $request->prenom . ' ' . $request->nom,
                'email' => $request->email,
            ]);
        }

        return redirect()->route('super-admin.chef-equipes.index')->with('success', 'Chef d\'équipe mis à jour');
    }

    public function destroy($id)
    {
        $chefEquipe = ChefEquipe::findOrFail($id);
        User::where('email', $chefEquipe->email)->delete();
        $chefEquipe->delete();

        return back()->with('success', 'Chef d\'équipe supprimé');
    }
}
