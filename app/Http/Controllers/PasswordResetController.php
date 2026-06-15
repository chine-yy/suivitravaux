<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Services\PhpMailerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use App\Mail\PasswordChangedMail;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    /**
     * Show the form to enter email for OTP.
     */
    public function showRequestForm()
    {
        // Dynamic roles like login form
        $rolesDisponibles = \App\Models\Role::orderBy('nom')
            ->where('nom', '!=', 'Administration')->get();
        $roles = ['super-admin' => 'Super Admin'];
        foreach($rolesDisponibles as $role) {
            $roles['role_' . $role->id] = $role->nom;
        }
        $roles['admin'] = 'Admin Entreprise';
        return view('auth.password-request', compact('roles'));
    }

    /**
     * Send OTP to the provided email.
     */
    public function sendOtp(Request $request, PhpMailerService $mailer)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'string'],
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'role.required' => 'Veuillez sélectionner votre type de compte.',
        ]);

        $email = $request->input('email');
        $role = $request->input('role');

        // Check if user exists in the correct table
        $exists = false;
        if ($role === 'admin') {
            $exists = User::entrepriseAdmins()->where('email', $email)->exists();
        } elseif ($role === 'super-admin') {
            $exists = User::superAdmins()->where('email', $email)->exists();
        } elseif (str_starts_with($role, 'role_')) {
            $roleId = (int) str_replace('role_', '', $role);
            $exists = \App\Models\User::where('email', $email)
                ->whereHas('role', fn($q) => $q->where('id', $roleId))
                ->exists();
        }

        if (!$exists) {
            $roleLabel = str_replace('role_', 'rôle personnalisé ', $role);
            return back()->withErrors([
                'email' => 'Aucun compte ' . $roleLabel . ' n\'est associé à cette adresse email.',
            ])->withInput();
        }

        // Generate OTP
        $otpRecord = PasswordResetOtp::generateFor($email);

        // Send OTP via email
        $body = View::make('emails.otp', [
            'otp' => $otpRecord->otp,
            'email' => $email,
        ])->render();

        $sent = $mailer->send([
            'to' => $email,
            'to_name' => '',
            'subject' => 'Code OTP - Réinitialisation de mot de passe ' . config('app.name'),
            'body' => $body,
            'alt_body' => 'Votre code OTP est : ' . $otpRecord->otp . '. Ce code expire dans 15 minutes.',
            'is_html' => true,
        ]);

        if (!$sent) {
            return back()->with('error', 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.')->withInput();
        }

        return redirect()->route('password.verify-otp', ['email' => $email, 'role' => $role])
            ->with('success', 'Un code OTP a été envoyé à votre adresse email.');
    }

    /**
     * Show the OTP verification form.
     */
    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email');
        $role = $request->query('role');
        if (!$email) {
            return redirect()->route('password.request')->with('error', 'Veuillez d\'abord saisir votre email.');
        }
        return view('auth.password-verify', compact('email', 'role'));
    }

    /**
     * Verify OTP code.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string'],
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Le code OTP est obligatoire.',
            'otp.size' => 'Le code OTP doit contenir exactement 6 chiffres.',
        ]);

        $otpRecord = PasswordResetOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$otpRecord || !$otpRecord->isValid()) {
            return back()->withErrors([
                'otp' => 'Le code OTP est invalide ou a expiré. Veuillez en demander un nouveau.',
            ])->withInput();
        }

        // Mark the OTP as used
        $otpRecord->update(['used' => true]);

        // Redirect to password reset form with a token
        $token = encrypt($request->email . '|' . $request->role . '|' . now()->timestamp);

        return redirect()->route('password.reset-form', ['token' => $token, 'email' => $request->email, 'role' => $request->role]);
    }

    /**
     * Show the new password form.
     */
    public function showResetForm(Request $request)
    {
        $email = $request->query('email');
        $role = $request->query('role');
        $token = $request->query('token');

        if (!$email || !$token) {
            return redirect()->route('password.request')->with('error', 'Lien invalide.');
        }

        // Verify token
        try {
            $decoded = decrypt($token);
            $parts = explode('|', $decoded);
            if (count($parts) < 3) throw new \Exception('Invalid token format');
            [$decodedEmail, $decodedRole, $timestamp] = $parts;

            if ($decodedEmail !== $email || $decodedRole !== $role || (now()->timestamp - $timestamp) > 1800) {
                return redirect()->route('password.request')->with('error', 'Le lien a expiré. Veuillez recommencer.');
            }
        } catch (\Exception $e) {
            return redirect()->route('password.request')->with('error', 'Lien invalide.');
        }

        return view('auth.password-reset', compact('email', 'role', 'token'));
    }

    /**
     * Reset the password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'role' => ['required', 'string'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);

        // Verify token
        try {
            $decoded = decrypt($request->token);
            $parts = explode('|', $decoded);
            [$decodedEmail, $decodedRole, $timestamp] = $parts;

            if ($decodedEmail !== $request->email || $decodedRole !== $request->role || (now()->timestamp - $timestamp) > 1800) {
                return redirect()->route('password.request')->with('error', 'Le lien a expiré.');
            }
        } catch (\Exception $e) {
            return redirect()->route('password.request')->with('error', 'Lien invalide.');
        }

        $newPassword = Hash::make($request->password);
        $role = $request->role;
        $userObj = null;

        if ($role === 'admin') {
            $userObj = User::entrepriseAdmins()->where('email', $request->email)->first();
            if ($userObj) $userObj->update(['password' => $newPassword]);
        } elseif ($role === 'super-admin') {
            $userObj = User::superAdmins()->where('email', $request->email)->first();
            if ($userObj) $userObj->update(['password' => $newPassword]);
        } elseif (str_starts_with($role, 'role_')) {
            $roleId = (int) str_replace('role_', '', $role);
            $userObj = \App\Models\User::where('email', $request->email)
                ->whereHas('role', fn($q) => $q->where('id', $roleId))
                ->first();
            if ($userObj) $userObj->update(['password' => $newPassword]);
        } else {
            // Fallback
            $userObj = User::where('email', $request->email)->first();
            if ($userObj) $userObj->update(['password' => $newPassword]);
        }

        if ($userObj) {
            Mail::to($userObj->email)->send(new PasswordChangedMail($userObj));
        }

        return redirect()->route('login')->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
