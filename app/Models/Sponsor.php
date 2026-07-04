<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Sponsor extends Model
{
    protected $table = 'crew.sponsors';

    protected $fillable = [
        'name', 'logo_url', 'link_url', 'description', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** DB 저장값(S3 key 또는 URL) → S3 key 정규화 */
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

    /** DB 저장값(S3 key 또는 URL) → 공개 CDN URL */
    public static function resolveLogoUrl(?string $value): ?string
    {
        $path = static::normalizeStoragePath($value);
        if (blank($path)) {
            return null;
        }

        return Storage::disk('s3')->url($path);
    }

    public function getPublicLogoUrlAttribute(): ?string
    {
        return static::resolveLogoUrl($this->attributes['logo_url'] ?? null);
    }
}
