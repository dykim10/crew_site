<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoGallery extends Model
{
    protected $table = 'crew.photo_galleries';

    protected $fillable = [
        'crew_id',
        'admin_id',
        'title',
        'description',
        'image_url',
        'thumbnail_url',
        'taken_at',
        'view_count',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'date',
        ];
    }

    /** 등록한 관리자 */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
