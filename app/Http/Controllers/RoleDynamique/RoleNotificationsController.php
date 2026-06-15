<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;

class RoleNotificationsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $notifications = $user ? $user->notifications()->latest()->take(30)->get() : collect();
        return view('role-dynamique.notifications.index', compact('notifications'));
    }
}
