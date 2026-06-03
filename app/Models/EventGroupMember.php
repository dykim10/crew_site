<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventGroupMember extends Model
{
    protected $table = 'crew.event_group_members';

    public $timestamps = false;

    protected $fillable = ['crew_id', 'group_id', 'user_id'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function group()
    {
        return $this->belongsTo(EventGroup::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
