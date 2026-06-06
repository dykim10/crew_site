<?php

namespace App\Policies;

use App\Models\BoardComment;
use App\Models\User;

class BoardCommentPolicy
{
    /** 수정: 본인 댓글만 */
    public function update(User $user, BoardComment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    /** 삭제: 본인 댓글 또는 모더레이터 */
    public function delete(User $user, BoardComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->isModerator();
    }
}
