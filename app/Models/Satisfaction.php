<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Satisfaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'entreprise_id',
        'partenaire_id',
        'projet_id',
        'note',
        'commentaire',
        'date_envoi',
        'date_reponse',
        'statut',
    ];

    protected $casts = [
        'note' => 'integer',
        'date_envoi' => 'datetime',
        'date_reponse' => 'datetime',
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

    public function getNoteLabel(): string
    {
        $labels = [
            1 => 'Très insatisfait',
            2 => 'Insatisfait',
            3 => 'Neutre',
            4 => 'Satisfait',
            5 => 'Très satisfait',
        ];
        return $labels[$this->note] ?? 'N/A';
    }
}
