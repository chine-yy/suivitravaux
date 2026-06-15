<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionHelper
{
    public static function getActiveSession()
    {
        return session('active_session');
    }

    public static function getAllSessions()
    {
        return session('multi_sessions', []);
    }

    public static function getActiveSessionData()
    {
        $activeKey = session('active_session');
        $sessions = session('multi_sessions', []);
        
        if ($activeKey && isset($sessions[$activeKey])) {
            return $sessions[$activeKey];
        }
        
        return null;
    }

    public static function getActiveUser()
    {
        $activeKey = session('active_session');
        $sessions = session('multi_sessions', []);
        
        if ($activeKey && isset($sessions[$activeKey])) {
            $session = $sessions[$activeKey];
            $guard = $session['guard'] ?? 'web';
            
            return Auth::guard($guard)->user();
        }
        
        return null;
    }

    public static function getCurrentGuard()
    {
        $activeData = self::getActiveSessionData();
        return $activeData['guard'] ?? 'web';
    }

    public static function getCurrentType()
    {
        $activeData = self::getActiveSessionData();
        return $activeData['type'] ?? null;
    }

    public static function hasMultipleSessions()
    {
        return count(self::getAllSessions()) > 1;
    }

    public static function sessionCount()
    {
        return count(self::getAllSessions());
    }

    public static function isSuperAdminSession()
    {
        return self::getCurrentType() === 'SuperAdmin';
    }

    public static function isAdminSession()
    {
        return self::getCurrentType() === 'Admin';
    }

    public static function isPartenaireSession()
    {
        return self::getCurrentType() === 'Partenaire';
    }

    public static function isRolePersonnaliseSession()
    {
        return self::getCurrentType() === 'RolePersonnalise';
    }

    public static function switchToSession(string $sessionKey): bool
    {
        $sessions = session('multi_sessions', []);
        if (isset($sessions[$sessionKey])) {
            session(['active_session' => $sessionKey]);
            return true;
        }
        return false;
    }
}