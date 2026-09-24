<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'club_id', // <-- Dodato
        'title',
        'scheduled_at',
        'location',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attendance')->withPivot('status')->withTimestamps();
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'training_user', 'training_session_id', 'user_id')
            ->withPivot('attended')
            ->withTimestamps();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}