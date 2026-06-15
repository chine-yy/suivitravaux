<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ChefProjet;
use App\Models\User;
use App\Models\Entreprise;
use App\Rules\UniqueEmailAcrossSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\PhpMailerService;
use Illuminate\Support\Facades\View;

class SuperAdminChefProjetController extends Controller
{
    public function index()
    {
        $chefProjets = ChefProjet::all();
        return view('super-admin.chef-projets.index', compact('chefProjets'));
    }

    public function create()
    {
        return view('super-admin.chef-projets.create');
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
        $entreprise = Entreprise::first();

        // Check if an entreprise exists
        $entrepriseId = $entreprise ? $entreprise->id : 1;

        $chefProjet = ChefProjet::create([
            'id_entreprise' => $entrepriseId,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($password),
            'poste' => 'Chef de projet',
            'statut' => true,
        ]);

        User::create([
            'name' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => Hash::make($password),
        ]);

        // Send Email
        $mailer = app(PhpMailerService::class);
        $emailBody = View::make('superadmin-emails.chef-projet-created', [
            'userName' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => $password,
        ])->render();

        $mailer->send([
            'to' => $request->email,
            'to_name' => $request->prenom . ' ' . $request->nom,
            'subject' => 'Vos identifiants Chef de Projet ' . config('app.name'),
            'body' => $emailBody,
            'alt_body' => "Votre compte a été créé. Email: {$request->email}, Mot de passe: {$password}",
            'is_html' => true,
        ]);

        return redirect()->route('super-admin.chef-projets.index')->with('success', 'Chef de projet ajouté et email envoyé');
    }

    public function edit($id)
    {
        $chefProjet = ChefProjet::findOrFail($id);
        return view('super-admin.chef-projets.edit', compact('chefProjet'));
    }

    public function update(Request $request, $id)
    {
        $chefProjet = ChefProjet::findOrFail($id);
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', new UniqueEmailAcrossSystem($id, 'chef_projets')],
            'telephone' => 'nullable|string|max:20',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
        ]);

        $chefProjet->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        // Use NEW email from request, not OLD email from $chefProjet
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update(['email' => $request->email, 'name' => $request->prenom . ' ' . $request->nom]);
        }

        return redirect()->route('super-admin.chef-projets.index')->with('success', 'Chef de projet modifié');
    }

    public function destroy($id)
    {
        $chefProjet = ChefProjet::findOrFail($id);
        User::where('email', $chefProjet->email)->delete();
        $chefProjet->delete();
        return back()->with('success', 'Chef de projet supprimé');
    }
}
