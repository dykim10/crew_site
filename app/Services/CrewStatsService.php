<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Event;
use App\Models\RunningLog;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CrewStatsService
{
    /** 공개 페이지 표시용 (캐시 10분) */
    public function getPublicStats(): array
    {
        return Cache::remember('crew_public_stats', 600, fn () => $this->buildPublicStats());
    }

    public function forgetCache(): void
    {
        Cache::forget('crew_public_stats');
    }

    /** Filament override helper text 등 원시값 */
    public function getRawStats(): array
    {
        return [
            'runners'  => $this->countRunners(),
            'branches' => $this->countActiveBranches(),
            'events'   => $this->countEvents(),
            'total_km' => $this->sumTotalKm(),
        ];
    }

    private function buildPublicStats(): array
    {
        return [
            'runners'  => number_format($this->countRunners()),
            'branches' => (string) $this->countActiveBranches(),
            'events'   => (string) $this->countEvents(),
            'total_km' => number_format((int) round($this->sumTotalKm())) . 'km',
        ];
    }

    /** crew 소속 활성 회원 (탈퇴 시 users row 삭제) */
    public function countRunners(): int
    {
        return User::query()->whereNotNull('crew_id')->count();
    }

    public function countActiveBranches(): int
    {
        return Branch::query()->where('status', 'active')->count();
    }

    /** 등록된 이벤트 전체 (status 무관) */
    public function countEvents(): int
    {
        return Event::query()->count();
    }

    /** 확정(is_confirmed) 러닝 기록 거리 합계(km) */
    public function sumTotalKm(): float
    {
        return (float) RunningLog::query()
            ->where('is_confirmed', true)
            ->sum('distance_km');
    }
}
