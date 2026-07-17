<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleForm extends Model
{
    protected $table = 'crew.google_forms';

    public const PURPOSE_GENERAL = 'general';
    public const PURPOSE_GENERATION_RECRUIT = 'generation_recruit';
    public const PURPOSE_EVENT = 'event';

    public const PURPOSE_LABELS = [
        self::PURPOSE_GENERAL => '일반 설문',
        self::PURPOSE_GENERATION_RECRUIT => '기수 모집',
        self::PURPOSE_EVENT => '이벤트',
    ];

    protected $fillable = [
        'title',
        'sheet_id',
        'description',
        'is_active',
        'purpose',
        'generation_id',
        'event_id',
        'column_mapping',
        'form_url',
    ];

    protected $casts = [
        'is_active'       => 'boolean',
        'column_mapping'  => 'array',
    ];

    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class, 'generation_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
