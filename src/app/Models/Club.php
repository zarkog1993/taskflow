<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'logo_url',
        'address',
        'city',
        'country',
        'phone',
        'onboarding_token_hash',
        'onboarding_token_expires_at',
        'onboarding_completed_at',
    ];

    protected $casts = [
        'onboarding_token_expires_at' => 'datetime',
        'onboarding_completed_at' => 'datetime',
    ];

    // Timovi koji pripadaju klubu
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    // Korisnici koji pripadaju klubu
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Vlasnik / Admin kluba (korisnik sa ulogom club-admin)
    public function owner(): HasOne
    {
        return $this->hasOne(User::class)->whereHas('roles', function ($q) {
            $q->where('slug', 'club-admin');
        });
    }

    // Pretplata kluba (preko vlasnika kluba)
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }
}