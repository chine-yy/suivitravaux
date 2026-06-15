<?php

namespace App\Providers;

use App\Support\PermissionSlugResolver;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        View::composer('*', function ($view) {
            try {
                $request = request();
                $isRoleDynamiqueRoute = $request->is('role-dynamique/*');
                $isSuperAdminRoute = $request->is('super-admin/*');

                if ($isRoleDynamiqueRoute && auth()->check()) {
                    $user = auth()->user();
                    $isSuperAdmin = false;
                    $currentGuard = 'web';
                } elseif ($isSuperAdminRoute && auth('superadmin')->check()) {
                    $user = auth('superadmin')->user();
                    $isSuperAdmin = true;
                    $currentGuard = 'superadmin';
                } else {
                    $user = auth('superadmin')->user() ?? auth()->user();
                    $isSuperAdmin = auth('superadmin')->check();
                    $currentGuard = null;
                }

                $isFullAdmin = $user && method_exists($user, 'isAdminEntreprise') && $user->isAdminEntreprise();
                $permissionSlugs = [];

                if ($user && method_exists($user, 'permissions')) {
                    $permissions = $user->permissions();

                    if ($permissions instanceof Collection) {
                        $permissionSlugs = $permissions->pluck('slug')->all();
                    } else {
                        $permissionSlugs = $permissions->pluck('slug')->all();
                    }
                }

                $hasPermission = function (?string $permission = null) use ($isSuperAdmin, $permissionSlugs) {
                    if ($permission === null) {
                        return false;
                    }

                    return $isSuperAdmin
                        || PermissionSlugResolver::matches($permission, $permissionSlugs);
                };

                $hasAnyPermission = function (array $permissions) use ($isSuperAdmin, $permissionSlugs) {
                    if ($isSuperAdmin) {
                        return true;
                    }

                    return collect($permissions)->contains(
                        fn ($permission) => PermissionSlugResolver::matches((string) $permission, $permissionSlugs)
                    );
                };

                $view->with([
                    'authUserPermissionSlugs' => $permissionSlugs,
                    'canPermission' => $hasPermission,
                    'canAnyPermission' => $hasAnyPermission,
                    'has' => $hasPermission,
                ]);
            } catch (\Throwable $e) {
                // Log and provide safe defaults so the application doesn't throw a 500 when DB is down
                \Log::error('View composer error (possibly DB unavailable): ' . $e->getMessage());

                $view->with([
                    'authUserPermissionSlugs' => [],
                    'canPermission' => fn (?string $perm = null) => false,
                    'canAnyPermission' => fn (array $perms = []) => false,
                    'has' => fn (?string $perm = null) => false,
                ]);
            }
        });
    }
}
