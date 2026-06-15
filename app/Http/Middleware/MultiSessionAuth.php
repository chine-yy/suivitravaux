<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MultiSessionAuth
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $activeKey = session('active_session');
        $sessions = session('multi_sessions', []);
        
        if ($activeKey && isset($sessions[$activeKey])) {
            $session = $sessions[$activeKey];
            $guard = $session['guard'] ?? 'web';
            
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }
        
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }
        
        return redirect()->route('login');
    }
}