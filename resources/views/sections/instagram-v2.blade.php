{{-- @pac_run Instagram 피드 (crew.instagram_cache) — v2 Energy Burst --}}
@php
  $fallbacks = [
    ['class' => 'ic-1', 'tag' => '#pacrun', 'label' => 'RUN'],
    ['class' => 'ic-2', 'tag' => '#running', 'label' => 'PAC'],
    ['class' => 'ic-3', 'tag' => '#crew', 'label' => '5K'],
    ['class' => 'ic-4', 'tag' => '#pacrun', 'label' => 'GO!'],
    ['class' => 'ic-5', 'tag' => '#runner', 'label' => 'KM'],
    ['class' => 'ic-6', 'tag' => '#seoul', 'label' => 'FIT'],
  ];
  $items = \App\Models\InstagramCache::sortForFeed($instagramPosts ?? collect());
  if ($items->isEmpty()) {
    $items = collect($fallbacks)->map(fn ($f) => (object) array_merge($f, ['permalink' => 'https://www.instagram.com/pac_run/', 'thumbnail_url' => null, 'is_fallback' => true]));
  }
@endphp
<section class="insta-section">
  <div class="section-eyebrow">Instagram</div>
  <div class="section-heading">@pac_run</div>
  <div class="swiper swiper-insta">
    <div class="swiper-wrapper">
      @foreach($items as $i => $post)
        @php
          $fb = $fallbacks[$i % count($fallbacks)];
          $bgClass = empty($post->is_fallback ?? false) ? '' : ($post->class ?? $fb['class']);
          $thumb = $post->thumbnail_url ?? null;
          $href = $post->permalink ?? 'https://www.instagram.com/pac_run/';
          $tag = $post->tag_label ?? $post->tag ?? $fb['tag'];
          $label = $fb['label'];
        @endphp
        <div class="swiper-slide" style="width:220px">
          <a href="{{ $href }}" target="_blank" rel="noopener noreferrer"
             class="insta-card block {{ $thumb ? '' : $bgClass }}"
             @if($thumb) style="background-image:url('{{ $thumb }}'); background-size:cover; background-position:center;" @endif>
            @unless($thumb)
              <div class="insta-inner"><div class="insta-icon-big">{{ $label }}</div></div>
            @endunless
            <div class="insta-hover"><div class="insta-hover-label">인스타 보기 →</div></div>
            <div class="insta-tag">{{ $tag }}</div>
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>
