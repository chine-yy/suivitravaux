<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'adresse',
        'categorie',
        'site_web',
        'contact_nom',
        'contact_prenom',
        'contact_telephone',
        'notes',
        'statut',
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
