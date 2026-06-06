<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminLog extends Model
{
    protected $table = 'crew.admin_logs';

    protected $fillable = [
        'admin_id',
        'action',
        'target_type',
        'target_id',
        'description',
        'ip_address',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /** 액션 코드 → 한국어 레이블 */
    public static function actionLabel(string $action): string
    {
        return match($action) {
            'confirmed'        => '검수 확인',
            'confirm_canceled' => '검수 취소',
            'approved'         => '승인',
            'rejected'         => '반려',
            'role_changed'     => '권한 변경',
            'created'          => '생성',
            'updated'          => '수정',
            'deleted'          => '삭제',
            'status_changed'   => '상태 변경',
            'email_sent'       => '이메일 발송',
            'pinned'           => '공지 고정',
            'unpinned'         => '공지 해제',
            default            => $action,
        };
    }

    /** 액션 코드 → Filament 배지 색상 */
    public static function actionColor(string $action): string
    {
        return match($action) {
            'confirmed', 'approved' => 'success',
            'confirm_canceled', 'rejected', 'deleted' => 'danger',
            'role_changed', 'status_changed' => 'warning',
            'email_sent' => 'info',
            default => 'gray',
        };
    }
}
