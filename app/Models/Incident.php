<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'projet_id',
        'phase_id',
        'signale_par',
        'titre',
        'description',
        'gravite',
        'statut',
        'date_debut',
        'date_fin_prevue',
        'resolution',
        'resolu_par',
        'date_resolution',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'date_resolution' => 'datetime',
    ];

    /**
     * Get the project that owns the incident.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * Get the phase that owns the incident.
     */
    public function phase()
    {
        return $this->belongsTo(Phase::class);
    }

    /**
     * Get the user who reported the incident.
     */
    public function signalePar()
    {
        return $this->belongsTo(User::class, 'signale_par');
    }

    /**
     * Get the user who resolved the incident.
     */
    public function resoluPar()
    {
        return $this->belongsTo(User::class, 'resolu_par');
    }

    /**
     * Check if the incident is open.
     */
    public function isOuvert()
    {
        return in_array($this->statut, ['ouvert', 'en_traitement']);
    }

    /**
     * Check if the incident is critical and unresolved for more than 24 hours.
     */
    public function isCritiqueNonResolu($heures = 24)
    {
        return $this->gravite === 'critique' &&
               $this->statut !== 'resolu' &&
               $this->created_at->addHours($heures)->isPast();
    }

    /**
     * Get gravite label.
     */
    public function getGraviteLabel()
    {
        $labels = [
            'faible' => 'Faible',
            'moyen' => 'Moyen',
            'critique' => 'Critique',
        ];
        return $labels[$this->gravite] ?? $this->gravite;
    }

    /**
     * Get statut label.
     */
    public function getStatutLabel()
    {
        $labels = [
            'ouvert' => 'Ouvert',
            'en_traitement' => 'En Traitement',
            'resolu' => 'Résolu',
            'ferme' => 'Fermé',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }
}

