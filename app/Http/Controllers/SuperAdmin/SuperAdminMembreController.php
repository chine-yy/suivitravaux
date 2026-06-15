<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Membre;
use App\Models\User;
use App\Rules\UniqueEmailAcrossSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Services\PhpMailerService;
use Illuminate\Support\Facades\View;

class SuperAdminMembreController extends Controller
{
    public function index()
    {
        $membres = Membre::all();
        return view('super-admin.membres.index', compact('membres'));
    }

    public function create()
    {
        return view('super-admin.membres.create');
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

        $membre = Membre::create([
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
        $emailBody = View::make('superadmin-emails.membre-created', [
            'userName' => $request->prenom . ' ' . $request->nom,
            'email' => $request->email,
            'password' => $password,
        ])->render();

        $mailer->send([
            'to' => $request->email,
            'to_name' => $request->prenom . ' ' . $request->nom,
            'subject' => 'Vos identifiants Membre ' . config('app.name'),
            'body' => $emailBody,
            'alt_body' => "Votre compte a été créé. Email: {$request->email}, Mot de passe: {$password}",
            'is_html' => true,
        ]);

        return redirect()->route('super-admin.membres.index')->with('success', 'Membre ajouté et email envoyé');
    }

    public function edit($id)
    {
        $membre = Membre::findOrFail($id);
        return view('super-admin.membres.edit', compact('membre'));
    }

    public function update(Request $request, $id)
    {
        $membre = Membre::findOrFail($id);
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', new UniqueEmailAcrossSystem($id, 'membres')],
            'telephone' => 'nullable|string|max:20',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
        ]);

        $membre->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        // Use NEW email from request, not OLD email from $membre
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->update(['email' => $request->email, 'name' => $request->prenom . ' ' . $request->nom]);
        }

        return redirect()->route('super-admin.membres.index')->with('success', 'Membre modifié');
    }

    public function destroy($id)
    {
        $membre = Membre::findOrFail($id);
        User::where('email', $membre->email)->delete();
        $membre->delete();
        return back()->with('success', 'Membre supprimé');
    }
}
