<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Generation extends Model
{
    protected $table = 'generations'; // public 스키마

    protected $fillable = [
        'number', 'alias', 'start_date', 'end_date',
        'main_race_id', 'main_race_name', 'main_races',
        'apply_method', 'google_form_id', 'application_form_id',
        'notes', 'active_branch_ids', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date'        => 'date',
            'end_date'          => 'date',
            'active_branch_ids' => 'array',
            'main_races'        => 'array',
        ];
    }

    public function googleForm(): BelongsTo
    {
        return $this->belongsTo(GoogleForm::class, 'google_form_id');
    }

    public function applicationForm(): BelongsTo
    {
        return $this->belongsTo(ApplicationForm::class, 'application_form_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->alias ? "{$this->number}기 ({$this->alias})" : "{$this->number}기";
    }

    /** @return list<array{edition_id: ?int, race_id: ?int, name: string}> */
    public function mainRacesList(): array
    {
        $rows = $this->main_races;
        if (is_array($rows) && $rows !== []) {
            return array_values(array_map(function ($r) {
                return [
                    'edition_id' => isset($r['edition_id']) && $r['edition_id'] !== '' ? (int) $r['edition_id'] : null,
                    'race_id'    => isset($r['race_id']) && $r['race_id'] !== '' ? (int) $r['race_id'] : null,
                    'name'       => (string) ($r['name'] ?? ''),
                ];
            }, $rows));
        }

        if ($this->main_race_id || $this->main_race_name) {
            return [[
                'edition_id' => null,
                'race_id'    => $this->main_race_id ? (int) $this->main_race_id : null,
                'name'       => (string) ($this->main_race_name ?? ''),
            ]];
        }

        return [];
    }
}
