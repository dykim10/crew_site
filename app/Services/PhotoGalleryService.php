<?php

namespace App\Services;

use App\Models\PhotoGallery;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoGalleryService
{
    private string $coreApiBase;

    public function __construct()
    {
        $this->coreApiBase = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
    }

    /**
     * 이미지 업로드 → S3 원본 저장 + CORE API WebP 썸네일 변환 → DB 저장.
     */
    public function store(array $data, UploadedFile $image, User $admin): PhotoGallery
    {
        // 1. 원본 이미지 S3 업로드
        $ext      = $image->extension();
        $key      = 'photo-galleries/' . now()->format('Y/m') . '/' . Str::uuid() . '.' . $ext;
        Storage::disk('s3')->put($key, $image->get());
        $imageUrl = Storage::disk('s3')->url($key);

        // 2. CORE API WebP 썸네일 변환
        $thumbnailUrl = $this->resizeToWebp($image);

        return PhotoGallery::create([
            'crew_id'       => 1,
            'admin_id'      => $admin->id,
            'title'         => $data['title'],
            'description'   => $data['description'] ?? null,
            'image_url'     => $imageUrl,
            'thumbnail_url' => $thumbnailUrl,
            'taken_at'      => $data['taken_at'] ?? null,
            'sort_order'    => $data['sort_order'] ?? 0,
        ]);
    }

    /**
     * CORE API POST /api/photo/resize-webp 호출.
     * 실패 시 null 반환 (원본 이미지만 저장, 썸네일 없이 운영 허용).
     */
    private function resizeToWebp(UploadedFile $image): ?string
    {
        try {
            $response = Http::timeout(30)
                ->attach('file', $image->get(), $image->getClientOriginalName())
                ->post("{$this->coreApiBase}/api/photo/resize-webp");

            if ($response->successful()) {
                return $response->json('thumbnail_url');
            }
        } catch (ConnectionException) {
            // CORE API 일시 불통 시 썸네일 없이 계속 진행
        }

        return null;
    }

    public function destroy(PhotoGallery $photo): void
    {
        // S3 원본 삭제
        $this->deleteS3Url($photo->image_url);

        // S3 썸네일 삭제
        if ($photo->thumbnail_url) {
            $this->deleteS3Url($photo->thumbnail_url);
        }

        $photo->delete();
    }

    private function deleteS3Url(string $url): void
    {
        $key = ltrim(parse_url($url, PHP_URL_PATH), '/');
        try {
            Storage::disk('s3')->delete($key);
        } catch (\Throwable) {
        }
    }
}
