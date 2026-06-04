<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Services\BoardService;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function __construct(private BoardService $service) {}

    public function index(string $type)
    {
        abort_unless(array_key_exists($type, Board::$types), 404);
        $posts = $this->service->list($type);
        $meta  = Board::meta($type);
        return view('boards.index', compact('type', 'posts', 'meta'));
    }

    public function show(string $type, Board $board)
    {
        abort_if($board->type !== $type, 404);
        $board = $this->service->show($board);
        $meta  = Board::meta($type);
        return view('boards.show', compact('type', 'board', 'meta'));
    }

    public function create(string $type)
    {
        abort_unless(array_key_exists($type, Board::$types), 404);
        $meta = Board::meta($type);
        return view('boards.create', compact('type', 'meta'));
    }

    public function store(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, Board::$types), 404);

        $rules = [
            'title'   => 'required|string|max:200',
            'content' => $type === 'photo' ? 'nullable|string|max:2000' : 'required|string|max:10000',
        ];
        if ($type === 'photo') {
            $rules['thumbnail'] = 'nullable|image|max:5120';
        }
        $request->validate($rules);

        $board = $this->service->store($request, $type, auth()->id());
        return redirect()
            ->route('boards.show', [$type, $board])
            ->with('success', '게시글이 등록되었습니다.');
    }

    public function edit(string $type, Board $board)
    {
        abort_if($board->type !== $type, 404);
        abort_unless($board->isWrittenBy(auth()->id()) || auth()->user()?->isAdmin(), 403);
        $meta = Board::meta($type);
        return view('boards.edit', compact('type', 'board', 'meta'));
    }

    public function update(Request $request, string $type, Board $board)
    {
        abort_if($board->type !== $type, 404);
        abort_unless($board->isWrittenBy(auth()->id()) || auth()->user()?->isAdmin(), 403);

        $rules = [
            'title'   => 'required|string|max:200',
            'content' => $type === 'photo' ? 'nullable|string|max:2000' : 'required|string|max:10000',
        ];
        if ($type === 'photo') {
            $rules['thumbnail'] = 'nullable|image|max:5120';
        }
        $request->validate($rules);

        $this->service->update($request, $board);
        return redirect()
            ->route('boards.show', [$type, $board])
            ->with('success', '게시글이 수정되었습니다.');
    }

    public function destroy(string $type, Board $board)
    {
        abort_if($board->type !== $type, 404);
        abort_unless($board->isWrittenBy(auth()->id()) || auth()->user()?->isAdmin(), 403);

        $this->service->destroy($board);
        return redirect()
            ->route('boards.index', $type)
            ->with('success', '게시글이 삭제되었습니다.');
    }
}
