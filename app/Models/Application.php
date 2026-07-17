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
        'import_source',
        'import_key',
        'matched_user_id',
        'matched_at',
        'matched_by',
        'generation_id',
        'branch_id',
        'preferred_branch_id',
        'enrolled_at',
    ];

    protected $casts = [
        'field_values'  => 'array',
        'agree_privacy' => 'boolean',
        'matched_at'    => 'datetime',
        'enrolled_at'   => 'datetime',
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

    public function matchedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matched_user_id');
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class, 'generation_id');
    }

    public function preferredBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'preferred_branch_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
