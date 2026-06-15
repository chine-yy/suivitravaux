<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SuperAdmin extends Authenticatable
{
    use Notifiable;

    protected $table = 'super_admins';

    protected $fillable = [
        'user_id',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the user profile associated with the super admin credentials.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Compatibility: delegate profile fields to the linked User.
     */
    public function __get($key)
    {
        if (in_array($key, ['name', 'prenom', 'telephone', 'role_id'])) {
            return $this->user?->$key;
        }

        return parent::__get($key);
    }
    
    /**
     * Check if the user is a Super Admin (STI helper compatibility).
     */
    public function isSuperAdmin()
    {
        return true;
    }
}
