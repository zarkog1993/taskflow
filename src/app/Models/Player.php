<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    protected static function booted(): void
    {
        static::addGlobalScope('club_isolation', function (Builder $builder) {
            if (Auth::check() && !Auth::user()->isSuperAdmin()) {
                // Ako je ulogovan Admin kluba, vidi samo igrače svog kluba
                $builder->where('club_id', Auth::user()->club_id);
            }
        });

        // Prilikom kreiranja igrača, automatski mu postavljamo club_id ulogovanog admina
        static::creating(function ($player) {
            if (Auth::check() && !Auth::user()->isSuperAdmin() && !$player->club_id) {
                $player->club_id = Auth::user()->club_id;
            }
        });
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    public function matches(): BelongsToMany
    {
        return $this->belongsToMany(GameMatch::class, 'match_player')
            ->withPivot(['attended', 'goals', 'assists'])
            ->withTimestamps();
    }
}