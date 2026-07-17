<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationForm;
use App\Models\Branch;
use Illuminate\Support\Collection;

class ApplicationFormBranchService
{
    /**
     * @return list<array{branch_id:int,is_active:bool,max_applicants:?int}>
     */
    public function normalizedSettings(ApplicationForm $form): array
    {
        $rows = [];
        foreach ($form->branch_settings ?? [] as $row) {
            $branchId = isset($row['branch_id']) ? (int) $row['branch_id'] : 0;
            if ($branchId <= 0) {
                continue;
            }
            $max = $row['max_applicants'] ?? null;
            $rows[] = [
                'branch_id'      => $branchId,
                'is_active'      => (bool) ($row['is_active'] ?? false),
                'max_applicants' => $max === null || $max === '' ? null : (int) $max,
            ];
        }

        return $rows;
    }

    /** 정원 집계에 쓰는 신청 (거절 제외) */
    public function countForBranch(ApplicationForm $form, int $branchId): int
    {
        return Application::query()
            ->where('form_id', $form->id)
            ->where('preferred_branch_id', $branchId)
            ->where('status', '!=', 'rejected')
            ->count();
    }

    /**
     * 공개 폼용 — 활성 지부 + 현재 인원/마감 여부.
     *
     * @return Collection<int, object{branch:Branch,count:int,max:?int,is_full:bool,is_selectable:bool}>
     */
    public function selectableBranches(ApplicationForm $form): Collection
    {
        $settings = collect($this->normalizedSettings($form))
            ->filter(fn (array $r) => $r['is_active'])
            ->keyBy('branch_id');

        if ($settings->isEmpty()) {
            return collect();
        }

        $branches = Branch::query()
            ->whereIn('id', $settings->keys())
            ->where('status', 'active')
            ->orderBy('name')
            ->get()
            ->keyBy('id');

        return $settings
            ->map(function (array $row) use ($form, $branches) {
                $branch = $branches->get($row['branch_id']);
                if (! $branch) {
                    return null;
                }
                $count = $this->countForBranch($form, $row['branch_id']);
                $max = $row['max_applicants'];
                $isFull = $max !== null && $count >= $max;

                return (object) [
                    'branch'         => $branch,
                    'count'          => $count,
                    'max'            => $max,
                    'is_full'        => $isFull,
                    'is_selectable'  => ! $isFull,
                ];
            })
            ->filter()
            ->values();
    }

    public function assertPreferredBranchAllowed(ApplicationForm $form, int $branchId): ?string
    {
        $match = collect($this->normalizedSettings($form))
            ->first(fn (array $r) => $r['branch_id'] === $branchId && $r['is_active']);

        if (! $match) {
            return '선택할 수 없는 지부입니다.';
        }

        $branch = Branch::query()->where('id', $branchId)->where('status', 'active')->first();
        if (! $branch) {
            return '선택할 수 없는 지부입니다.';
        }

        $max = $match['max_applicants'];
        if ($max !== null && $this->countForBranch($form, $branchId) >= $max) {
            return '선택한 지부는 모집이 마감되었습니다. 다른 지부를 선택해주세요.';
        }

        return null;
    }

    /** @return list<string> 공개 URL */
    public function imageUrls(ApplicationForm $form): array
    {
        $urls = [];
        foreach ($form->images ?? [] as $path) {
            $path = is_string($path) ? trim($path) : '';
            if ($path === '') {
                continue;
            }
            $urls[] = Branch::resolveImageUrl($path) ?? $path;
        }

        return array_values(array_filter($urls));
    }

    /** Filament 저장 전 — S3 key 정규화, 최대 10장 */
    public function normalizeImages(mixed $images): array
    {
        if (! is_array($images)) {
            return [];
        }

        $out = [];
        foreach ($images as $item) {
            $path = Branch::normalizeStoragePath(is_string($item) ? $item : null);
            if ($path) {
                $out[] = $path;
            }
            if (count($out) >= 10) {
                break;
            }
        }

        return array_values(array_unique($out));
    }
}
