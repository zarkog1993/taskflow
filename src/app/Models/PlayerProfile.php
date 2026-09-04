<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'jersey_number',
        'preferred_foot',
        'primary_position',
        'date_of_birth',
        'fitness_status',
        'category',
        'seniority',
        'medical_notes',
    ];
}
