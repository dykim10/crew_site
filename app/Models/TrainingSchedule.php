<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingSchedule extends Model
{
    protected $table = 'crew.training_schedules';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'week_start',
        'point_workout',
        'weekly_volume',
        'rationale',
        'matched_log_id',
        'evaluation',
    ];

    protected function casts(): array
    {
        return [
            'week_start'     => 'date',
            'point_workout'  => 'array',
            'weekly_volume'  => 'array',
            'evaluation'     => 'array',
            'created_at'     => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function matchedLog()
    {
        return $this->belongsTo(RunningLog::class, 'matched_log_id');
    }
}
