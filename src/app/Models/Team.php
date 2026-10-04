<?php

namespace App\Models;

use App\Models\Traits\BelongsToAcademy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Team extends Model
{
    use HasFactory, BelongsToAcademy;

    protected $fillable = ['academy_id', 'name', 'age_group', 'club_id'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Relacija ka novom Player modelu
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public function tactic(): HasOne
    {
        return $this->hasOne(TeamTactic::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
