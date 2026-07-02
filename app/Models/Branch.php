<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Branch extends Model
{
    protected $table = 'branches'; // public 스키마 (Supabase 기존 테이블)

    protected $fillable = [
        'crew_id', 'name', 'admin_id', 'operator_id', 'status', 'image_url', 'branch_desc',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /** DB 저장값(S3 key 또는 URL) → 공개 CDN URL */
    public static function resolveImageUrl(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        if (str_starts_with($value, 'http')) {
            $cdnBase = rtrim((string) config('filesystems.disks.s3.url', ''), '/');
            if ($cdnBase !== '') {
                $bucket = (string) config('filesystems.disks.s3.bucket', '');
                if (preg_match('#amazonaws\.com(?:/' . preg_quote($bucket, '#') . ')?/(.+)$#', $value, $m)) {
                    return $cdnBase . '/' . $m[1];
                }
            }

            return $value;
        }

        return Storage::disk('s3')->url($value);
    }

    public function getPublicImageUrlAttribute(): ?string
    {
        return static::resolveImageUrl($this->attributes['image_url'] ?? null);
    }
}
