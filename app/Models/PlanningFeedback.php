<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanningFeedback extends Model
{
    protected $table = 'crew.planning_feedbacks';

    public $timestamps = false;

    protected $fillable = [
        'document_key',
        'reviewer_name',
        'good_points',
        'bad_points',
        'questions',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
