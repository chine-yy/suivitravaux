<?php

namespace App\Models;

use App\Support\PermissionSlugResolver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 'Super Admin';
    public const ROLE_ADMIN_ENTREPRISE = 'Administrateur Entreprise';

    protected static ?bool $hasTypeCompteColumn = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'telephone',
        'photo',
        'password',
        'role_id',
        'type_compte',
        'is_active',
        'projet_id',
        'chef_equipe_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $user): void {
            $user->syncTypeCompteFromRole();
        });

        static::deleting(function (self $user): void {
            if (method_exists($user, 'projetsSuivis')) {
                $user->projetsSuivis()->detach();
            }
            Projet::where('partenaire_id', $user->id)->update(['partenaire_id' => null]);
        });
    }

    public function scopeEntrepriseAdmins(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereHas('role', function ($roleQuery): void {
                $roleQuery->where('nom', self::ROLE_ADMIN_ENTREPRISE);
            });

            if (self::hasTypeCompteColumn()) {
                $q->orWhere('type_compte', 'admin');
            }
        });
    }

    public function scopeNonEntrepriseAdmins(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereDoesntHave('role', function ($roleQuery): void {
                $roleQuery->where('nom', self::ROLE_ADMIN_ENTREPRISE);
            });

            if (self::hasTypeCompteColumn()) {
                $q->where(function ($sub) {
                    $sub->whereNotIn('type_compte', ['admin', 'admin_entreprise', 'administrateur_entreprise'])
                        ->orWhereNull('type_compte');
                });
            }
        });
    }

    public function scopeSuperAdmins(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereHas('role', function ($roleQuery): void {
                $roleQuery->where('nom', self::ROLE_SUPER_ADMIN);
            });

            if (self::hasTypeCompteColumn()) {
                $q->orWhere('type_compte', 'super_admin');
                $q->orWhere('type_compte', 'superadmin');
                $q->orWhere('type_compte', 'super-admin');
            }
        });
    }

    public function scopeNonSuperAdmins(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereDoesntHave('role', function ($roleQuery): void {
                $roleQuery->whereIn('nom', [self::ROLE_SUPER_ADMIN]);
            });

            if (self::hasTypeCompteColumn()) {
                $q->where(function ($typeQuery) {
                    $typeQuery->whereNotIn('type_compte', ['super_admin', 'superadmin', 'super-admin'])
                             ->orWhereNull('type_compte');
                });
            }
        });
    }

    public function scopePartenaires(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            if (self::hasTypeCompteColumn()) {
                $q->where('type_compte', 'partenaire');
            }
        });
    }

    public function scopeNonPartenaires(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            if (self::hasTypeCompteColumn()) {
                $q->where(function ($sub) {
                    $sub->whereNotIn('type_compte', ['partenaire'])
                        ->orWhereNull('type_compte');
                });
            }
            $q->whereDoesntHave('role', function ($roleQuery): void {
                $roleQuery->where('nom', 'Partenaire');
            });
        });
    }

    public function scopeMembres(Builder $query): Builder
    {
        return $query->where(function (Builder $q): void {
            $q->whereDoesntHave('role', function ($roleQuery): void {
                $roleQuery->whereIn('nom', [self::ROLE_SUPER_ADMIN, 'Partenaire', 'Administration']);
            });

            if (self::hasTypeCompteColumn()) {
                $q->whereNotIn('type_compte', ['super_admin', 'superadmin', 'super-admin', 'partenaire']);
            }

            $q->whereNotNull('role_id');
        });
    }

    private static function hasTypeCompteColumn(): bool
    {
        if (self::$hasTypeCompteColumn === null) {
            self::$hasTypeCompteColumn = Schema::hasColumn('users', 'type_compte');
        }

        return self::$hasTypeCompteColumn;
    }

    private function syncTypeCompteFromRole(): void
    {
        if (!self::hasTypeCompteColumn()) {
            return;
        }

        $roleNom = $this->relationLoaded('role')
            ? $this->role?->nom
            : ($this->role_id ? Role::query()->whereKey($this->role_id)->value('nom') : null);

        if ($roleNom === self::ROLE_SUPER_ADMIN) {
            $this->attributes['type_compte'] = 'super_admin';
            return;
        }

        if ($roleNom === 'Partenaire') {
            $this->attributes['type_compte'] = 'partenaire';
            return;
        }

        if ($roleNom === self::ROLE_ADMIN_ENTREPRISE) {
            $this->attributes['type_compte'] = 'admin';
            return;
        }

        if ($this->role_id !== null) {
            // Rôle dynamique
            $this->attributes['type_compte'] = 'role_personnalise';
        }
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'projet_id');
    }

    /**
     * Projets suivis par ce partenaire (via projets.partenaire_id).
     */
    public function projets()
    {
        return $this->hasMany(Projet::class, 'partenaire_id');
    }

    /**
     * Projets liés au partenaire via la table Many-to-Many projet_partenaires.
     */
    public function projetsSuivis()
    {
        return $this->belongsToMany(Projet::class, 'projet_partenaires', 'user_id', 'projet_id')->withTimestamps();
    }

    /**
     * Get the user's role.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the entreprise the user belongs to.
     */
    public function entreprise()
    {
        return $this->belongsTo(Entreprise::class, 'entreprise_id');
    }

    /**
     * Get the user's permissions through role.
     */
    public function permissions()
    {
        if (!$this->role) {
            return collect();
        }
        return $this->role->permissions()->get();
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return ($this->role?->nom === self::ROLE_SUPER_ADMIN)
            || (($this->type_compte ?? null) === 'super_admin')
            || (($this->type_compte ?? null) === 'superadmin')
            || (($this->type_compte ?? null) === 'super-admin');
    }

    /**
     * Check if user is Admin Entreprise.
     */
    public function isAdminEntreprise(): bool
    {
        return ($this->type_compte ?? null) === 'admin'
            || ($this->role?->nom === self::ROLE_ADMIN_ENTREPRISE)
            || ($this->role_id && $this->role?->nom === self::ROLE_ADMIN_ENTREPRISE);
    }

    public function isPartenaire(): bool
    {
        return ($this->type_compte ?? null) === 'partenaire'
            || ($this->role?->nom === 'Partenaire')
            || ($this->role_id && $this->role?->nom === 'Partenaire');
    }

    /**
     * Check if user has specific permission.
     */
    public function hasPermission($permission)
    {
        if ($this->role_id === null) {
            return false;
        }

        $roleNom = $this->role->nom ?? null;
        if ($roleNom === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        return PermissionSlugResolver::matches((string) $permission, $this->permissions()->pluck('slug')->all());
    }

    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->role_id === null) {
            return false;
        }

        $roleNom = $this->role->nom ?? null;
        if ($roleNom === self::ROLE_SUPER_ADMIN) {
            return true;
        }

        $userSlugs = $this->permissions()->pluck('slug')->all();

        foreach ($permissions as $permission) {
            if (PermissionSlugResolver::matches((string) $permission, $userSlugs)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the équipes of the user.
     */
    public function equipes()
    {
        return $this->belongsToMany(Equipe::class, 'equipe_user');
    }

    /**
     * Centralized profile photo URL logic.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        if (filter_var($this->photo, FILTER_VALIDATE_URL)) {
            return $this->photo;
        }

        $normalized = ltrim($this->photo, '/');

        // Check via Storage disk (most reliable, doesn't depend on symlink)
        if (Storage::disk('public')->exists($normalized)) {
            return asset('storage/' . $normalized);
        }

        $basename = basename($normalized);
        if ($basename !== $normalized && Storage::disk('public')->exists($basename)) {
            return asset('storage/' . $basename);
        }

        if (Storage::disk('public')->exists('photos/' . $normalized)) {
            return asset('storage/photos/' . $normalized);
        }

        // Fallback: check public directory directly
        $candidates = [
            $normalized,
            'storage/' . $normalized,
            'storage/photos/' . $normalized,
            'uploads/profil-images/' . $basename,
            'storage/uploads/profil-images/' . $basename,
            'storage/uploads/profil-images/' . $normalized,
        ];

        foreach ($candidates as $candidate) {
            if (file_exists(public_path($candidate))) {
                return asset($candidate);
            }
        }

        return null;
    }
}
