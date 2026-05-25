<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventScore extends Model
{
    protected $table = 'crew.event_scores';

    protected $fillable = [
        'event_id', 'user_id', 'score', 'rank', 'memo',
        'group_id', 'source', 'awarded_by',
    ];

    protected function casts(): array
    {
        return ['score' => 'float'];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function awarder()
    {
        return $this->belongsTo(User::class, 'awarded_by');
    }
}
