<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BugReport extends Model
{
    protected $table = 'crew.bug_reports';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'attachment_url',
        'attachment_name',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => '접수 대기',
            'in_progress' => '처리 중',
            'resolved'    => '처리 완료',
            'closed'      => '종료',
            default       => $this->status,
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low'      => '낮음',
            'medium'   => '보통',
            'high'     => '높음',
            'critical' => '긴급',
            default    => $this->priority,
        };
    }

    public function isPending(): bool    { return $this->status === 'pending'; }
    public function isResolved(): bool   { return in_array($this->status, ['resolved', 'closed']); }
}
