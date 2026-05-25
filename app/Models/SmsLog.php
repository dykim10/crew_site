<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $table = 'crew.sms_logs';

    const UPDATED_AT = null;

    protected $fillable = [
        'sent_by',
        'filter_type',
        'filter_value',
        'recipient_cnt',
        'message',
        'status',
        'result_data',
    ];

    protected $casts = [
        'result_data' => 'array',
    ];

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
