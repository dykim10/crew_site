<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InstagramCache extends Model
{
    protected $table = 'crew.instagram_cache';

    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'media_type',
        'media_url',
        'thumbnail_url',
        'caption',
        'like_count',
        'comments_count',
        'permalink',
        'posted_at',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'posted_at'  => 'datetime',
            'fetched_at' => 'datetime',
        ];
    }

    /** 캡션 첫 해시태그 또는 짧은 라벨 */
    public function getTagLabelAttribute(): string
    {
        if (preg_match('/#(\w+)/u', $this->caption ?? '', $m)) {
            return '#' . $m[1];
        }
        return '#pacrun';
    }

    /** 홈 피드: 최신 게시물 우선 (posted_at → post_id) */
    public function scopeForFeed(Builder $query): Builder
    {
        return $query
            ->orderByDesc('posted_at')
            ->orderByDesc('post_id');
    }

    /** 홈에 노출할 최신 N건 */
    public static function forHome(int $limit = 12): Collection
    {
        return static::query()
            ->forFeed()
            ->limit($limit)
            ->get();
    }

    /** 컬렉션 재정렬 (뷰·컨트롤러 이중 방어) */
    public static function sortForFeed(iterable $posts, int $limit = 12): Collection
    {
        return collect($posts)
            ->sortByDesc(fn (self $post) => sprintf(
                '%019d-%s',
                $post->posted_at?->getTimestamp() ?? 0,
                $post->post_id
            ))
            ->values()
            ->take($limit);
    }
}
