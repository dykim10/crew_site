<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationForm extends Model
{
    protected $table = 'crew.application_forms';

    protected $fillable = [
        'title',
        'cohort',
        'subtitle',
        'is_active',
        'open_from',
        'open_until',
        'form_fields',
        'created_by',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'open_from'   => 'date',
        'open_until'  => 'date',
        'form_fields' => 'array',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'form_id');
    }

    public static function getActive(): ?self
    {
        return self::where('is_active', true)->latest()->first();
    }

    public function isOpen(): bool
    {
        if (!$this->is_active) return false;
        $today = now()->toDateString();
        if ($this->open_from && $today < $this->open_from->toDateString()) return false;
        if ($this->open_until && $today > $this->open_until->toDateString()) return false;
        return true;
    }
}
