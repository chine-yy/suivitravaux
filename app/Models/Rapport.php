<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Rapport extends Model
{
    use HasFactory;

    protected $fillable = [
        'projet_id',
        'auteur_id',
        'sous_tache_id',
        'partenaire_id',
        'type',
        'titre',
        'contenu',
        'observations',
        'difficultes',
        'solutions',
        'statut',
        'avancement_constate',
        'destinataire_id',
        'destinataire_type',
        'est_envoye',
        'date_envoi',
        'envoye_par_id',
    ];

    /**
     * Get the project that owns the report.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * Get the sub-task context.
     */
    public function sousTache()
    {
        return $this->belongsTo(SousTache::class, 'sous_tache_id');
    }

    /**
     * Get the partenaire receiving the report.
     */
    public function partenaire()
    {
        return $this->belongsTo(Partenaire::class);
    }

    /**
     * Get the author of the report.
     */
    public function auteur()
    {
        return $this->belongsTo(User::class, 'auteur_id');
    }

    public function envoyePar()
    {
        return $this->belongsTo(User::class, 'envoye_par_id');
    }

    /**
     * Get the recipient of the report (polymorphic).
     */
    public function destinataire()
    {
        return $this->morphTo();
    }

    /**
     * Check if the report is pending validation.
     */
    public function isEnAttente()
    {
        return $this->statut === 'soumis';
    }

    /**
     * Check if validation is overdue.
     */
    public function isEnRetardValidation()
    {
        return $this->statut === 'soumis' &&
               $this->created_at &&
               now()->diffInDays($this->created_at) > 2;
    }

    /**
     * Get type label.
     */
    public function getTypeLabel()
    {
        $labels = [
            'journalier' => 'Rapport Journalier',
            'hebdomadaire' => 'Rapport Hebdomadaire',
            'mensuel' => 'Rapport Mensuel',
            'incident' => 'Rapport d\'Incident',
            'fin_tache' => 'Fin de Tâche',
            'sous_tache' => 'Rapport de Sous-tâche',
        ];
        return $labels[$this->type] ?? $this->type;
    }

    /**
     * Get statut label.
     */
    public function getStatutLabel()
    {
        $labels = [
            'soumis' => 'Soumis',
            'en_revision' => 'En Révision',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }
}

