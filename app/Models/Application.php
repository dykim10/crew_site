<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    protected $table = 'crew.applications';

    protected $fillable = [
        'form_id',
        'name_enc',
        'email_hash',
        'email_enc',
        'phone_enc',
        'field_values',
        'status',
        'admin_memo',
        'agree_privacy',
    ];

    protected $casts = [
        'field_values'  => 'array',
        'agree_privacy' => 'boolean',
    ];

    public const STATUS_LABELS = [
        'pending'    => '검토 중',
        'approved'   => '승인',
        'rejected'   => '거절',
        'waitlisted' => '대기',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(ApplicationForm::class, 'form_id');
    }
}
