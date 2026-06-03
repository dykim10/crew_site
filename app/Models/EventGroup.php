<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventGroup extends Model
{
    protected $table = 'crew.event_groups';

    protected $fillable = [
        'crew_id', 'generation_id', 'branch_id', 'event_id',
        'group_no', 'group_name', 'leader_user_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function members()
    {
        return $this->hasMany(EventGroupMember::class, 'group_id');
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_user_id');
    }

    public function scopeForEvent($query, int $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeForBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
