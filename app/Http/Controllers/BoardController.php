<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Services\BoardService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BoardController extends Controller
{
    public function __construct(private BoardService $service) {}

    public function index(string $type): View
    {
        abort_unless(array_key_exists($type, Board::$types), 404);
        $posts = $this->service->list($type);
        $meta  = Board::meta($type);
        return view('boards.index', compact('type', 'posts', 'meta'));
    }

    public function show(string $type, Board $board): View
    {
        abort_if($board->board_type !== $type, 404);
        $this->authorize('view', $board);

        $board = $this->service->show($board);
        $meta  = Board::meta($type);
        return view('boards.show', compact('type', 'board', 'meta'));
    }

    public function create(string $type): View
    {
        abort_unless(array_key_exists($type, Board::$types), 404);
        $meta = Board::meta($type);
        return view('boards.create', compact('type', 'meta'));
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
        return view('boards.edit', compact('type', 'board', 'meta'));
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

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ], [
            'image.required' => '이미지를 선택해주세요.',
            'image.image'    => '이미지 파일만 업로드 가능합니다.',
            'image.mimes'    => 'JPG, PNG, WEBP, GIF 파일만 가능합니다.',
            'image.max'      => '파일 크기는 10MB 이하여야 합니다.',
        ]);

        $user    = auth()->user();
        $file    = $request->file('image');
        $coreUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
        $folder  = 'boards/' . $user->id;

        try {
            $url = null;
            try {
                $response = Http::timeout(30)
                    ->attach('file', $file->get(), $file->getClientOriginalName())
                    ->post("{$coreUrl}/api/photo/resize-webp", ['folder' => $folder]);

                if ($response->successful()) {
                    $url = $response->json('thumbnail_url');
                }
            } catch (ConnectionException) {}

            if (!$url) {
                $path = $folder . '/' . Str::uuid() . '.' . $file->extension();
                Storage::disk('s3')->put($path, $file->get(), 'public');
                $url = Storage::disk('s3')->url($path);
            }

            return response()->json(['url' => $url]);
        } catch (\Exception $e) {
            Log::error('게시판 이미지 업로드 실패', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return response()->json(['error' => '이미지 업로드에 실패했습니다.'], 500);
        }
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
