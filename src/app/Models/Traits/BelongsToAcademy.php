<?php

namespace App\Models\Traits;

use App\Models\Academy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToAcademy
{
    protected static function bootBelongsToAcademy(): void
    {
        // Globalni scope: Vraćaj samo podatke koji pripadaju akademiji ulogovanog korisnika
        static::addGlobalScope('academy', function (Builder $builder) {
            if (auth()->check() && auth()->user()->academy_id) {
                $builder->where('academy_id', auth()->user()->academy_id);
            }
        });

        // Event listener: Automatski postavi academy_id pri kreiranju zapisa
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->academy_id && !$model->academy_id) {
                $model->academy_id = auth()->user()->academy_id;
            }
        });
    }

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academy::class);
    }
}
