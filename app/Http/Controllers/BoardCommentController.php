<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\BoardComment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BoardCommentController extends Controller
{
    public function store(Request $request, Board $board): RedirectResponse
    {
        $this->authorize('view', $board); // 비밀글 접근 제어 통과 후 댓글 허용

        $validated = $request->validate([
            'content'   => 'required|string|max:2000',
            'parent_id' => 'nullable|integer',
        ], [
            'content.required' => '댓글 내용을 입력해주세요.',
            'content.max'      => '댓글은 2,000자 이하로 작성해주세요.',
        ]);

        if ($validated['parent_id']) {
            $parent = BoardComment::find($validated['parent_id']);

            // 존재 확인, 동일 게시글 확인, 2depth 초과 방지
            abort_if(!$parent, 422, '존재하지 않는 댓글입니다.');
            abort_if($parent->board_id !== $board->id, 422, '잘못된 댓글 대상입니다.');
            abort_if($parent->parent_id !== null, 422, '대댓글에는 답글을 달 수 없습니다.');
        }

        BoardComment::create([
            'board_id'  => $board->id,
            'user_id'   => auth()->id(),
            'content'   => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', '댓글이 등록되었습니다.');
    }

    public function update(Request $request, Board $board, BoardComment $comment): RedirectResponse
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ], [
            'content.required' => '댓글 내용을 입력해주세요.',
        ]);

        $comment->update(['content' => $validated['content']]);

        return back()->with('success', '댓글이 수정되었습니다.');
    }

    public function destroy(Board $board, BoardComment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        // 최상위 댓글 삭제 시 대댓글도 함께 소프트 삭제
        if ($comment->isTopLevel()) {
            $comment->replies()->each(fn ($reply) => $reply->delete());
        }

        $comment->delete();

        return back()->with('success', '댓글이 삭제되었습니다.');
    }
}
