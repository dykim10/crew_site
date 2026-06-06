<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasSkin;
use App\Models\Board;
use App\Services\BoardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoardController extends Controller
{
    use HasSkin;

    public function __construct(private BoardService $service) {}

    public function index(string $type): View
    {
        abort_unless(array_key_exists($type, Board::$types), 404);
        $posts = $this->service->list($type);
        $meta  = Board::meta($type);
        return view("boards._common.{$this->skinDir()}.list", compact('type', 'posts', 'meta'));
    }

    public function show(string $type, Board $board): View
    {
        abort_if($board->board_type !== $type, 404);
        $this->authorize('view', $board);

        $board = $this->service->show($board);
        $meta  = Board::meta($type);
        return view("boards._common.{$this->skinDir()}.show", compact('type', 'board', 'meta'));
    }

    public function create(string $type): View
    {
        abort_unless(array_key_exists($type, Board::$types), 404);
        $meta = Board::meta($type);
        return view("boards._common.{$this->skinDir()}.form", compact('type', 'meta'));
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        abort_unless(array_key_exists($type, Board::$types), 404);

        $request->validate([
            'title'   => 'required|string|max:200',
            'content' => 'required|string|max:100000',
        ], [
            'title.required'   => '제목을 입력해주세요.',
            'content.required' => '내용을 입력해주세요.',
        ]);

        $board = $this->service->store($request, $type, auth()->id());

        return redirect()
            ->route('boards.show', [$type, $board])
            ->with('success', '게시글이 등록되었습니다.');
    }

    public function edit(string $type, Board $board): View
    {
        abort_if($board->board_type !== $type, 404);
        $this->authorize('update', $board);

        $meta = Board::meta($type);
        return view("boards._common.{$this->skinDir()}.form", compact('type', 'board', 'meta'));
    }

    public function update(Request $request, string $type, Board $board): RedirectResponse
    {
        abort_if($board->board_type !== $type, 404);
        $this->authorize('update', $board);

        $request->validate([
            'title'   => 'required|string|max:200',
            'content' => 'required|string|max:100000',
        ], [
            'title.required'   => '제목을 입력해주세요.',
            'content.required' => '내용을 입력해주세요.',
        ]);

        $this->service->update($request, $board);

        return redirect()
            ->route('boards.show', [$type, $board])
            ->with('success', '게시글이 수정되었습니다.');
    }

    public function destroy(string $type, Board $board): RedirectResponse
    {
        abort_if($board->board_type !== $type, 404);
        $this->authorize('delete', $board);

        $this->service->destroy($board);

        return redirect()
            ->route('boards.index', $type)
            ->with('success', '게시글이 삭제되었습니다.');
    }
}
