<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'club_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ✅ Postavljamo podrazumevanu vrednost za is_admin
    protected $attributes = [
        'is_admin' => false,
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole(string $roleSlug): bool
    {
        // Proverava i preko is_admin polja ili kroz uloge u pivot tabeli
        if ($roleSlug === 'admin' && $this->is_admin) {
            return true;
        }

        return $this->roles->contains('slug', $roleSlug);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function hasPermission(string $permissionSlug): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('slug', $permissionSlug)) {
                return true;
            }
        }
        return false;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function playerProfile(): HasOne
    {
        return $this->hasOne(PlayerProfile::class);
    }

    public function createdSessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Ekipe kojima korisnik/trener/igrač pripada.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function trainingSessions(): BelongsToMany
    {
        return $this->belongsToMany(TrainingSession::class, 'training_user', 'user_id', 'training_session_id')
            ->withPivot('attended')
            ->withTimestamps();
    }

    /**
     * Relacija sa klubom kom korisnik pripada.
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Provera da li je korisnik Super Admin (glavni admin aplikacije).
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_admin
            || $this->roles()->whereIn('slug', ['admin', 'super-admin'])->exists();
    }

    /**
     * Provera da li je korisnik Admin konkretnog kluba.
     */
    public function isClubAdmin(): bool
    {
        return $this->roles()->where('slug', 'club-admin')->exists()
            || $this->hasRole('club-admin');
    }

    // Unutar User klase:
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription?->isActive() ?? false;
    }

    public function hasFeature(string $feature): bool
    {
        return $this->hasActiveSubscription() && $this->subscription->hasFeature($feature);
    }
}
