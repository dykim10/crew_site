<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Generation;
use App\Models\User;
use App\Models\UserGeneration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * 신청 명단 → 기수·지부 편성.
 * 원장 = applications (generation_id, branch_id). 회원가입은 선택.
 * matched_user 가 있으면 user_generations + users.branch_id 동기화.
 */
class GenerationEnrollmentService
{
    /**
     * @param  Collection<int, Application>  $applications
     * @param  array<int, int>  $branchByApplicationId  application_id => branch_id (개별 지부 시)
     * @return array{enrolled:int, synced_users:int, skipped:int, errors:list<string>}
     */
    public function enrollMany(
        Collection $applications,
        int $generationId,
        ?int $commonBranchId,
        array $branchByApplicationId = [],
        bool $isCurrent = true,
    ): array {
        $enrolled = 0;
        $syncedUsers = 0;
        $skipped = 0;
        $errors = [];

        $generation = Generation::find($generationId);
        if (! $generation) {
            return [
                'enrolled'      => 0,
                'synced_users'  => 0,
                'skipped'       => $applications->count(),
                'errors'        => ['대상 기수를 찾을 수 없습니다.'],
            ];
        }

        foreach ($applications as $app) {
            $branchId = $branchByApplicationId[$app->id] ?? $commonBranchId;
            if (! $branchId) {
                $skipped++;
                $errors[] = "신청 #{$app->id}: 지부 미지정 — skip";
                continue;
            }

            $alreadySame = (int) $app->generation_id === $generationId
                && (int) $app->branch_id === (int) $branchId
                && $app->status === 'approved';

            $memo = trim((string) ($app->admin_memo ?? ''));
            $note = '입단이관 generation_id='.$generationId.' branch_id='.$branchId
                .' by='.(auth()->user()?->id ?? 'system');

            $app->update([
                'generation_id' => $generationId,
                'branch_id'     => $branchId,
                'status'        => 'approved',
                'enrolled_at'   => $app->enrolled_at ?? Carbon::now(),
                'admin_memo'    => $alreadySame
                    ? $app->admin_memo
                    : ($memo === '' ? $note : $memo."\n".$note),
            ]);

            if ($app->matched_user_id) {
                if ($this->syncUserMembership(
                    (int) $app->matched_user_id,
                    $generationId,
                    (int) $branchId,
                    $isCurrent,
                )) {
                    $syncedUsers++;
                }
            }

            if ($alreadySame) {
                $skipped++;
            } else {
                $enrolled++;
            }
        }

        return [
            'enrolled'     => $enrolled,
            'synced_users' => $syncedUsers,
            'skipped'      => $skipped,
            'errors'       => $errors,
        ];
    }

    /**
     * 신청에 이미 편성된 기수·지부가 있고 회원이 연결되면 UG 동기화.
     */
    public function syncFromApplication(Application $app, bool $isCurrent = true): bool
    {
        if (! $app->matched_user_id || ! $app->generation_id || ! $app->branch_id) {
            return false;
        }

        return $this->syncUserMembership(
            (int) $app->matched_user_id,
            (int) $app->generation_id,
            (int) $app->branch_id,
            $isCurrent,
        );
    }

    private function syncUserMembership(
        int $userId,
        int $generationId,
        int $branchId,
        bool $isCurrent,
    ): bool {
        $user = User::find($userId);
        if (! $user) {
            return false;
        }

        $existing = UserGeneration::where('user_id', $user->id)
            ->where('generation_id', $generationId)
            ->first();

        if ($isCurrent) {
            UserGeneration::where('user_id', $user->id)
                ->where('generation_id', '!=', $generationId)
                ->where('is_current', true)
                ->update(['is_current' => false]);
        }

        UserGeneration::updateOrCreate(
            ['user_id' => $user->id, 'generation_id' => $generationId],
            [
                'branch_id'  => $branchId,
                'joined_at'  => $existing?->joined_at ?? Carbon::today(),
                'is_current' => $isCurrent,
            ]
        );

        if ((int) $user->branch_id !== $branchId) {
            $user->update(['branch_id' => $branchId]);
        }

        return true;
    }
}
