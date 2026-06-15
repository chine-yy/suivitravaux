<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Intervention;

class SousTache extends Model
{
    use HasFactory;

    protected $fillable = [
        'tache_id',
        'user_id',
        'titre',
        'description',
        'statut',
        'date_debut',
        'date_fin_prevue',
        'avancement',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'avancement' => 'integer',
    ];

    public function tache(): BelongsTo
    {
        return $this->belongsTo(Tache::class, 'tache_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function interventions(): HasMany
    {
        return $this->hasMany(Intervention::class, 'sous_tache_id');
    }

    public function assignedPersonnels()
    {
        $technicienIds = $this->interventions()->pluck('technicien_id')->unique();
        return User::whereIn('id', $technicienIds)->get();
    }

    public function personnels(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'interventions', 'sous_tache_id', 'technicien_id');
    }


    public function isEnRetard(): bool
    {
        return $this->statut !== 'terminee' &&
               $this->date_fin_prevue &&
               Carbon::now()->gt($this->date_fin_prevue);
    }
}
