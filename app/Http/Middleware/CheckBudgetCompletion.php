<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class CheckBudgetCompletion
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if (!$user) {
            return $next($request);
        }

        $isSuperAdmin = $user->isSuperAdmin() || ($user->type_compte ?? null) === 'super_admin';
        $isAdmin = $user->isAdminEntreprise() || ($user->type_compte ?? null) === 'admin';
        $isPartenaire = $user->isPartenaire() || ($user->type_compte ?? null) === 'partenaire';

        $currentYear = date('Y');

        $budgetExists = \App\Models\Budget::where('annee', $currentYear)->exists();

        $routeName = Route::currentRouteName();

        // Allowed routes even without budget
        $allowedRoutes = [
            'login', 'logout', 'dashboard', 'accueil',
            'password.*',
            'super-admin.dashboard', 'admin.dashboard', 'admin-entreprise.dashboard',
            'super-admin.budget.create', 'super-admin.budget.store', 'super-admin.rapports.*', 'admin.budget.create', 'admin.budget.store', 'admin.budget.index',
            'admin.taches.*', 'admin.phases.*', 'admin.projets.*',
            'admin.roles.*', 'admin.permissions.*',
            'admin.equipes.*', 'admin.sous-traitance.*',
            'admin.sous-taches.*', 'admin.rapports.*', 'admin.incidents.*',
            'admin.configuration.*', 'admin.database.*', 'admin.historique.*',
            'admin.chat.*', 'admin.ia-chat.*', 'admin.documents.*',
            'admin.profile.*', 'admin.notifications.*',
            'role-dynamique.budget.*', 'role-dynamique.dashboard',
            'role-dynamique.taches.*', 'role-dynamique.projets.*', 'role-dynamique.contrats.*', 'role-dynamique.factures.*',
            'role-dynamique.phases.*', 'role-dynamique.sous-taches.*',
            'role-dynamique.rapports.*', 'role-dynamique.incidents.*',
            'role-dynamique.chat.*', 'role-dynamique.ia-chat.*',
            'role-dynamique.configuration.*', 'role-dynamique.historique.*',
            'role-dynamique.parametres.*', 'role-dynamique.profile.*', 'role-dynamique.documents.*',
            'admin-entreprise.budget.*', 'admin-entreprise.projets.*',
            'admin-entreprise.phases.*', 'admin-entreprise.taches.*',
            'admin-entreprise.sous-taches.*', 'admin-entreprise.rapports.*',
            'admin-entreprise.incidents.*', 'admin-entreprise.chat.*',
            'admin-entreprise.ia-chat.*', 'admin-entreprise.configuration.*',
            'admin-entreprise.historique.*', 'admin-entreprise.parametres.*',
            'admin-entreprise.profile.*', 'admin-entreprise.documents.*',
            'profile.*', 'parametres.*',
        ];

        // Check if current route is allowed
        $isAllowed = false;
        foreach ($allowedRoutes as $allowed) {
            if (Str::is($allowed, $routeName)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$budgetExists && !$isAllowed) {
            $target = 'role-dynamique.budget.create';
            if ($isSuperAdmin) {
                $target = 'super-admin.budget.create';
            } elseif ($isAdmin) {
                $target = 'admin.budget.create';
            }

            if (Route::has($target)) {
                return redirect()->route($target)->with('warning', 'Veuillez établir un budget annuel pour ' . $currentYear . ' avant de continuer.');
            }

            $index = 'role-dynamique.budget.index';
            if ($isSuperAdmin) {
                $index = 'super-admin.budget.index';
            } elseif ($isAdmin) {
                $index = 'admin.budget.index';
            }
            if (Route::has($index)) {
                return redirect()->route($index)->with('warning', 'Veuillez établir un budget annuel pour ' . $currentYear . ' avant de continuer.');
            }
        }

        return $next($request);
    }
}
