<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Generation extends Model
{
    protected $table = 'generations'; // public 스키마

    protected $fillable = [
        'number', 'alias', 'start_date', 'end_date',
        'main_race_id', 'main_race_name',
        'notes', 'active_branch_ids', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date'       => 'date',
            'end_date'         => 'date',
            'active_branch_ids'=> 'array',
        ];
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->alias ? "{$this->number}기 ({$this->alias})" : "{$this->number}기";
    }
}
