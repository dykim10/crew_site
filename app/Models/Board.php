<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use SoftDeletes;

    protected $table = 'crew.boards';

    protected $fillable = [
        'crew_id',
        'board_type',
        'user_id',
        'title',
        'content',
        'view_count',
        'is_secret',
    ];

    protected function casts(): array
    {
        return [
            'is_secret'  => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public static array $types = [
        'free' => ['label' => '자유게시판', 'desc' => '크루 멤버들의 자유로운 이야기'],
        'qna'  => ['label' => '문의게시판', 'desc' => '질문 & 답변'],
    ];

    public static function meta(string $type): array
    {
        return self::$types[$type] ?? ['label' => '게시판', 'desc' => ''];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 최상위 댓글 목록 (parent_id = null).
     * 각 댓글에 대댓글(replies)과 작성자를 eager-load.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(BoardComment::class)
            ->whereNull('parent_id')
            ->whereNull('deleted_at')
            ->with([
                'author',
                'replies' => fn ($q) => $q->whereNull('deleted_at')
                    ->with('author')
                    ->orderBy('created_at'),
            ])
            ->orderBy('created_at');
    }

    public function isWrittenBy(?int $userId): bool
    {
        return $userId !== null && $this->user_id === $userId;
    }
}
