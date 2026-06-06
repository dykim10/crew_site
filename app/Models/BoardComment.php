<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardComment extends Model
{
    use SoftDeletes;

    protected $table = 'crew.board_comments';

    protected $fillable = [
        'board_id',
        'user_id',
        'content',
        'parent_id',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** 상위 댓글 (parent_id → comment) */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(BoardComment::class, 'parent_id');
    }

    /**
     * 대댓글 목록 (2depth — 대댓글에는 자식 없음).
     * 삭제된 대댓글 제외, 작성 순 정렬.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(BoardComment::class, 'parent_id')
            ->whereNull('deleted_at')
            ->with('author')
            ->orderBy('created_at');
    }

    /** 최상위 댓글 여부 */
    public function isTopLevel(): bool
    {
        return $this->parent_id === null;
    }

    public function isWrittenBy(?int $userId): bool
    {
        return $userId !== null && $this->user_id === $userId;
    }
}
