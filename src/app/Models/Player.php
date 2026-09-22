<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'email',
        'photo_path',
        'primary_position',
        'seniority',
        'jersey_number',
        'height',
        'weight',
        'date_of_birth',
        'preferred_foot',
        'physical_status',
        'medical_notes',
        'coach_notes',
        'matches_played',
        'trainings_attended',
        'goals',
        'assists',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'height' => 'integer',
        'weight' => 'integer',
        'jersey_number' => 'integer',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function matches(): BelongsToMany
    {
        return $this->belongsToMany(GameMatch::class, 'match_player')
            ->withPivot(['attended', 'goals', 'assists'])
            ->withTimestamps();
    }
}