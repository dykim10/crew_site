<?php

namespace App\Services;

use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoardService
{
    public function list(string $type, int $perPage = 20)
    {
        return Board::where('type', $type)
            ->with('author')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function show(Board $board): Board
    {
        $board->increment('view_count');
        $board->load('author');
        return $board;
    }

    public function store(Request $request, string $type, int $authorId): Board
    {
        $data = [
            'type'      => $type,
            'title'     => $request->title,
            'content'   => $request->content,
            'author_id' => $authorId,
        ];

        if ($type === 'photo' && $request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store("boards/{$type}", 's3');
            $data['thumbnail_url'] = Storage::disk('s3')->url($path);
        }

        return Board::create($data);
    }

    public function update(Request $request, Board $board): Board
    {
        $data = [
            'title'   => $request->title,
            'content' => $request->content,
        ];

        if ($board->type === 'photo' && $request->hasFile('thumbnail')) {
            if ($board->thumbnail_url) {
                $this->deleteS3Image($board->thumbnail_url);
            }
            $path = $request->file('thumbnail')->store("boards/{$board->type}", 's3');
            $data['thumbnail_url'] = Storage::disk('s3')->url($path);
        }

        $board->update($data);
        return $board;
    }

    public function destroy(Board $board): void
    {
        if ($board->thumbnail_url) {
            $this->deleteS3Image($board->thumbnail_url);
        }
        $board->delete();
    }

    private function deleteS3Image(string $url): void
    {
        $key = ltrim(parse_url($url, PHP_URL_PATH), '/');
        try {
            Storage::disk('s3')->delete($key);
        } catch (\Throwable) {
        }
    }
}
