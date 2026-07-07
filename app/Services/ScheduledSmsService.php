<?php

namespace App\Services;

use App\Models\ScheduledSms;
use App\Models\ScheduledSmsRecipient;
use App\Models\User;
use App\Models\UsersDetail;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScheduledSmsService
{
    /** @return array<int, string> user_id => nickname */
    public function recipientOptions(
        ?int $generationId = null,
        ?int $regionId = null,
        ?string $trainingGroup = null,
    ): array {
        return $this->buildRecipientQuery($generationId, $regionId, $trainingGroup)
            ->orderBy('nickname')
            ->pluck('nickname', 'id')
            ->all();
    }

    /** @return list<int> */
    public function resolveUserIds(
        ?int $generationId = null,
        ?int $regionId = null,
        ?string $trainingGroup = null,
    ): array {
        return $this->buildRecipientQuery($generationId, $regionId, $trainingGroup)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    private function buildRecipientQuery(
        ?int $generationId,
        ?int $regionId,
        ?string $trainingGroup,
    ) {
        $query = User::query()
            ->where('role', 'member')
            ->whereHas('detail');

        if ($generationId) {
            $query->whereHas('userGenerations', fn ($q) => $q->where('generation_id', $generationId));
        }

        if ($regionId) {
            $query->whereHas('detail', fn ($q) => $q->where('region_id', $regionId));
        }

        if ($trainingGroup !== null && $trainingGroup !== '') {
            $query->whereHas('detail', fn ($q) => $q->where('training_group', $trainingGroup));
        }

        return $query->select('users.id', 'users.nickname');
    }

    public function create(array $data, array $userIds, int $adminId): ScheduledSms
    {
        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $this->assertSchedulable($scheduledAt);
        $userIds = $this->normalizeUserIds($userIds);

        return DB::transaction(function () use ($data, $userIds, $adminId, $scheduledAt) {
            $sms = ScheduledSms::create([
                'title'          => $data['title'],
                'message_body'   => $data['message_body'],
                'sender_number'  => preg_replace('/[^0-9]/', '', $data['sender_number']),
                'scheduled_at'   => $scheduledAt,
                'status'         => ScheduledSms::STATUS_PENDING,
                'created_by'     => $adminId,
            ]);

            $now = now();
            $rows = array_map(fn (int $uid) => [
                'scheduled_sms_id' => $sms->id,
                'user_id'          => $uid,
                'status'           => ScheduledSmsRecipient::STATUS_PENDING,
                'created_at'       => $now,
            ], $userIds);

            ScheduledSmsRecipient::insert($rows);

            return $sms->load('recipients');
        });
    }

    public function update(ScheduledSms $sms, array $data, array $userIds): ScheduledSms
    {
        if (!$sms->isEditable()) {
            throw new \RuntimeException('테스트 발송 이후에는 수정할 수 없습니다.');
        }

        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $this->assertSchedulable($scheduledAt);
        $userIds = $this->normalizeUserIds($userIds);

        return DB::transaction(function () use ($sms, $data, $userIds, $scheduledAt) {
            $sms->update([
                'title'         => $data['title'],
                'message_body'  => $data['message_body'],
                'sender_number' => preg_replace('/[^0-9]/', '', $data['sender_number']),
                'scheduled_at'  => $scheduledAt,
            ]);

            $sms->recipients()->delete();

            $now = now();
            $rows = array_map(fn (int $uid) => [
                'scheduled_sms_id' => $sms->id,
                'user_id'          => $uid,
                'status'           => ScheduledSmsRecipient::STATUS_PENDING,
                'created_at'       => $now,
            ], $userIds);

            ScheduledSmsRecipient::insert($rows);

            return $sms->fresh(['recipients']);
        });
    }

    public function cancel(ScheduledSms $sms, int $adminId): bool
    {
        $affected = DB::table('crew.scheduled_sms')
            ->where('id', $sms->id)
            ->whereIn('status', [ScheduledSms::STATUS_PENDING, ScheduledSms::STATUS_TEST_SENT])
            ->update([
                'status'      => ScheduledSms::STATUS_CANCELED,
                'canceled_by' => $adminId,
                'canceled_at' => now(),
                'updated_at'  => now(),
            ]);

        if ($affected === 0) {
            throw new \RuntimeException('이미 발송이 시작되어 취소할 수 없습니다.');
        }

        return true;
    }

    public function activeTestRecipientCount(): int
    {
        return DB::table('crew.sms_test_recipients')
            ->where('is_active', true)
            ->count();
    }

    /** @return Collection<int, string> region id => name */
    public function regionOptions(): Collection
    {
        return DB::table('regions')->orderBy('name')->pluck('name', 'id');
    }

    /** @return Collection<int|string, string> */
    public function trainingGroupOptions(): Collection
    {
        return UsersDetail::query()
            ->whereNotNull('training_group')
            ->where('training_group', '!=', '')
            ->distinct()
            ->orderBy('training_group')
            ->pluck('training_group', 'training_group');
    }

    private function assertSchedulable(Carbon $scheduledAt): void
    {
        if ($scheduledAt->lte(now()->addMinutes(10))) {
            throw new \InvalidArgumentException('예약 시각은 현재 시각으로부터 10분 이후여야 합니다.');
        }
    }

    /** @param list<int|string> $userIds */
    private function normalizeUserIds(array $userIds): array
    {
        $userIds = array_values(array_unique(array_map('intval', $userIds)));

        if ($userIds === []) {
            throw new \InvalidArgumentException('수신자를 1명 이상 선택해 주세요.');
        }

        $existing = User::whereIn('id', $userIds)->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (count($existing) !== count($userIds)) {
            throw new \InvalidArgumentException('존재하지 않는 회원이 포함되어 있습니다.');
        }

        return $userIds;
    }
}
