{{-- 이벤트 썸네일: 고정 영역 + inset 5% + contain + 중앙 정렬 --}}
@props(['url' => null, 'alt' => ''])

<div {{ $attributes->merge(['class' => 'event-thumb-frame relative w-full overflow-hidden bg-pac-black-900 flex-shrink-0']) }}>
  @if($url)
    <div class="event-thumb-media">
      <img src="{{ $url }}" alt="{{ $alt }}" class="event-thumb-img" loading="lazy" decoding="async">
    </div>
  @endif
  {{ $slot }}
</div>
