<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGoal extends Model
{
    protected $table = 'crew.user_goals';

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'target_km',
        'achieved_km',
        'is_achieved',
    ];

    protected function casts(): array
    {
        return [
            'is_achieved' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeYearly($query, int $userId, int $year)
    {
        return $query->where('user_id', $userId)->where('year', $year)->whereNull('month');
    }

    public function scopeMonthly($query, int $userId, int $year, int $month)
    {
        return $query->where('user_id', $userId)->where('year', $year)->where('month', $month);
    }
}
