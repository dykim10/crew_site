{{-- @pac_run Instagram 피드 (crew.instagram_cache) — v1 Dark Editorial --}}
@php
  $fallbacks = [
    ['class' => 'ic-1', 'tag' => '#pacrun'],
    ['class' => 'ic-2', 'tag' => '#running'],
    ['class' => 'ic-3', 'tag' => '#crew'],
    ['class' => 'ic-4', 'tag' => '#pacrun'],
    ['class' => 'ic-5', 'tag' => '#runner'],
    ['class' => 'ic-6', 'tag' => '#seoul'],
  ];
  $items = \App\Models\InstagramCache::sortForFeed($instagramPosts ?? collect());
  if ($items->isEmpty()) {
    $items = collect($fallbacks)->map(fn ($f) => (object) array_merge($f, ['permalink' => 'https://www.instagram.com/pac_run/', 'thumbnail_url' => null, 'is_fallback' => true]));
  }
@endphp
<section class="insta-section">
  <div class="pac-section-label">Instagram</div>
  <div class="pac-section-heading">@pac_run</div>
  <div class="swiper swiper-insta">
    <div class="swiper-wrapper">
      @foreach($items as $i => $post)
        @php
          $fb = $fallbacks[$i % count($fallbacks)];
          $bgClass = empty($post->is_fallback ?? false) ? '' : ($post->class ?? $fb['class']);
          $thumb = $post->thumbnail_url ?? null;
          $href = $post->permalink ?? 'https://www.instagram.com/pac_run/';
          $tag = $post->tag_label ?? $post->tag ?? $fb['tag'];
        @endphp
        <div class="swiper-slide" style="width:220px">
          <a href="{{ $href }}" target="_blank" rel="noopener noreferrer"
             class="insta-card block {{ $thumb ? '' : $bgClass }}"
             @if($thumb) style="background-image:url('{{ $thumb }}'); background-size:cover; background-position:center;" @endif>
            <div class="insta-hover">
              <div class="insta-hover-icon">📸</div>
              <div class="insta-hover-text">보기</div>
            </div>
            <div class="insta-watermark">{{ $tag }}</div>
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>
