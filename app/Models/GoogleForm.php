<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleForm extends Model
{
    protected $table = 'crew.google_forms';

    protected $fillable = [
        'title',
        'sheet_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
