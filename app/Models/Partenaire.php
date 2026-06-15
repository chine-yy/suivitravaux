<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Partenaire extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'prenom',
        'email',
        'telephone',
        'password',
        'role_id',
        'projet_id',
        'entreprise_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted()
    {
        static::addGlobalScope('partenaire_role', function ($builder) {
            $builder->whereHas('role', function ($q) {
                $q->where('nom', 'Partenaire');
            });
        });
    }

    /**
     * Scope to order partenaires by name.
     * Fixes SQL error: Column 'nom' unknown - ambiguous column when joining users and roles tables.
     */
    public function scopeOrderedByName(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    /**
     * Get the project associated with the partenaire.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    /**
     * Role relationship.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
