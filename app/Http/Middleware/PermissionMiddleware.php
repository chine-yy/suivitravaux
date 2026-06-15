<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        // Vérifier quel garde est actif pour cette route
        $route = $request->route();
        $routePrefix = $route->getPrefix();

        // ✅ SUPER ADMIN : seulement dans son propre espace
        if (str_starts_with($routePrefix, '/super-admin')) {
            if (Auth::guard('superadmin')->check()) {
                // Super Admin a TOUTES les permissions uniquement dans son espace
                return $next($request);
            }
            abort(403);
        }

        // ✅ ROLE DYNAMIQUE / UTILISATEURS : seulement garde web, JAMAIS Super Admin
        if (str_starts_with($routePrefix, '/role-dynamique')) {
            if (!Auth::guard('web')->check() || Auth::guard('web')->user()->isSuperAdmin()) {
                if (Auth::guard('web')->check() && Auth::guard('web')->user()->isSuperAdmin()) {
                     return redirect('/super-admin/dashboard')->with('error', 'Accès réservé aux rôles personnalisés.');
                }
                return redirect('/login');
            }

            $user = Auth::guard('web')->user();

            if (!method_exists($user, 'hasPermission') || !$user->hasPermission((string) $permission)) {
                abort(403, 'Accès refusé. Permission "' . $permission . '" requise.');
            }

            return $next($request);
        }

        // Pour les autres routes
        if (!Auth::check() && !Auth::guard('superadmin')->check()) {
            return redirect('/login');
        }

        $user = Auth::guard('web')->check() ? Auth::guard('web')->user() : Auth::guard('superadmin')->user();

        if (!$user) {
            return redirect('/login');
        }

        if (Auth::guard('superadmin')->check()) {
            return $next($request);
        }

        if (!method_exists($user, 'hasPermission') || !$user->hasPermission((string) $permission)) {
            abort(403, 'Accès refusé. Permission "' . $permission . '" requise.');
        }

        return $next($request);
    }
}
