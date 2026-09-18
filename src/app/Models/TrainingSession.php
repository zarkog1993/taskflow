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
        'title',
        'scheduled_at',
        'location',
        'created_by',
    ];

    // Obavezno pretvaranje u Carbon/DateTime objekat
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

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

    /**
     * Igrači koji prisustvuju treningu.
     */
    public function users(): BelongsToMany
    {
        // Koristimo 'training_user' kao naziv pivot tabele
        return $this->belongsToMany(User::class, 'training_user', 'training_session_id', 'user_id')
            ->withPivot('attended')
            ->withTimestamps();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
