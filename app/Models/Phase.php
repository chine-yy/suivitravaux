<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    use HasFactory;

    protected $fillable = [
        'projet_id',
        'nom',
        'description',
        'ordre',
        'date_debut',
        'date_fin_prevue',
        'date_fin_reelle',
        'avancement',
        'statut',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'date_fin_reelle' => 'date',
        'avancement' => 'integer',
        'ordre' => 'integer',
    ];

    /**
     * Get the project that owns the phase.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * Get the tasks of the phase.
     */
    public function taches()
    {
        return $this->hasMany(Tache::class);
    }

    /**
     * Get incidents for this phase.
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    /**
     * Check if the phase is late.
     */
    public function isEnRetard()
    {
        return $this->statut !== 'terminee' &&
               $this->date_fin_prevue &&
               now()->gt($this->date_fin_prevue);
    }

    /**
     * Check if the phase is ending soon (within 7 days).
     */
    public function isEcheanceProche($jours = 7)
    {
        return $this->date_fin_prevue &&
               now()->addDays($jours)->gte($this->date_fin_prevue) &&
               !$this->date_fin_reelle;
    }
}

