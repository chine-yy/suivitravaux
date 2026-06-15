<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SousTraitance;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'annee',
        'budget_total',
        'description',
        'statut',
    ];

    protected $casts = [
        'budget_total' => 'decimal:2',
        'annee' => 'integer',
    ];


    /**
     * Get the budget projects for this budget.
     */
    public function budgetProjets()
    {
        return $this->hasMany(BudgetProjet::class);
    }

    /**
     * Get all projects associated with this budget.
     */
    public function projets()
    {
        return $this->belongsToMany(Projet::class, 'budget_projets')
            ->withPivot('montant_alloue')
            ->withTimestamps();
    }

    /**
     * Get total allocated to projects (sum of budget_projets.montant_alloue).
     */
    public function getTotalAlloueProjets()
    {
        return $this->budgetProjets()->sum('montant_alloue');
    }

    /**
     * Get total allocated to sous-traitance contracts (sum of sous_traitances.montant_contrat).
     */
    public function getTotalAlloueSousTraitance()
    {
        $projectIds = $this->budgetProjets()->pluck('projet_id');
        return SousTraitance::whereIn('projet_id', $projectIds)->sum('montant_contrat');
    }

    /**
     * Get total allocated (projects + sous-traitance).
     */
    public function getTotalAlloue()
    {
        return $this->getTotalAlloueProjets() + $this->getTotalAlloueSousTraitance();
    }

    /**
     * Get total validated expenses.
     */
    public function getTotalDepenses()
    {
        return $this->budgetProjets->map(function($bp) {
            return $bp->getTotalConsomme();
        })->sum();
    }

    /**
     * Get Solde Total (allocation remaining)
     * = Budget Total - (Alloué Projets + Alloué ST)
     * Dépenses sont suivies séparément et n'affectent pas le solde d'allocation.
     */
    public function getSoldeTotal()
    {
        $totalAlloue = $this->getTotalAlloueProjets()
            + $this->getTotalAlloueSousTraitance();

        return max(0, $this->budget_total - $totalAlloue);
    }

    /**
     * Alias: remaining after everything (real-time).
     */
    public function getRemaining()
    {
        return $this->getSoldeTotal();
    }

    /**
     * Alias: solde disponible (real-time).
     */
    public function getSoldeDisponible()
    {
        return $this->getSoldeTotal();
    }

    /**
     * Get remaining available for new project allocations.
     * When allocating, we check against solde + oldAllocation (for updates).
     */
    public function getRemainingForProjectAllocation()
    {
        return $this->getSoldeTotal();
    }

    /**
     * Get remaining available for new sous-traitance allocations.
     */
    public function getRemainingForSTAllocation()
    {
        return $this->getSoldeTotal();
    }

    /**
     * Get remaining available for factures.
     */
    public function getRemainingForFacture()
    {
        return $this->getSoldeTotal();
    }

    /**
     * Get remaining budget for a specific project (allocated - validated expenses).
     */
    public function getRemainingForProject($projetId)
    {
        $bp = $this->budgetProjets()->where('projet_id', $projetId)->first();
        if (!$bp) {
            return 0;
        }
        return $bp->getRemaining();
    }

    /**
     * Check if budget is for current year.
     */
    public function isCurrentYear()
    {
        return $this->annee == date('Y');
    }

    /**
     * Get status label.
     */
    public function getStatutLabel()
    {
        $labels = [
            'brouillon' => 'Brouillon',
            'valide' => 'Validé',
            'clos' => 'Clôturé',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }
}
