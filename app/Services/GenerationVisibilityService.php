<?php

namespace App\Services;

use App\Models\Generation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/** 모집 중 OR 운영 중 기수 — GNB·공개 페이지 공통 */
class GenerationVisibilityService
{
    public const CACHE_KEY = 'crew_visible_generation_ids';

    /** @deprecated 구 키 — Eloquent 직렬화 Incomplete_Class 방지용 정리 */
    private const LEGACY_CACHE_KEY = 'crew_visible_generations';

    public function __construct(private GenerationRecruitmentService $recruitment) {}

    /** @return Collection<int, Generation> */
    public function visibleGenerations(): Collection
    {
        // ID만 캐시 — 모델 직렬화 시 __PHP_Incomplete_Class 회피
        $ids = Cache::remember(self::CACHE_KEY, 600, function () {
            $active = Generation::query()
                ->where('status', 'active')
                ->orderByDesc('number')
                ->pluck('id');

            $recruitingIds = Generation::query()
                ->whereIn('status', ['active', 'upcoming'])
                ->orderByDesc('number')
                ->get()
                ->filter(fn (Generation $g) => $this->isRecruiting($g))
                ->pluck('id');

            return $active->merge($recruitingIds)->unique()->values()->all();
        });

        if ($ids === [] || ! is_array($ids)) {
            return collect();
        }

        return Generation::query()
            ->whereIn('id', $ids)
            ->orderByDesc('number')
            ->get();
    }

    public function hasVisible(): bool
    {
        return $this->visibleGenerations()->isNotEmpty();
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::LEGACY_CACHE_KEY);
    }

    public function isRecruiting(Generation $generation): bool
    {
        if ($generation->status === 'ended') {
            return false;
        }

        if ($generation->apply_method === 'google_form') {
            if (! $generation->google_form_id) {
                return false;
            }
            $gf = $generation->relationLoaded('googleForm')
                ? $generation->googleForm
                : $generation->googleForm()->first();

            if ($gf === null || ! $gf->is_active) {
                return false;
            }

            // 연결된 신청서 폼이 있으면 모집 기간(open_from~open_until + is_active)도 충족해야 함
            // (구글폼만 활성이라 기간 종료 후에도 접수되던 문제 방지)
            $appForm = $this->recruitment->resolveApplicationForm($generation);
            if ($appForm) {
                return $appForm->isOpen();
            }

            return true;
        }

        // 직접 신청서: 연결된 폼이 실제로 열려 있을 때만
        return $this->recruitment->isFormOpen($generation);
    }
}
