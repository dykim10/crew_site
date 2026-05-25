<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGeneration extends Model
{
    protected $table = 'crew.user_generations';

    protected $fillable = [
        'user_id',
        'generation_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generation()
    {
        return $this->belongsTo(Generation::class);
    }
}
