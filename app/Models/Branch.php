<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'branches'; // public 스키마 (Supabase 기존 테이블)

    protected $fillable = [
        'crew_id', 'name', 'admin_id', 'operator_id', 'status', 'image_url', 'branch_desc',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
