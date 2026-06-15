<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Mail\PasswordChangedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Permission;
use PDF;

class RoleDynamiqueProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('role-dynamique.profil.index', compact('user'));
    }

    public function editPermissions()
    {
        $user = auth()->user();
        $permissions = Permission::all();
        $userPermissions = $user->role ? $user->role->permissions()->pluck('id')->toArray() : [];

        return view('role-dynamique.profil.permissions', compact('user', 'permissions', 'userPermissions'));
    }

    public function updatePermissions(Request $request)
    {
        $user = auth()->user();

        $selectedPermissions = $request->input('permissions', []);

        $validPermissionIds = Permission::pluck('id')->toArray();
        $selectedPermissions = array_filter($selectedPermissions, function($id) use ($validPermissionIds) {
            return in_array($id, $validPermissionIds);
        });

        if ($user->role) {
            $user->role->permissions()->sync($selectedPermissions);
        }

        return redirect()->route('role-dynamique.parametres')
            ->with('success', 'Permissions mises à jour avec succès.');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $isProfileUpdate = $request->filled('name') || $request->filled('email') || $request->hasFile('photo');

        if ($isProfileUpdate) {
            $request->validate([
                'name'    => 'required|string|max:100',
                'prenom'  => 'nullable|string|max:100',
                'email'   => 'required|email|unique:users,email,' . $user->id,
                'telephone' => 'nullable|string',
                'photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        }

        $updateData = [
            'name'      => $request->input('name', $user->name),
            'prenom'    => $request->input('prenom', $user->prenom),
            'email'     => $request->input('email', $user->email),
            'telephone' => $request->input('telephone', $user->telephone),
        ];
        
        $statusField = $request->has('is_active') ? 'is_active' : ($request->has('statut') ? 'statut' : null);
        if ($statusField !== null) {
            $updateData['is_active'] = $request->boolean($statusField);
        }

        $passwordChanged = $request->filled('password');

        if ($request->filled('password')) {
            $request->validate([
                'password'              => 'min:6',
                'password_confirmation' => 'required_with:password|same:password',
            ]);
            $updateData['password'] = Hash::make($request->password);

            Mail::to($user->email)->send(new PasswordChangedMail($user));
        }

        if ($request->hasFile('photo')) {
            $this->deleteProfilePhotoFile($user->photo);
            $photo = $request->file('photo');
            $filename = Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('uploads/profil-images', $filename, 'public');
            $updateData['photo'] = $photoPath;
        }

        $user->update($updateData);

        if ($passwordChanged) {
            return back()->with('success', 'Mot de passe modifié avec succès.');
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function destroyPhoto()
    {
        $user = auth()->user();

        $this->deleteProfilePhotoFile($user->photo);
        $user->update(['photo' => null]);

        return back()->with('success', 'Photo de profil supprimée avec succès.');
    }

    public function configuration()
    {
        $configurations = [
            'app_name' => config('app.name'),
            'app_env' => config('app.env', 'production'),
            'app_debug' => config('app.debug', false),
            'db_driver' => config('database.default', 'mysql'),
            'db_host' => config('database.connections.mysql.host', 'localhost'),
            'mail_driver' => config('mail.default', 'smtp'),
            'session_driver' => config('session.driver', 'file'),
            'cache_driver' => config('cache.default', 'file'),
            'filesystems_default' => config('filesystems.default', 'local'),
            'queue_default' => config('queue.default', 'sync'),
            'log_channel' => config('logging.default', 'stack'),
            'timezone' => config('app.timezone', 'UTC'),
            'locale' => config('app.locale', 'fr'),
        ];

        return view('role-dynamique.configuration.index', compact('configurations'));
    }

    public function viewLogs()
    {
        $user = auth()->user();
        if (!$user || !$user->hasPermission('view-logs')) {
            abort(403, 'Permission "view-logs" requise.');
        }

        $logFile = storage_path('logs/laravel.log');
        $logs = "";

        if (file_exists($logFile)) {
            $file = new \SplFileObject($logFile, 'r');
            $file->seek(PHP_INT_MAX);
            $lastLine = $file->key();

            $start = max(0, $lastLine - 500);
            $file->seek($start);

            while (!$file->eof()) {
                $logs .= $file->current();
                $file->next();
            }
        } else {
            $logs = "No log file found at " . $logFile;
        }

        $canClear = $user->hasPermission('clear-logs');
        $canExport = $user->hasPermission('exporter-pdf-logs');

        return view('role-dynamique.configuration.logs', compact('logs', 'canClear', 'canExport'));
    }

    public function clearLogs()
    {
        $user = auth()->user();
        
        if (!$user || !$user->hasPermission('clear-logs')) {
            abort(403, 'Permission "clear-logs" requise.');
        }

        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            return back()->with('success', 'Le fichier de logs a été vidé avec succès.');
        }
        return back()->with('error', 'Le fichier de logs est introuvable.');
    }

    public function exportLogsPdf()
    {
        $user = auth()->user();

        if (!$user || !$user->hasPermission('exporter-pdf-logs')) {
            abort(403, 'Permission "exporter-pdf-logs" requise.');
        }

        $logFile = storage_path('logs/laravel.log');
        $logs = "";

        if (file_exists($logFile)) {
            // Pour le PDF, on peut exporter un peu plus de lignes ou tout le fichier
            // Mais pour éviter les plantages PDF sur des fichiers géants, on va limiter aux 1000 dernières lignes
            $file = new \SplFileObject($logFile, 'r');
            $file->seek(PHP_INT_MAX);
            $lastLine = $file->key();

            $start = max(0, $lastLine - 1000);
            $file->seek($start);

            while (!$file->eof()) {
                $logs .= $file->current();
                $file->next();
            }
        } else {
            $logs = "Aucun log disponible.";
        }

        $pdf = \PDF::loadView('partials.pdf-logs', compact('logs'));
        return $pdf->download('laravel-log-' . now()->format('Y-m-d_H-i-s') . '.pdf');
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
