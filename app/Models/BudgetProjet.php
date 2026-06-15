<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetProjet extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'projet_id',
        'montant_alloue',
        'montant_consomme',
    ];

    protected $casts = [
        'montant_alloue' => 'decimal:2',
    ];

    /**
     * Get the budget that owns this allocation.
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Get the project.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * Get the expenses for this project allocation.
     */
    public function depenses()
    {
        return $this->hasMany(Depense::class, 'budget_projet_id');
    }

    /**
     * Get total spent amount for this project (only validated expenses).
     */
    public function getTotalConsomme()
    {
        return $this->projet->getDynamicConsomme($this->budget_id);
    }

    /**
     * Get remaining budget for this project.
     */
    public function getRemaining()
    {
        return $this->montant_alloue - $this->getTotalConsomme();
    }

    /**
     * Get percentage spent.
     */
    public function getPercentageSpent()
    {
        if ($this->montant_alloue > 0) {
            return round(($this->getTotalConsomme() / $this->montant_alloue) * 100, 2);
        }
        return 0;
    }

    /**
     * Get allocation history.
     */
    public function getHistorique()
    {
        return $this->depenses()->orderBy('date_depense', 'desc')->get();
    }

    /**
     * Check if project has exceeded budget.
     */
    public function isOverBudget()
    {
        return $this->getTotalConsomme() > $this->montant_alloue;
    }

    /**
     * Get percentage attribute for accessor.
     */
    public function getPercentageAttribute()
    {
        return $this->getPercentageSpent();
    }
}

