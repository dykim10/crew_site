<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingMinute extends Model
{
    protected $table = 'crew.meeting_minutes';

    protected $fillable = [
        'user_id', 'title', 'content', 'meeting_date', 'views', 'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
            'is_pinned'    => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
