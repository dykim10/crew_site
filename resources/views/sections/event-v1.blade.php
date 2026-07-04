{{-- 이벤트 섹션 v1 — Dark Editorial: 날짜/배지/장소 텍스트 나열 카드형 --}}
<section class="event-section">
  <div class="pac-section-label">Events</div>
  <div class="pac-section-heading">다가오는 이벤트</div>
  @if($events->isNotEmpty())
  <div class="event-grid">
    @foreach($events as $ev)
    @php
      $thumb = $ev->thumbnail_url
        ? (str_starts_with($ev->thumbnail_url,'http') ? $ev->thumbnail_url : \Storage::disk('s3')->url($ev->thumbnail_url))
        : null;
    @endphp
    <a href="{{ route('events.show', $ev) }}" class="event-card" style="text-decoration:none;color:inherit;">
      <div class="event-thumb {{ $thumb ? 'event-thumb--has-image' : 'event-thumb--placeholder' }}">
        @if($thumb)
          <div class="event-thumb-media">
            <img src="{{ $thumb }}" alt="{{ $ev->name }}" class="event-thumb-img" loading="lazy" decoding="async">
          </div>
        @endif
        <div class="event-date-tag">{{ $ev->start_date->format('Y.m.d') }}</div>
        @if($ev->status === 'active')
          <div style="position:absolute;top:14px;right:14px;background:#E80043;color:white;font-size:9px;font-weight:700;padding:3px 8px;letter-spacing:2px;">LIVE</div>
        @endif
      </div>
      <div class="event-info">
        <div class="event-type-tag">B Type · {{ $ev->target_scope === 'all' ? '전체' : ($ev->target_scope === 'generation' ? $ev->generation.'기' : '지부') }}</div>
        <div class="event-title">{{ Str::limit($ev->name, 28) }}</div>
        <div class="event-meta">
          @if($ev->location) <span>📍 {{ $ev->location }}</span> @endif
          <span>📅 {{ $ev->start_date->format('m.d') }}@if($ev->start_date->ne($ev->end_date))~{{ $ev->end_date->format('m.d') }}@endif</span>
        </div>
      </div>
    </a>
    @endforeach
    @if($events->count() < 4)
    <a href="{{ route('events.index') }}" class="event-card" style="text-decoration:none;color:inherit;display:flex;align-items:center;justify-content:center;min-height:200px;">
      <div style="text-align:center;opacity:.4;">
        <div style="font-family:'Bebas Neue',sans-serif;font-size:32px;letter-spacing:4px;">MORE</div>
        <div style="font-size:11px;letter-spacing:2px;margin-top:4px;">전체 이벤트 →</div>
      </div>
    </a>
    @endif
  </div>
  @else
  <div style="text-align:center;padding:60px 0;opacity:.4;">
    <div style="font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:4px;">진행 중인 이벤트가 없습니다</div>
    <a href="{{ route('events.index') }}" style="color:var(--pac-yellow);font-size:12px;letter-spacing:2px;text-decoration:none;margin-top:12px;display:block;">전체 이벤트 보기 →</a>
  </div>
  @endif
  <div style="text-align:right;margin-top:20px;">
    <a href="{{ route('events.index') }}" class="pac-btn-outline">전체 이벤트 →</a>
  </div>
</section>
