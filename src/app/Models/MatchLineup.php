<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchLineup extends Model
{
    protected $fillable = [
        'formation',
        'positions',
        'bench_player_ids',
    ];

    protected function casts(): array
    {
        return [
            'positions' => 'array',
            'bench_player_ids' => 'array',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchDay::class, 'match_day_id');
    }
}
