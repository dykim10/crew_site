<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduledSms extends Model
{
    protected $table = 'crew.scheduled_sms';

    public const STATUS_PENDING   = 'pending';
    public const STATUS_TEST_SENT = 'test_sent';
    public const STATUS_SENDING   = 'sending';
    public const STATUS_SENT      = 'sent';
    public const STATUS_CANCELED  = 'canceled';
    public const STATUS_FAILED    = 'failed';

    protected $fillable = [
        'title',
        'message_body',
        'sender_number',
        'scheduled_at',
        'status',
        'test_sent_at',
        'sent_at',
        'solapi_group_id',
        'error_message',
        'created_by',
        'canceled_by',
        'canceled_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'test_sent_at' => 'datetime',
            'sent_at'      => 'datetime',
            'canceled_at'  => 'datetime',
        ];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(ScheduledSmsRecipient::class, 'scheduled_sms_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function canceler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'canceled_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCancelable($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_TEST_SENT]);
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCancelable(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_TEST_SENT], true);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING   => '예약 대기',
            self::STATUS_TEST_SENT => '테스트 발송됨',
            self::STATUS_SENDING   => '발송 중',
            self::STATUS_SENT      => '발송 완료',
            self::STATUS_CANCELED  => '취소됨',
            self::STATUS_FAILED    => '실패',
            default                => $status,
        };
    }

    public static function statusColor(string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING   => 'gray',
            self::STATUS_TEST_SENT => 'warning',
            self::STATUS_SENDING   => 'info',
            self::STATUS_SENT      => 'success',
            self::STATUS_CANCELED  => 'gray',
            self::STATUS_FAILED    => 'danger',
            default                => 'gray',
        };
    }
}
