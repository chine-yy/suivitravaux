<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;
use Closure;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     * We add super-admin paths so even the $except fallback works.
     *
     * @var array<int, string>
     */
    protected $except = [
        '',
        '/',
        'super-admin',
        'super-admin/*',
        'login',
        'logout',
        'password/*',
    ];
}
