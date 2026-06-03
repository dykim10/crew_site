<?php

namespace App\Services;

use App\Models\EventGroup;
use App\Models\EventGroupMember;
use App\Models\Generation;
use App\Models\UserGeneration;
use Illuminate\Support\Facades\DB;

class EventGroupService
{
    /**
     * 지부별 조 편성 데이터를 저장한다.
     *
     * @param int $eventId
     * @param int $branchId
     * @param array $groups  [['group_name' => '1조', 'members' => [userId, ...]]]
     */
    public function saveGroups(int $eventId, int $branchId, array $groups): void
    {
        try {
            DB::transaction(function () use ($eventId, $branchId, $groups) {

                // 해당 이벤트+지부의 기존 조 편성을 일괄 삭제 후 재저장
                $existingGroupIds = EventGroup::where('event_id', $eventId)
                    ->where('branch_id', $branchId)
                    ->pluck('id');

                EventGroupMember::whereIn('group_id', $existingGroupIds)->delete();
                EventGroup::whereIn('id', $existingGroupIds)->delete();

                $generationId = $this->resolveGenerationId($eventId);

                foreach ($groups as $index => $groupData) {
                    $groupNo   = $index + 1;
                    $memberIds = $groupData['members'] ?? [];

                    $group = EventGroup::create([
                        'crew_id'        => 1,
                        'generation_id'  => $generationId,
                        'branch_id'      => $branchId,
                        'event_id'       => $eventId,
                        'group_no'       => $groupNo,
                        'group_name'     => $groupData['group_name'] ?? "{$groupNo}조",
                        'leader_user_id' => $memberIds[0] ?? null,
                    ]);

                    foreach ($memberIds as $userId) {
                        EventGroupMember::create([
                            'crew_id'  => 1,
                            'group_id' => $group->id,
                            'user_id'  => $userId,
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('EventGroupService::saveGroups 실패', [
                'event_id'  => $eventId,
                'branch_id' => $branchId,
                'error'     => $e->getMessage(),
            ]);
            throw new \RuntimeException('조 편성 저장에 실패했습니다. 잠시 후 다시 시도해 주세요.');
        }
    }

    /**
     * 이벤트에 설정된 기수의 generation_id를 반환한다.
     */
    private function resolveGenerationId(int $eventId): int
    {
        $event = \App\Models\Event::find($eventId);
        if (!$event || !$event->generation) return 0;

        $gen = Generation::where('number', $event->generation)->first();
        return $gen ? $gen->id : 0;
    }

    /**
     * 이벤트+지부 기준으로 미배정 구성원 목록을 반환한다.
     * (기수에 속하면서 해당 지부이고, 아직 어느 조에도 없는 사용자)
     */
    public function getUnassignedMembers(int $eventId, int $branchId): \Illuminate\Support\Collection
    {
        $event = \App\Models\Event::find($eventId);
        if (!$event || !$event->generation) return collect();

        $gen = Generation::where('number', $event->generation)->first();
        if (!$gen) return collect();

        // 기수 구성원 중 해당 지부 소속
        $allUserIds = UserGeneration::where('generation_id', $gen->id)
            ->pluck('user_id');

        $users = \App\Models\User::whereIn('id', $allUserIds)
            ->where('branch_id', $branchId)
            ->get(['id', 'name', 'branch_id']);

        // 이미 배정된 user_id 제외
        $assignedUserIds = EventGroupMember::whereHas('group', fn ($q) =>
            $q->where('event_id', $eventId)->where('branch_id', $branchId)
        )->pluck('user_id')->toArray();

        return $users->filter(fn ($u) => !in_array($u->id, $assignedUserIds))->values();
    }

    /**
     * 이벤트+지부 기준 현재 조 편성 현황을 반환한다.
     */
    public function getGroups(int $eventId, int $branchId): \Illuminate\Support\Collection
    {
        return EventGroup::with('members.user')
            ->where('event_id', $eventId)
            ->where('branch_id', $branchId)
            ->orderBy('group_no')
            ->get();
    }
}
