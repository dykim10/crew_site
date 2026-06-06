<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;

class BoardPolicy
{
    /**
     * 비밀글(is_secret=true): 작성자 본인 또는 모더레이터만 열람.
     * 일반 글: 모든 로그인 사용자 열람 가능.
     */
    public function view(User $user, Board $board): bool
    {
        if ($board->is_secret) {
            return $user->id === $board->user_id || $user->isModerator();
        }

        return true;
    }

    /** 수정: 본인 글만 */
    public function update(User $user, Board $board): bool
    {
        return $user->id === $board->user_id;
    }

    /** 삭제: 본인 글 또는 모더레이터 */
    public function delete(User $user, Board $board): bool
    {
        return $user->id === $board->user_id || $user->isModerator();
    }
}
