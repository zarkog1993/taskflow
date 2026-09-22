<?php

namespace App\Models;

use App\Models\Traits\BelongsToAcademy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory, BelongsToAcademy;

    protected $fillable = ['academy_id', 'name', 'age_group'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the upcoming matches for the team.
     *
     * @return HasMany
     */
    public function upcomingMatches(): HasMany
    {
        return $this->hasMany(TrainingSession::class)
            ->where('type', 'match') // Pretpostavka da je tip događaja 'match'
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at', 'asc')
            ->with(['attendees.playerProfile']);
    }

    /**
     * Relacija ka novom Player modelu
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }
}
