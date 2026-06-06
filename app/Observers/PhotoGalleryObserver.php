<?php

namespace App\Observers;

use App\Models\PhotoGallery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhotoGalleryObserver
{
    /**
     * 포토 갤러리 등록 직후 CORE API를 호출해 WebP 썸네일을 생성한다.
     * CORE API 장애 또는 타임아웃 시 thumbnail_url = null로 유지 (서비스 중단 없음).
     */
    public function created(PhotoGallery $photo): void
    {
        if (!$photo->image_url || $photo->thumbnail_url) {
            return;
        }

        $this->generateThumbnail($photo);
    }

    /**
     * 이미지가 교체될 때 썸네일도 재생성한다.
     */
    public function updated(PhotoGallery $photo): void
    {
        if (!$photo->wasChanged('image_url')) {
            return;
        }

        $this->generateThumbnail($photo);
    }

    private function generateThumbnail(PhotoGallery $photo): void
    {
        $coreApiUrl = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');

        try {
            // S3 원본 이미지 다운로드
            $imageContent = Http::timeout(30)->get($photo->image_url)->body();
            $ext          = pathinfo(parse_url($photo->image_url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename     = "photo_{$photo->id}.{$ext}";

            // CORE API WebP 변환 요청
            $response = Http::timeout(60)
                ->attach('file', $imageContent, $filename)
                ->post("{$coreApiUrl}/api/photo/resize-webp");

            if ($response->successful()) {
                $photo->updateQuietly(['thumbnail_url' => $response->json('thumbnail_url')]);
            } else {
                Log::warning("PhotoGallery #{$photo->id} 썸네일 생성 실패: HTTP {$response->status()}");
            }
        } catch (\Throwable $e) {
            Log::warning("PhotoGallery #{$photo->id} 썸네일 생성 예외: {$e->getMessage()}");
        }
    }
}
