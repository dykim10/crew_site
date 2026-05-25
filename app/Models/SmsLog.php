<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsLog extends Model
{
    protected $table = 'crew.sms_logs';

    protected $fillable = [
        'sender_id',
        'target_type',
        'target_id',
        'recipient_count',
        'message',
        'result',
    ];

    protected $casts = [
        'result' => 'array',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getSuccessCountAttribute(): int
    {
        return $this->result['success_count'] ?? 0;
    }

    public function getFailCountAttribute(): int
    {
        return $this->result['fail_count'] ?? 0;
    }
}
