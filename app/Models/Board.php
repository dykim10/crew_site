<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $table = 'crew.boards';

    protected $fillable = [
        'type', 'title', 'content',
        'author_id', 'thumbnail_url', 'image_urls',
        'view_count', 'is_pinned',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned'   => 'boolean',
            'image_urls'  => 'array',
        ];
    }

    public static array $types = [
        'free'  => ['label' => '자유게시판',  'desc' => '크루 멤버들의 자유로운 이야기', 'layout' => 'list'],
        'photo' => ['label' => '포토 게시판', 'desc' => '러닝 사진 공유 갤러리',        'layout' => 'grid'],
        'qna'   => ['label' => '문의 게시판', 'desc' => '질문 & 답변',                  'layout' => 'list'],
    ];

    public static function meta(string $type): array
    {
        return self::$types[$type] ?? ['label' => '게시판', 'desc' => '', 'layout' => 'list'];
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isWrittenBy(?int $userId): bool
    {
        return $userId && $this->author_id === $userId;
    }
}
