<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersDetail extends Model
{
    protected $table = 'crew.users_detail';

    protected $fillable = [
        'user_id',
        'generation_id',
        'region_id',
        'group_id',
        'grade',
        'training_group',
        'skin_select',
        'join_date',
        'memo',
        'gender',
        'shirt_size',
    ];

    protected $attributes = [
        'skin_select' => '_skin_v1',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
