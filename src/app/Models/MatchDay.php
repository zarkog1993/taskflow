<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class MatchDay extends Model
{
    protected $table = 'match_days';

    protected $fillable = [
        'team_id',
        'opponent',
        'is_home',
        'scheduled_at',
        'location',
        'status',
        'home_score',
        'away_score',
        'notes',
    ];

    protected $casts = [
        'is_home' => 'boolean',
        'scheduled_at' => 'datetime',
        'home_score' => 'integer',
        'away_score' => 'integer',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Sastav i učinak igrača na utakmici (zapisnik).
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'match_day_player')
                    ->withPivot('attended', 'goals', 'assists')
                    ->withTimestamps();
    }

    /**
     * Igrači pozvani na događaj, sa njihovim RSVP odgovorom.
     */
    public function invitedPlayers(): MorphToMany
    {
        return $this->morphToMany(Player::class, 'invitable', 'event_invitations')
            ->withPivot('status', 'responded_at')
            ->withTimestamps();
    }
}
