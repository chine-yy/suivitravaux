<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'budget',
        'budget_consomme',
        'date_debut',
        'date_fin_prevue',
        'date_fin_reelle',
        'avancement',
        'statut',
        'type_travaux',
        'partenaire',
        'partenaire_id',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'budget_consomme' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'date_fin_reelle' => 'date',
        'avancement' => 'integer',
    ];


    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function partenaire()
    {
        return $this->belongsTo(User::class, 'partenaire_id');
    }

    public function partenaires()
    {
        return $this->belongsToMany(User::class, 'projet_partenaires')->withTimestamps();
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'partenaire_id');
    }


    /**
     * Get the phases of the project.
     */
    public function phases()
    {
        return $this->hasMany(Phase::class)->orderBy('ordre');
    }

    /**
     * Get the tasks of the project.
     */
    public function taches()
    {
        return $this->hasMany(Tache::class);
    }

    /**
     * Get the reports of the project.
     */
    public function rapports()
    {
        return $this->hasMany(Rapport::class);
    }

    /**
     * Get the incidents of the project.
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    /**
     * Check if the project is en retard (late).
     */
    public function isEnRetard()
    {
        return $this->statut !== 'termine' &&
               $this->date_fin_prevue &&
               now()->gt($this->date_fin_prevue);
    }

    /**
     * Get budget percentage consumed.
     */
    public function getBudgetConsommePercentage()
    {
        if ($this->budget > 0) {
            return round(($this->budget_consomme / $this->budget) * 100, 2);
        }
        return 0;
    }

    // Removed chefEquipe relationship as ChefEquipe model no longer exists

    /**
     * Get the equipes of the project.
     */
    public function equipes()
    {
        return $this->hasMany(Equipe::class);
    }

/**
     * Get the number of unique members in the project.
     */
    public function membresCount()
    {
        return User::whereHas('equipes', function($q) {
            $q->where('projet_id', $this->id);
        })->distinct()->count();
    }

    /**
     * Get the members (users) of the project through equipes.
     */
    public function membres()
    {
        return User::whereHas('equipes', function($q) {
            $q->where('projet_id', $this->id);
        });
    }

    /**
     * Get the budget allocations for this project.
     */
    public function budgetProjets()
    {
        return $this->hasMany(BudgetProjet::class);
    }

    /**
     * Get the sub-contracting records for this project.
     */
    public function sousTraitances()
    {
        return $this->hasMany(SousTraitance::class);
    }

    /**
     * Get all expenses for this project.
     */
    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }

    /**
     * Get total consumed budget for this project from validated expenses.
     * If a budgetId is provided, only expenses linked to that budget are counted.
     */
    public function getDynamicConsomme($budgetId = null)
    {
        $queryDepenses = $this->depenses()->whereIn('statut', ['validee', 'en_attente']);
        
        if ($budgetId) {
            $queryDepenses->where('budget_projet_id', function($q) use ($budgetId) {
                $q->select('id')->from('budget_projets')->where('budget_id', $budgetId)->where('projet_id', $this->id);
            });
        }
        
        $totalDepenses = $queryDepenses->sum('montant');
        $totalSousTraitances = $this->sousTraitances()->sum('montant_contrat');
        
        return $totalDepenses + $totalSousTraitances;
    }
}
