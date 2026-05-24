<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 개인 목표 모델 (app/Models/UserGoal.php)
 *
 * crew.user_goals 테이블과 매핑.
 * 구성원이 설정한 연간/월간 러닝 목표 거리를 관리하며,
 * 확정 기록 추가·취소 시 RunningLogService::recalculateGoals() 가 자동으로 재집계한다.
 *
 * [목표 기간 구분]
 *   month = NULL  → 연간 목표 (scopeYearly 로 조회)
 *   month = 1~12  → 월간 목표 (scopeMonthly 로 조회)
 *
 * [주요 컬럼]
 *   target_km   : 목표 거리 (km)
 *   achieved_km : 달성 거리 — 직접 수정 금지, recalculateGoals() 를 통해서만 갱신
 *   is_achieved : 달성 완료 플래그 (achieved_km >= target_km 일 때 true)
 *
 * [Scope]
 *   yearly(userId, year)          : 연간 목표 조회 (month IS NULL)
 *   monthly(userId, year, month)  : 월간 목표 조회
 */
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
