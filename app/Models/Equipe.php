<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    use HasFactory;

protected $fillable = [
    'nom',
    'description',
    'projet_id',
    'role_id',
    'chef_equipe_id',
    'statut',
];

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_equipe_id');
    }

public function users()
{
    return $this->belongsToMany(User::class, 'equipe_user');
}

public function scopeActive($query)
{
    return $query->where('statut', 'active');
}

public function scopeInactive($query)
{
    return $query->where('statut', 'inactive');
}

public function scopeSuspended($query)
{
    return $query->where('statut', 'suspended');
}
}

