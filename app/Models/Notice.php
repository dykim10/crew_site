<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $table = 'crew.notices';

    protected $fillable = [
        'title', 'content', 'author_id', 'is_pinned', 'target_type', 'target_ids',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned'  => 'boolean',
            'target_ids' => 'array',
        ];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // 해당 유저에게 보여줄 공지 최대 N개 (고정 우선 → 최신순)
    public static function forUser(User $user, int $limit = 3)
    {
        return static::where(function ($q) use ($user) {
            $q->where('target_type', 'all');
            // 추후: generation/region 필터 추가
        })
        ->orderByDesc('is_pinned')
        ->orderByDesc('created_at')
        ->limit($limit)
        ->get();
    }
}
