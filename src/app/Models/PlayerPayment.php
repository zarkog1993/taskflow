<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'team_id',
        'player_id',
        'type',
        'period',
        'amount',
        'status',
        'paid_at',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Relacija sa klubom kom pripadaju uplate.
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Relacija sa ekipom (timom).
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Relacija sa igračem na koga se odnosi uplata/isplata.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }
}
