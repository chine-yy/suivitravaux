<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Auth;
use App\Support\PermissionSlugResolver;

trait ChecksPermissions
{
    protected function hasPermission(string $permission): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdminEntreprise()) {
            return true;
        }

        return $user->hasPermission($permission);
    }
}