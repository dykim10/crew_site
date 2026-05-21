<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'crew.events';

    protected $fillable = [
        'crew_id', 'name', 'description', 'start_date', 'end_date',
        'target_km', 'status',
        'event_type', 'parent_event_id', 'target_scope', 'generation',
        'form_schema', 'score_rules', 'max_participants', 'is_registration_open',
    ];

    protected function casts(): array
    {
        return [
            'start_date'           => 'date',
            'end_date'             => 'date',
            'form_schema'          => 'array',
            'score_rules'          => 'array',
            'is_registration_open' => 'boolean',
        ];
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function subEvents()
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    public function parentEvent()
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    public function isTypeB(): bool { return $this->event_type === 'B'; }
    public function isTypeA(): bool { return $this->event_type === 'A'; }

    public function isActive(): bool
    {
        $now = now()->toDateString();
        return $this->status === 'active'
            && $this->start_date->toDateString() <= $now
            && $this->end_date->toDateString() >= $now;
    }

    // form_schema 배열에서 필드 한 개 찾기
    public function getFieldByKey(string $key): ?array
    {
        foreach ($this->form_schema ?? [] as $field) {
            if (($field['key'] ?? '') === $key) return $field;
        }
        return null;
    }
}
