<?php

namespace App\Services;

use App\Models\Board;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoardService
{
    public function list(string $boardType, int $perPage = 20): LengthAwarePaginator
    {
        return Board::where('board_type', $boardType)
            ->with('author')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function show(Board $board): Board
    {
        $board->increment('view_count');
        $board->load(['author', 'comments']);
        return $board;
    }

    public function store(Request $request, string $boardType, int $userId): Board
    {
        return Board::create([
            'board_type' => $boardType,
            'user_id'    => $userId,
            'title'      => $request->title,
            'content'    => clean($request->content, 'boards'),
            'is_secret'  => (bool) $request->boolean('is_secret'),
        ]);
    }

    public function update(Request $request, Board $board): Board
    {
        $board->update([
            'title'     => $request->title,
            'content'   => clean($request->content, 'boards'),
            'is_secret' => (bool) $request->boolean('is_secret'),
        ]);

        return $board;
    }

    public function destroy(Board $board): void
    {
        $this->deleteOrphanImages($board->content);
        $board->delete();
    }

    /**
     * content HTML에서 <img src="..."> URL을 추출해 S3에서 삭제.
     * Board Observer의 updating/deleting 이벤트에서도 호출 가능.
     */
    public function deleteOrphanImages(string $html): void
    {
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $matches);
        foreach ($matches[1] as $url) {
            $key = ltrim(parse_url($url, PHP_URL_PATH), '/');
            try {
                Storage::disk('s3')->delete($key);
            } catch (\Throwable) {
            }
        }
    }
}
