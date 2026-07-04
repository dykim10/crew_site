<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Administrator extends Model
{
    protected $table = 'crew.administrators';

    public const ROLES = [
        'branch_leader' => '지부장',
        'crew_ops'      => '운영진',
        'photo'         => '포토',
        'other'         => '기타',
    ];

    protected $fillable = [
        'user_id', 'name', 'profile_image', 'instagram_url', 'youtube_url', 'bio',
        'branch_id', 'branch_custom', 'role', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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

    public function getDisplayNameAttribute(): string
    {
        if ($this->user) {
            return $this->user->name ?? $this->user->nickname ?? $this->name ?? '—';
        }

        return $this->name ?? '—';
    }

    public function getPublicProfileImageUrlAttribute(): ?string
    {
        $override = static::resolveImageUrl($this->attributes['profile_image'] ?? null);
        if ($override) {
            return $override;
        }

        $avatar = $this->user?->detail?->avatar_url;
        if (blank($avatar)) {
            return null;
        }

        return static::resolveImageUrl($avatar);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function getBranchDisplayAttribute(): string
    {
        if ($this->branch) {
            return $this->branch->name;
        }

        return filled($this->branch_custom) ? $this->branch_custom : '기타';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
