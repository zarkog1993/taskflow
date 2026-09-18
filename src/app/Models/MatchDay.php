<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'match_day_user')
                    ->withPivot('attended', 'goals', 'assists')
                    ->withTimestamps();
    }
}
