<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MainHeroImageService
{
    /** WebP 변환 후 S3 저장 경로 prefix */
    public const S3_FOLDER = 'main';

    /** 메인 배경 WebP 최대 해상도 (10:7) */
    public const WEBP_MAX_WIDTH = 1400;

    public const WEBP_MAX_HEIGHT = 980;

    /** CORE API 업로드 허용 바이트 (20MB) */
    public const MAX_BYTES = 20971520;

    private string $coreApiBase;

    public function __construct()
    {
        $this->coreApiBase = rtrim(config('services.core_api.url', 'http://localhost:8100'), '/');
    }

    /**
     * 크롭된 이미지 → CORE API WebP 변환 → S3 URL 반환.
     *
     * @throws ValidationException
     */
    public function convertAndStore(TemporaryUploadedFile|UploadedFile $file): string
    {
        try {
            $response = Http::timeout(60)
                ->attach('file', $file->get(), $file->getClientOriginalName())
                ->post("{$this->coreApiBase}/api/photo/resize-webp", [
                    'folder'         => self::S3_FOLDER,
                    'max_width'      => self::WEBP_MAX_WIDTH,
                    'max_height'     => self::WEBP_MAX_HEIGHT,
                    'max_size_bytes' => self::MAX_BYTES,
                ]);

            if ($response->successful()) {
                $url = $response->json('thumbnail_url');
                if (filled($url)) {
                    return $url;
                }
            }

            Log::warning('MainHeroImage CORE API 변환 실패', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (ConnectionException $e) {
            Log::warning('MainHeroImage CORE API 연결 실패', ['error' => $e->getMessage()]);
        }

        throw ValidationException::withMessages([
            'image_path' => '이미지 WebP 변환에 실패했습니다. CORE API 연결을 확인한 뒤 다시 시도해 주세요.',
        ]);
    }

    public function deleteFromStorage(?string $storedValue): void
    {
        $key = \App\Models\MainHeroImage::normalizeStoragePath($storedValue);
        if (blank($key) || str_starts_with($key, 'http')) {
            return;
        }

        try {
            \Illuminate\Support\Facades\Storage::disk('s3')->delete($key);
        } catch (\Throwable $e) {
            Log::warning('MainHeroImage S3 삭제 실패', ['key' => $key, 'error' => $e->getMessage()]);
        }
    }
}