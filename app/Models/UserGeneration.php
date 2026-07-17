<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGeneration extends Model
{
    protected $table = 'crew.user_generations';

    protected $fillable = [
        'user_id',
        'generation_id',
        'branch_id',
        'joined_at',
        'is_current',
    ];

    protected function casts(): array
    {
        return [
            'joined_at'  => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
