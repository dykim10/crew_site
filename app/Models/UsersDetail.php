<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersDetail extends Model
{
    protected $table = 'crew.users_detail';

    protected $fillable = [
        'user_id', 'generation_id', 'region_id',
        'grade', 'training_group', 'badges', 'admin_memo',
    ];

    protected function casts(): array
    {
        return ['badges' => 'array'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
