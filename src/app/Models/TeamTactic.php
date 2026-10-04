<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamTactic extends Model
{
    protected $fillable = [
        'formation',
        'positions',
    ];

    protected function casts(): array
    {
        return [
            'positions' => 'array',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
