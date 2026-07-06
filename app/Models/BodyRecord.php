<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 체성분 기록 — Laravel INSERT/UPDATE 금지 (CORE API 경유).
 */
class BodyRecord extends Model
{
    protected $table = 'crew.body_records';

    public $timestamps = false;

    protected $guarded = ['*'];
}
