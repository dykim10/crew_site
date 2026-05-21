<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhotoGallery extends Model
{
    protected $table = 'crew.photo_galleries';

    protected $fillable = [
        'user_id', 'session_number', 'title', 'description',
        'taken_at', 'cover_image_url', 'album_url', 'photo_urls',
    ];

    protected function casts(): array
    {
        return [
            'taken_at'   => 'date',
            'photo_urls' => 'array',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
