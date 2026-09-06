<?php

namespace App\Models\Traits;

use App\Models\Academy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToAcademy
{
    protected static function bootBelongsToAcademy(): void
    {
        // Globalni scope: Admin vidi sve timove, ostali vide samo timove svoje akademije
        static::addGlobalScope('academy', function (Builder $builder) {
            if (auth()->check() && !auth()->user()->hasRole('admin') && auth()->user()->academy_id) {
                $builder->where('academy_id', auth()->user()->academy_id);
            }
        });

        // Pri kreiranju zapisa automatski dodeljujemo academy_id
        static::creating(function ($model) {
            if (!$model->academy_id) {
                // Ako korisnik ima academy_id koristi ga, u suprotnom uzima prvu dostupnu akademiju
                $academyId = auth()->user()?->academy_id ?? Academy::first()?->id;

                // Ako akademija uopšte ne postoji u bazi, kreira podrazumevanu
                if (!$academyId) {
                    $defaultAcademy = Academy::firstOrCreate(
                        ['slug' => 'default-academy'],
                        ['name' => 'Glavna Akademija']
                    );
                    $academyId = $defaultAcademy->id;
                }

                $model->academy_id = $academyId;
            }
        });
    }

    public function academy(): BelongsTo
    {
        return $this->belongsTo(Academy::class);
    }
}