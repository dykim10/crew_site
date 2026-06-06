<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugReport extends Model
{
    protected $table = 'crew.bug_reports';

    protected $fillable = [
        'crew_id',
        'user_id',
        'title',
        'path',
        'description',
        'screenshot_url',
        'severity',
        'status',
        'admin_note',
        'resolved_by',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getSeverityLabelAttribute(): string
    {
        return match ($this->severity) {
            'low'    => '낮음',
            'medium' => '보통',
            'high'   => '높음',
            default  => $this->severity,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open'        => '접수 대기',
            'in_progress' => '처리 중',
            'resolved'    => '처리 완료',
            default       => $this->status,
        };
    }

    public function isOpen(): bool     { return $this->status === 'open'; }
    public function isResolved(): bool { return $this->status === 'resolved'; }
}
