<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\User;

/**
 * 지부 관리자(region_admin) ↔ branches.admin_id 동기화
 */
class BranchAdminService
{
    /** @return array<string, string> */
    public static function roleLabels(): array
    {
        return [
            'super_admin'  => '슈퍼관리자',
            'region_admin' => '지부 관리자',
            'operator'     => '운영자',
            'member'       => '일반멤버',
        ];
    }

    public function resolveBranchId(User $user): ?int
    {
        if ($user->branch_id) {
            return (int) $user->branch_id;
        }

        $user->loadMissing('administrator');

        return $user->administrator?->branch_id
            ? (int) $user->administrator->branch_id
            : null;
    }

    /** 지부 관리자 권한 사용자를 해당 지부의 admin_id로 지정 (기존 1인은 대체) */
    public function syncFromRegionAdmin(User $user): ?Branch
    {
        if ($user->role !== 'region_admin') {
            return null;
        }

        $branchId = $this->resolveBranchId($user);
        if (! $branchId) {
            return null;
        }

        $branch = Branch::query()->find($branchId);
        if (! $branch) {
            return null;
        }

        if ((int) $branch->admin_id !== (int) $user->id) {
            $branch->update(['admin_id' => $user->id]);
        }

        return $branch->fresh();
    }

    /** 지부 관리자 권한 해제 시, 본인이 담당이던 지부 admin_id 초기화 */
    public function clearIfBranchAdmin(User $user): void
    {
        Branch::query()
            ->where('admin_id', $user->id)
            ->update(['admin_id' => null]);
    }

    public function onRoleChanged(User $user, string $before, string $after): ?Branch
    {
        if ($after === 'region_admin') {
            return $this->syncFromRegionAdmin($user->fresh());
        }

        if ($before === 'region_admin' && $after !== 'region_admin') {
            $this->clearIfBranchAdmin($user->fresh());
        }

        return null;
    }
}
