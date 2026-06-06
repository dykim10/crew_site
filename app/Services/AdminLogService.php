<?php

namespace App\Services;

use App\Models\AdminLog;
use Illuminate\Http\Request;

class AdminLogService
{
    /**
     * 관리자 액션을 로그에 기록한다.
     *
     * @param  string       $action      confirmed / approved / rejected / role_changed / etc.
     * @param  string       $targetType  RunningLog / User / BugReport / Event / Notice / etc.
     * @param  int|null     $targetId
     * @param  string       $description 사람이 읽을 수 있는 설명
     * @param  int|null     $adminId     null 이면 현재 로그인 사용자
     */
    public static function log(
        string  $action,
        string  $targetType,
        ?int    $targetId,
        string  $description,
        ?int    $adminId = null,
    ): void {
        $adminId ??= auth()->id();

        if (! $adminId) {
            return;
        }

        AdminLog::create([
            'admin_id'    => $adminId,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'description' => $description,
            'ip_address'  => request()?->ip(),
        ]);
    }
}
