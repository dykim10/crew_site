<?php

namespace App\Services;

use App\Models\Administrator;
use App\Models\User;

class AdministratorService
{
    public function appoint(User $user, array $data): Administrator
    {
        $branchId = $data['branch_id'] ?? $user->branch_id;

        return Administrator::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name'           => $user->name ?? $user->nickname,
                'role'           => $data['role'] ?? 'crew_ops',
                'branch_id'      => $branchId ?: null,
                'branch_custom'  => blank($branchId) ? ($data['branch_custom'] ?? null) : null,
                'instagram_url'  => $data['instagram_url'] ?? null,
                'youtube_url'    => $data['youtube_url'] ?? null,
                'bio'            => $data['bio'] ?? null,
                'sort_order'     => (int) ($data['sort_order'] ?? 0),
                'is_active'      => (bool) ($data['is_active'] ?? true),
            ]
        );
    }

    public function dismiss(User $user): bool
    {
        return (bool) Administrator::query()->where('user_id', $user->id)->delete();
    }

    public function syncNameFromUser(Administrator $administrator): void
    {
        if (!$administrator->user) {
            return;
        }

        $administrator->update([
            'name' => $administrator->user->name ?? $administrator->user->nickname,
        ]);
    }

    /** @return array<int, string> */
    public function memberOptionsForSelect(): array
    {
        $appointedIds = Administrator::query()
            ->whereNotNull('user_id')
            ->pluck('user_id');

        return User::query()
            ->whereNotIn('id', $appointedIds)
            ->orderBy('nickname')
            ->get()
            ->mapWithKeys(fn (User $user) => [
                $user->id => ($user->name ?? $user->nickname) . ' (@' . $user->nickname . ')',
            ])
            ->all();
    }
}
