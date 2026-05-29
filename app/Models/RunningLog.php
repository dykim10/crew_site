<?php

namespace App\Models;

use App\Services\RunningLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * 러닝 기록 모델 (app/Models/RunningLog.php)
 *
 * crew.running_logs 테이블과 매핑 (Supabase crew 스키마 사용).
 *
 * [상태 플래그]
 *   is_confirmed = false : 이미지 파싱 직후 임시 저장 (draft) — 마일리지 집계 제외
 *   is_confirmed = true  : 사용자 또는 관리자가 확정한 기록 — 마일리지 집계 대상
 *
 * [시간 단위 규칙] — 모든 시간 값은 초(seconds) 단위로 DB 저장
 *   duration_seconds  : 운동 총 시간
 *   avg_pace_seconds  : 평균 페이스 (1km 당 초)
 *   best_pace_seconds : 최고 페이스
 *
 * [Accessor — 화면 표시용 포맷 변환]
 *   avg_pace_formatted : avg_pace_seconds  → "5'30\"" 형식
 *   duration_formatted : duration_seconds  → "1:23:45" 형식
 *   → new-style Attribute::make() 방식 사용 (Laravel 9+)
 *
 * [Scope]
 *   byUser($userId)    : 특정 사용자의 기록만 필터
 *   byGroup($groupId)  : 특정 그룹의 기록만 필터
 *
 * [Static 집계 메서드]
 *   totalKmByUser(userId, year, ?month) : 연간 또는 월간 누적 거리 합산
 */
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
        'is_confirmed',
    ];

    protected function casts(): array
    {
        return [
            'run_date'     => 'date',
            'is_indoor'    => 'boolean',
            'is_confirmed' => 'boolean',
            'parsed_data'  => 'array',
        ];
    }

    // 삭제 시 S3 이미지 자동 정리 — Controller/Filament 단건/일괄 모든 경로 커버
    protected static function booted(): void
    {
        static::deleting(function (RunningLog $log) {
            $rawUrl = $log->getRawOriginal('image_url');
            if ($rawUrl) {
                app(RunningLogService::class)->deleteS3Image($rawUrl);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // S3 직접 URL → CloudFront CDN URL 변환 (AWS_URL 설정 시 자동 적용)
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;
                $cdnBase = rtrim(config('filesystems.disks.s3.url', ''), '/');
                if (!$cdnBase) return $value;

                $bucket = config('filesystems.disks.s3.bucket', '');
                // "https://bucket.s3.region.amazonaws.com/key" 또는
                // "https://s3.region.amazonaws.com/bucket/key" 패턴 처리
                if (preg_match('#amazonaws\.com(?:/' . preg_quote($bucket, '#') . ')?/(.+)$#', $value, $m)) {
                    return $cdnBase . '/' . $m[1];
                }
                return $value;
            }
        );
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
