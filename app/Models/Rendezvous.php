<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rendezvous extends Model
{
    use HasFactory;

    protected $table = 'rendezvous';

    protected $fillable = [
        'entreprise_id',
        'projet_id',
        'user_id',
        'titre',
        'description',
        'date_heure',
        'duree_minutes',
        'lieu',
        'type',
        'type_autre',
        'statut',
        'rappel',
    ];

    protected $casts = [
        'date_heure' => 'datetime',
        'duree_minutes' => 'integer',
    ];

    public function entreprise(): BelongsTo
    {
        return $this->belongsTo(Entreprise::class);
    }

    public function projet(): BelongsTo
    {
        return $this->belongsTo(Projet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDateFin(): \Carbon\Carbon
    {
        return $this->date_heure->addMinutes($this->duree_minutes);
    }
}
