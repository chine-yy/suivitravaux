<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowRegistrationAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}