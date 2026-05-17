<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class RunningLog extends Model
{
    protected $table = 'crew.running_logs';

    protected $fillable = [
        'user_id',
        'group_id',
        'run_date',
        'distance_km',
        'duration_seconds',
        'avg_pace_seconds',
        'best_pace_seconds',
        'is_indoor',
        'calories',
        'avg_heart_rate',
        'elevation_m',
        'weather_desc',
        'image_url',
        'parsed_data',
        'memo',
    ];

    protected function casts(): array
    {
        return [
            'run_date'    => 'date',
            'is_indoor'   => 'boolean',
            'parsed_data' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // avg_pace_seconds → "5'30\"" 형식으로 변환
    protected function avgPaceFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->avg_pace_seconds) return null;
                $m = intdiv($this->avg_pace_seconds, 60);
                $s = $this->avg_pace_seconds % 60;
                return sprintf("%d'%02d\"", $m, $s);
            }
        );
    }

    // duration_seconds → "1:23:45" 형식으로 변환
    protected function durationFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->duration_seconds) return null;
                $h = intdiv($this->duration_seconds, 3600);
                $m = intdiv($this->duration_seconds % 3600, 60);
                $s = $this->duration_seconds % 60;
                return $h > 0
                    ? sprintf('%d:%02d:%02d', $h, $m, $s)
                    : sprintf('%d:%02d', $m, $s);
            }
        );
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByGroup($query, int $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    public static function totalKmByUser(int $userId, int $year, ?int $month = null): float
    {
        $query = static::where('user_id', $userId)
            ->whereYear('run_date', $year);
        if ($month) {
            $query->whereMonth('run_date', $month);
        }
        return (float) $query->sum('distance_km');
    }
}
