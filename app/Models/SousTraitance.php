<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SousTraitance extends Model
{
    use HasFactory;

    protected $fillable = [
        'projet_id',
        'nom_entreprise',
        'contact_nom',
        'contact_prenom',
        'contact_email',
        'contact_telephone',
        'description_tache',
        'nombre_employes',
        'montant_contrat',
        'date_debut',
        'date_fin',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'montant_contrat' => 'decimal:2',
        'nombre_employes' => 'integer',
    ];

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }
}
