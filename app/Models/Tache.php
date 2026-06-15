<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Intervention;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'projet_id',
        'phase_id',
        'user_id',
        'titre',
        'description',
        'priorite',
        'statut',
        'avancement',
        'date_debut_prevue',
        'date_fin_prevue',
        'date_debut_reelle',
        'date_fin_reelle',
    ];

    protected $casts = [
        'date_debut_prevue' => 'date',
        'date_fin_prevue' => 'date',
        'date_debut_reelle' => 'date',
        'date_fin_reelle' => 'date',
        'avancement' => 'integer',
    ];

    /**
     * Get the project that owns the task.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * Get the phase that owns the task.
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }



    public function sousTaches()
    {
        return $this->hasMany(SousTache::class, 'tache_id');
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class, 'tache_id');
    }

    public function assignedPersonnels()
    {
        $technicienIds = $this->interventions()->pluck('technicien_id')->unique();
        return User::whereIn('id', $technicienIds)->get();
    }

    public function personnels(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'interventions', 'tache_id', 'technicien_id');
    }

    /**
     * Check if the task is late.
     */
    public function isEnRetard()
    {
        return $this->statut !== 'terminee' &&
               $this->date_fin_prevue &&
               now()->gt($this->date_fin_prevue);
    }

    /**
     * Get days of delay.
     */
    public function getJoursRetard()
    {
        if ($this->isEnRetard()) {
            return now()->diffInDays($this->date_fin_prevue);
        }
        return 0;
    }

    /**
     * Check if the task is currently in progress.
     */
    public function isEnCours()
    {
        return $this->statut === 'en_cours';
    }

    /**
     * Check if the task is blocked.
     */
    public function isBloquee()
    {
        return $this->statut === 'bloquee';
    }

    /**
     * Get the status color for badges.
     */
    public function getStatutColor()
    {
        switch ($this->statut) {
            case 'en_attente':
                return 'warning';
            case 'en_cours':
                return 'primary';
            case 'terminee':
                return 'success';
            case 'bloquee':
                return 'danger';
            case 'en_retard':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}
