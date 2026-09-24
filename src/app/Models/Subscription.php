<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_id',
        'plan_type',
        'status',
        'max_teams',
        'max_players',
        'trial_ends_at',
        'ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at'       => 'datetime',
    ];

    /**
     * Vlasnik pretplate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
