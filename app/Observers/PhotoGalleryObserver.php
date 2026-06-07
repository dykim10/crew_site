<?php

namespace App\Observers;

use App\Models\PhotoGallery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            // S3 키 추출: 전체 URL이면 경로 부분만, 경로면 그대로 사용
            $imageUrl = $photo->image_url;
            if (str_starts_with($imageUrl, 'http')) {
                // https://bucket.s3.region.amazonaws.com/key 형식에서 key 추출
                $s3Key = ltrim(parse_url($imageUrl, PHP_URL_PATH), '/');
            } else {
                $s3Key = $imageUrl;
            }

            // S3 자격증명으로 직접 다운로드 (private 버킷 대응)
            $imageContent = Storage::disk('s3')->get($s3Key);
            if (!$imageContent) {
                Log::warning("PhotoGallery #{$photo->id} S3 다운로드 실패: {$s3Key}");
                return;
            }

            $ext      = pathinfo($s3Key, PATHINFO_EXTENSION) ?: 'jpg';
            $filename = "photo_{$photo->id}.{$ext}";

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
