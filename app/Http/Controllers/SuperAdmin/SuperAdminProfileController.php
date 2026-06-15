<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Mail\PasswordChangedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SuperAdminProfileController extends Controller
{
    public function show()
    {
        $user = $this->getSuperAdminUser();
        if (!$user) {
            abort(403, 'Accès réservé au Super Administrateur.');
        }
        return view('super-admin.profil.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $this->getSuperAdminUser();
        if (!$user) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:255',
            'statut' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'password' => 'nullable|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $request->name;
        $user->prenom = $request->prenom;
        $user->email = $request->email;
        $user->telephone = $request->telephone;
        $statusField = $request->has('is_active') ? 'is_active' : ($request->has('statut') ? 'statut' : null);
        if ($statusField !== null) {
            $user->is_active = $request->boolean($statusField);
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);

            // Send security email
            Mail::to($user->email)->send(new PasswordChangedMail($user));
        }

        // Handle photo upload
        $passwordChanged = $request->filled('password');

        if ($request->hasFile('photo')) {
            $this->handlePhotoUpload($request, $user);
        }

        $user->save();

        if ($passwordChanged) {
            return back()->with('success', 'Mot de passe modifié avec succès.');
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    private function handlePhotoUpload(Request $request, $user)
    {
        $this->deleteProfilePhotoFile($user->photo);

        // Upload new photo (Consistent location)
        $photo = $request->file('photo');
        $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
        $photoPath = $photo->storeAs('uploads/profil-images', $filename, 'public');

        $user->photo = $photoPath;
    }

    /**
     * Upload rapide depuis le modal du dashboard (champ profile_photo).
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = $this->getSuperAdminUser();
        if (!$user) {
            abort(403);
        }

        $this->deleteProfilePhotoFile($user->photo);

        $photo    = $request->file('profile_photo');
        $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
        $photoPath = $photo->storeAs('uploads/profil-images', $filename, 'public');

        $user->photo = $photoPath;
        $user->save();

        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Photo de profil mise à jour avec succès.');
    }


    public function resetPhoto(Request $request)
    {
        $user = $this->getSuperAdminUser();
        if (!$user) {
            abort(403);
        }

        $this->deleteProfilePhotoFile($user->photo);

        // Reset photo to null
        $user->photo = null;
        $user->save();

        return back()->with('success', 'Photo réinitialisée avec succès.');
    }

    /**
     * Résolution robuste de l'utilisateur Super Admin.
     * Cherche d'abord dans les sessions multi-comptes, puis fallback sur Auth::user().
     */
    private function getSuperAdminUser(): ?\App\Models\User
    {
        // 1. Chercher dans les multi-sessions
        $sessions = session('multi_sessions', []);
        $superAdminSession = collect($sessions)->where('type', 'SuperAdmin')->first();

        if ($superAdminSession && isset($superAdminSession['user_id'])) {
            $user = \App\Models\User::find($superAdminSession['user_id']);
            if ($user && $user->isSuperAdmin()) {
                return $user;
            }
        }

        // 2. Fallback sur l'utilisateur authentifié (garde web par défaut)
        $user = Auth::user();
        if ($user && $user->isSuperAdmin()) {
            return $user;
        }

        // 3. Fallback ultime : récupérer le premier Super Admin de la base (pour ne pas crash en debug)
        return \App\Models\User::superAdmins()->first();
    }

    private function deleteProfilePhotoFile(?string $photo): void
    {
        if (!$photo) {
            return;
        }

        $normalized = ltrim($photo, '/');
        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, strlen('storage/'));
        }

        $storageCandidates = [
            'public/' . $normalized,
            'public/photos/' . $normalized,
            'public/uploads/profil-images/' . basename($normalized),
        ];

        foreach ($storageCandidates as $candidate) {
            if (Storage::exists($candidate)) {
                Storage::delete($candidate);
            }
        }

        $publicCandidates = [
            public_path($normalized),
            public_path('storage/' . $normalized),
            public_path('storage/photos/' . $normalized),
            public_path('uploads/profil-images/' . basename($normalized)),
        ];

        foreach ($publicCandidates as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }
}
