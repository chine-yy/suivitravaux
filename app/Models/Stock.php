<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise_id',
        'fournisseur_id',
        'nom',
        'reference',
        'categorie',
        'quantite',
        'prix_unitaire',
        'emplacement',
        'description',
        'statut',
    ];

    protected $casts = [
        'quantite' => 'integer',
        'prix_unitaire' => 'decimal:2',
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }



    public function getValeurTotale(): float
    {
        return $this->quantite * $this->prix_unitaire;
    }
}
