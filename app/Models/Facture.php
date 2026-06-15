<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'partenaire_id',
        'projet_id',
        'contrat_id',
        'numero_facture',
        'type',
        'montant_ht',
        'montant_tva',
        'montant_ttc',
        'date_emission',
        'date_echeance',
        'statut_paiement',
        'mode_paiement',
        'notes',
        'created_by',
        'est_envoye_partenaire',
        'date_envoi_partenaire',
    ];

    protected $casts = [
        'montant_ht' => 'decimal:2',
        'montant_tva' => 'decimal:2',
        'montant_ttc' => 'decimal:2',
        'date_emission' => 'datetime',
        'date_echeance' => 'datetime',
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

public function contrat(): BelongsTo
    {
        return $this->belongsTo(Contrat::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isPaid(): bool
    {
        return $this->statut_paiement === 'paye';
    }

    public function isOverdue(): bool
    {
        return !$this->isPaid() && $this->date_echeance->isPast();
    }

    /**
     * Boot the model to handle automatic numbering.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->numero_facture)) {
                $latest = static::latest('id')->first();
                $number = $latest ? intval($latest->numero_facture) + 1 : 1;
                $model->numero_facture = str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
