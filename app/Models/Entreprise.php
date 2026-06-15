<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Entreprise extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_entreprise',
        'nom_entreprise',
        'adresse',
        'telephone',
        'email',
        'site_web',
        'ville',
        'pays',
        'description',
        'industry',
        'statut',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'statut' => 'boolean',
    ];

    /**
     * Get users linked to this entreprise.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get admin users linked to this entreprise.
     */
    public function administrateurs(): HasMany
    {
        return $this->hasMany(User::class)
            ->whereHas('role', fn ($q) => $q->where('nom', User::ROLE_ADMIN_ENTREPRISE));
    }

    /**
     * Get the primary administrator for the entreprise.
     */
    public function administrateurPrincipal(): HasOne
    {
        return $this->hasOne(User::class)
            ->whereHas('role', fn ($q) => $q->where('nom', User::ROLE_ADMIN_ENTREPRISE));
    }

    /**
     * Generate a new unique entreprise ID.
     */
    public static function generateEntrepriseId(): string
    {
        $lastEntreprise = self::orderBy('id', 'desc')->first();
        $lastNumber = $lastEntreprise ? (int) substr($lastEntreprise->id_entreprise, 4) : 0;
        return 'ENT-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Determine if a real entreprise account is already registered.
     * A seeded placeholder entreprise without admin user should not block registration.
     */
    public static function hasRegisteredAccount(): bool
    {
        return User::query()
            ->entrepriseAdmins()
            ->exists();
    }
}
