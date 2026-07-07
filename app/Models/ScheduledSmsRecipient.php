<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduledSmsRecipient extends Model
{
    protected $table = 'crew.scheduled_sms_recipients';

    public $timestamps = false;

    public const STATUS_PENDING = 'pending';
    public const STATUS_SENT    = 'sent';
    public const STATUS_FAILED  = 'failed';

    protected $fillable = [
        'scheduled_sms_id',
        'user_id',
        'status',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function scheduledSms(): BelongsTo
    {
        return $this->belongsTo(ScheduledSms::class, 'scheduled_sms_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
