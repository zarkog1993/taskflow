<?php

namespace App\Models;

use App\Models\Traits\BelongsToAcademy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory, BelongsToAcademy;

    protected $fillable = ['academy_id', 'name', 'age_group'];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
