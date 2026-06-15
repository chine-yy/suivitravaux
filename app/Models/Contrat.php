<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contrat extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise_id',
        'partenaire_id',
        'projet_id',
        'numero_contrat',
        'type',
        'objet',
        'montant',
        'date_debut',
        'date_fin',
        'conditions',
        'statut',
        'created_by',
        'est_envoye_partenaire',
        'date_envoi_partenaire',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'est_envoye_partenaire' => 'boolean',
        'date_envoi_partenaire' => 'datetime',
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function partenaire(): BelongsTo
    {
        return $this->belongsTo(Partenaire::class);
    }

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }

    /**
     * Boot the model to handle automatic numbering.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->numero_contrat)) {
                $latest = static::latest('id')->first();
                $number = $latest ? intval($latest->numero_contrat) + 1 : 1;
                $model->numero_contrat = str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
