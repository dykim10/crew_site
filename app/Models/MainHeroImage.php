<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MainHeroImage extends Model
{
    protected $table = 'crew.main_hero_images';

    protected $fillable = [
        'image_path',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public static function normalizeStoragePath(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (! str_starts_with($value, 'http')) {
            return $value;
        }

        $cdnBase = rtrim((string) config('filesystems.disks.s3.url', ''), '/');
        if ($cdnBase !== '' && str_starts_with($value, $cdnBase . '/')) {
            return substr($value, strlen($cdnBase) + 1);
        }

        $bucket = (string) config('filesystems.disks.s3.bucket', '');
        if (preg_match('#amazonaws\.com(?:/' . preg_quote($bucket, '#') . ')?/(.+)$#', $value, $m)) {
            return $m[1];
        }

        return $value;
    }

    public static function resolveImageUrl(?string $value): ?string
    {
        $path = static::normalizeStoragePath($value);
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('s3')->url($path);
    }

    public function getPublicImageUrlAttribute(): ?string
    {
        return static::resolveImageUrl($this->attributes['image_path'] ?? null);
    }

    public static function active(): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->orderByDesc('id')
            ->first();
    }

    /** @return array{url: string} */
    public static function activeDisplay(): array
    {
        return [
            'url' => static::publicBackgroundUrl(),
        ];
    }

    /** 메인 pac-image 배경 URL (미등록 시 기본 이미지) */
    public static function publicBackgroundUrl(): string
    {
        $custom = static::active()?->public_image_url;

        return $custom ?: asset('images/main_default_img.jpg');
    }

    protected static function booted(): void
    {
        static::deleting(function (MainHeroImage $image) {
            app(\App\Services\MainHeroImageService::class)->deleteFromStorage($image->image_path);
        });
    }
}
