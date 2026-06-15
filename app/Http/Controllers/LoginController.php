<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController
{
    protected function getSessionKey($type, $id)
    {
        return $type . '_' . $id;
    }

    protected function storeSession(Request $request, string $type, $user, array $extraData = [])
    {
        $sessionKey = $this->getSessionKey($type, $user->id);

        $sessionData = array_merge([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name ?? $user->nom ?? $user->email,
            'type' => $type,
            'guard' => 'web',
        ], $extraData);

        $sessions = session()->get('multi_sessions', []);
        $sessions[$sessionKey] = $sessionData;
        session(['multi_sessions' => $sessions]);

        session(['active_session' => $sessionKey]);

        return $sessionKey;
    }

    protected function setActiveSession(string $sessionKey)
    {
        $sessions = session()->get('multi_sessions', []);
        if (isset($sessions[$sessionKey])) {
            session(['active_session' => $sessionKey]);
            return true;
        }
        return false;
    }

    public function getActiveUser()
    {
        $activeKey = session('active_session');
        $sessions = session('multi_sessions', []);

        if ($activeKey && isset($sessions[$activeKey])) {
            $session = $sessions[$activeKey];
            $guard = $session['guard'] ?? 'web';

            $user = Auth::guard($guard)->user();
            if ($user) {
                return (object) array_merge((array) $user, [
                    'session_type' => $session['type'],
                    'session_key' => $activeKey
                ]);
            }
        }

        return null;
    }

    public function switchSession(Request $request, string $sessionKey)
    {
        if ($this->setActiveSession($sessionKey)) {
            $sessions = session('multi_sessions', []);
            $session = $sessions[$sessionKey] ?? null;

            if ($session) {
                $type = $session['type'];
                if ($type === 'SuperAdmin') {
                    return redirect()->route('super-admin.dashboard');
                } elseif ($type === 'Partenaire') {
                    return redirect()->route('partenaire.dashboard');
                } elseif ($type === 'Admin') {
                    return redirect()->route('admin-entreprise.dashboard');
                }
                return redirect()->route('role-dynamique.dashboard');
            }
        }

        return back()->with('error', 'Session invalide');
    }

    public function switchSessionGet(string $sessionKey)
    {
        if ($this->setActiveSession($sessionKey)) {
            $sessions = session('multi_sessions', []);
            $session = $sessions[$sessionKey] ?? null;

            if ($session) {
                $type = $session['type'];
                if ($type === 'SuperAdmin') {
                    return redirect()->route('super-admin.dashboard');
                } elseif ($type === 'Partenaire') {
                    return redirect()->route('partenaire.dashboard');
                } elseif ($type === 'Admin') {
                    return redirect()->route('admin-entreprise.dashboard');
                }
                return redirect()->route('role-dynamique.dashboard');
            }
        }

        return back()->with('error', 'Session invalide');
    }

    public function getSessions()
    {
        return session('multi_sessions', []);
    }

    public function showLoginForm()
    {
        if (!\App\Models\Entreprise::hasRegisteredAccount() && \App\Models\User::superAdmins()->count() === 0) {
            return redirect()->route('entreprise.register');
        }
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = $credentials['email'];
        $password = $credentials['password'];

        $existingSessions = session('multi_sessions', []);
        $alreadyLoggedIn = false;
        $loggedInType = null;

        foreach ($existingSessions as $key => $session) {
            if ($session['email'] === $email) {
                $alreadyLoggedIn = true;
                $loggedInType = $session['type'];
                break;
            }
        }

        if ($alreadyLoggedIn) {
            return back()->with('error', 'Vous êtes déjà connecté en tant que ' . $loggedInType . '. Basculez sur cette session ci-dessous.')->withInput();
        }

        $hasExistingSessions = !empty(session('multi_sessions', []));

        $user = User::with('role')
            ->where('email', $email)
            ->where('is_active', true)
            ->first();

        if ($user && Auth::attempt(['email' => $email, 'password' => $password])) {
            if (!$hasExistingSessions) {
                $request->session()->regenerate();

                if ($user->isSuperAdmin() || $user->type_compte === 'super_admin') {
                    $this->storeSession($request, 'SuperAdmin', $user);
                    return redirect()->route('super-admin.dashboard')->with('success', 'Bienvenue dans votre espace Super Administrateur !');
                }

                if ($user->isAdminEntreprise() || $user->type_compte === 'admin') {
                    $this->storeSession($request, 'Admin', $user);
                    return redirect()->route('admin-entreprise.dashboard')->with('success', 'Bienvenue dans votre espace Administrateur !');
                }

                if ($user->isPartenaire() || $user->type_compte === 'partenaire') {
                    $this->storeSession($request, 'Partenaire', $user);
                    return redirect()->route('partenaire.dashboard')->with('success', 'Bienvenue dans votre espace Partenaire !');
                }

                if ($user->role) {
                    $this->storeSession($request, 'RolePersonnalise', $user, [
                        'role_id' => $user->role_id,
                        'role_name' => $user->role->nom
                    ]);
                    return redirect()->route('role-dynamique.dashboard')->with('success', 'Bienvenue ' . $user->role->nom . ' !');
                }

                $this->storeSession($request, 'User', $user);
                return redirect()->route('role-dynamique.dashboard')->with('success', 'Bienvenue sur votre espace !');
            }

            if ($user->isSuperAdmin() || $user->type_compte === 'super_admin') {
                $this->storeSession($request, 'SuperAdmin', $user);
                return redirect()->route('super-admin.dashboard')->with('success', 'Bienvenue dans votre espace Super Administrateur !');
            }

            if ($user->isAdminEntreprise() || $user->type_compte === 'admin') {
                $this->storeSession($request, 'Admin', $user);
                return redirect()->route('admin-entreprise.dashboard')->with('success', 'Bienvenue dans votre espace Administrateur !');
            }

            if ($user->isPartenaire() || $user->type_compte === 'partenaire') {
                $this->storeSession($request, 'Partenaire', $user);
                return redirect()->route('partenaire.dashboard')->with('success', 'Bienvenue dans votre espace Partenaire !');
            }

            if ($user->role) {
                $this->storeSession($request, 'RolePersonnalise', $user, [
                    'role_id' => $user->role_id,
                    'role_name' => $user->role->nom
                ]);
                return redirect()->route('role-dynamique.dashboard')->with('success', 'Bienvenue ' . $user->role->nom . ' !');
            }

            $this->storeSession($request, 'User', $user);
            return redirect()->route('role-dynamique.dashboard')->with('success', 'Bienvenue sur votre espace !');
        }

        return back()->withErrors(['email' => 'Ces identifiants ne correspondent pas à nos enregistrements.'])->onlyInput('email');
    }

    public function logout(Request $request, $sessionKey = null)
    {
        $sessions = session('multi_sessions', []);

        if ($sessionKey && isset($sessions[$sessionKey])) {
            $session = $sessions[$sessionKey];
            $guard = $session['guard'] ?? 'web';

            Auth::guard($guard)->logout();

            unset($sessions[$sessionKey]);
            session(['multi_sessions' => $sessions]);

            if (session('active_session') === $sessionKey) {
                $remainingSessions = session('multi_sessions', []);
                if (!empty($remainingSessions)) {
                    session(['active_session' => array_key_first($remainingSessions)]);
                } else {
                    session()->forget(['active_session', 'multi_sessions', 'active_role']);
                }
            }

            return redirect()->route('login')->with('success', 'Session déconnectée. Vous avez encore ' . count($sessions) . ' session(s) active(s).');
        }

        foreach ($sessions as $key => $session) {
            $guard = $session['guard'] ?? 'web';
            Auth::guard($guard)->logout();
        }

        session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
