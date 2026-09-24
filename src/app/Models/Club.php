<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Club extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

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
    public function subscription(): HasOneThrough
    {
        return $this->hasOneThrough(
            Subscription::class,
            User::class,
            'club_id', // Strani ključ u users tabeli
            'user_id', // Strani ključ u subscriptions tabeli
            'id',      // Lokalni ključ u clubs tabeli
            'id'       // Lokalni ključ u users tabeli
        );
    }
}