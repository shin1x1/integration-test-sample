<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'is_completed',
        'assigned_user_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'is_completed' => 'boolean',
    ];

    public static function hasIncompleteTaskAssigned(int $userId): bool
    {
        return self::query()
            ->where('assigned_user_id', $userId)
            ->where('is_completed', false)
            ->exists();
    }
}
