<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $table = 'crew.sms_logs';

    // 웹훅 수신 시 updated_at 갱신을 위해 UPDATED_AT 활성화
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'group_id',
        'sent_by',
        'filter_type',
        'filter_value',
        'recipient_cnt',
        'message',
        'status',
        'delivered_cnt',
        'failed_cnt',
        'result_data',
    ];

    protected $casts = [
        'result_data'   => 'array',
        'delivered_cnt' => 'integer',
        'failed_cnt'    => 'integer',
        'recipient_cnt' => 'integer',
    ];

    // status 가능 값: sent / delivered / partial / failed
    const STATUS_SENT      = 'sent';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_PARTIAL   = 'partial';
    const STATUS_FAILED    = 'failed';

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function getSuccessCountAttribute(): int
    {
        return $this->result_data['success_count'] ?? 0;
    }

    public function getFailCountAttribute(): int
    {
        return $this->result_data['fail_count'] ?? 0;
    }
}
