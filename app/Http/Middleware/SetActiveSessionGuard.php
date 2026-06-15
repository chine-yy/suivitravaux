<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetActiveSessionGuard
{
    public function handle(Request $request, Closure $next)
    {
        $sessions = session('multi_sessions', []);
        $path = $request->getPathInfo();
        $globalActiveKey = session('active_session');
        
        $activeKey = null;

        if (str_starts_with($path, '/super-admin')) {
            $activeKey = collect($sessions)->where('type', 'SuperAdmin')->keys()->first();
        } elseif (str_starts_with($path, '/role-dynamique')) {
            $activeKey = collect($sessions)->whereIn('type', ['RolePersonnalise', 'Admin'])->keys()->first();
        } elseif (str_starts_with($path, '/partenaire')) {
            $activeKey = collect($sessions)->where('type', 'Partenaire')->keys()->first();
        } else {
            // Non-prefixed route: use the global active session
            $activeKey = $globalActiveKey;
        }

        // Update global active session if we found a better match for this prefix
        if ($activeKey && $activeKey !== $globalActiveKey) {
            session(['active_session' => $activeKey]);
        }

        if ($activeKey && isset($sessions[$activeKey])) {
            $session = $sessions[$activeKey];
            $userId = $session['user_id'] ?? null;

            if ($userId) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    Auth::shouldUse('web');
                    Auth::guard('web')->setUser($user);
                    
                    if ($session['type'] === 'SuperAdmin') {
                        Auth::guard('superadmin')->setUser($user);
                    }
                }
            }
        } else {
            // Strictly isolate: no user should be authenticated if the session doesn't match the prefix
            if (str_starts_with($path, '/super-admin') || str_starts_with($path, '/role-dynamique') || str_starts_with($path, '/partenaire')) {
                // We use logout() to clear the guard's user, but we must ensure multi_sessions persists
                $sessionsBackup = session('multi_sessions', []);
                Auth::guard('web')->logout();
                Auth::guard('superadmin')->logout();
                session(['multi_sessions' => $sessionsBackup]);
            }
        }

        return $next($request);
    }
}