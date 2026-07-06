<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingReport extends Model
{
    protected $table = 'crew.training_reports';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'period_start',
        'period_end',
        'report',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end'   => 'date',
            'report'       => 'array',
            'created_at'   => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
