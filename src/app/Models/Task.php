<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    public mixed $user_id;
    protected $fillable = [
        'user_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    public static function create(array $data)
    {
        $task = new self($data);
        $task->user_id = auth()->id(); // Postavljanje user_id na trenutno ulogovanog korisnika
        $task->save();

        return $task;
    }

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}