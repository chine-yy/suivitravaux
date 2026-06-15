<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Depense extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_projet_id',
        'projet_id',
        'montant',
        'description',
        'categorie',
        'date_depense',
        'type_paiement',
        'reference',
        'statut',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_depense' => 'date',
    ];

    /**
     * Get the budget project allocation.
     */
    public function budgetProjet()
    {
        return $this->belongsTo(BudgetProjet::class);
    }

    /**
     * Get the project.
     */
    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * Get the user who created the expense.
     */
    public function createur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get category label.
     */
    public function getCategorieLabel()
    {
        $labels = [
            'materiaux' => 'Matériaux',
            'main_oeuvre' => 'Main d\'œuvre',
            'equipement' => 'Équipement',
            'transport' => 'Transport',
            'sous_traitance' => 'Sous-traitance',
            'services' => 'Services',
            'autres' => 'Autres',
        ];
        return $labels[$this->categorie] ?? $this->categorie;
    }

    /**
     * Get payment type label.
     */
    public function getTypePaiementLabel()
    {
        $labels = [
            'especes' => 'Espèces',
            'virement' => 'Virement bancaire',
            'cheque' => 'Chèque',
            'carte_bancaire' => 'Carte bancaire',
            'autres' => 'Autres',
        ];
        return $labels[$this->type_paiement] ?? $this->type_paiement;
    }

    /**
     * Get status label.
     */
    public function getStatutLabel()
    {
        $labels = [
            'en_attente' => 'En attente',
            'validee' => 'Validée',
            'rejetee' => 'Rejetée',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }
}

