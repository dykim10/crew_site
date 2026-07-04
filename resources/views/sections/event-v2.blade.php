{{-- 이벤트 섹션 v2 — Energy Burst: 번호 + [신청하기] CTA 카드형 --}}
<section class="event-section">
  <div class="section-eyebrow" style="color:var(--yellow)">Events</div>
  <div class="section-heading">다가오는<br>이벤트</div>
  @if($events->isNotEmpty())
  <div class="event-grid">
    @foreach($events as $idx => $ev)
    @php
      $thumb = $ev->thumbnail_url
        ? (str_starts_with($ev->thumbnail_url,'http') ? $ev->thumbnail_url : \Storage::disk('s3')->url($ev->thumbnail_url))
        : null;
      $gradients = ['et-1','et-2','et-3','et-4'];
      $grad = $gradients[$idx % 4];
    @endphp
    <a href="{{ route('events.show', $ev) }}" class="event-card" style="text-decoration:none;color:inherit;">
      <div class="event-thumb {{ $thumb ? 'event-thumb--has-image' : $grad }}">
        @if($thumb)
          <div class="event-thumb-media">
            <img src="{{ $thumb }}" alt="{{ $ev->name }}" class="event-thumb-img" loading="lazy" decoding="async">
          </div>
        @endif
        <div class="event-thumb-num">{{ str_pad($idx+1,2,'0',STR_PAD_LEFT) }}</div>
        <div class="event-date-badge">{{ $ev->start_date->format('Y.m.d') }}</div>
        @if($ev->status === 'active')
          <div style="position:absolute;top:0;right:0;background:#E80043;color:white;font-size:9px;font-weight:700;padding:4px 10px;letter-spacing:2px;">LIVE</div>
        @endif
      </div>
      <div class="event-body">
        <div class="event-region-tag">{{ $ev->target_scope === 'all' ? '전체' : ($ev->target_scope === 'generation' ? $ev->generation.'기' : '지부') }} · B Type</div>
        <div class="event-title">{{ Str::limit($ev->name, 24) }}</div>
        @if($ev->location) <div class="event-meta-line">📍 {{ $ev->location }}</div> @endif
        <div class="event-meta-line">📅 {{ $ev->start_date->format('m.d') }}@if($ev->start_date->ne($ev->end_date))~{{ $ev->end_date->format('m.d') }}@endif</div>
        <a href="{{ route('events.show', $ev) }}" class="event-link">
          {{ $ev->isRecruitOpen() ? '신청하기 →' : '상세보기 →' }}
        </a>
      </div>
    </a>
    @endforeach
    @if($events->count() < 4)
    <a href="{{ route('events.index') }}" class="event-card" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">
      <div style="text-align:center;color:rgba(255,255,255,.3);">
        <div style="font-family:'Anton',sans-serif;font-size:28px;letter-spacing:4px;">MORE</div>
        <div style="font-size:11px;letter-spacing:2px;margin-top:4px;">전체 이벤트 →</div>
      </div>
    </a>
    @endif
  </div>
  @else
  <div style="text-align:center;padding:60px 0;color:rgba(255,255,255,.3);">
    <p style="font-family:'Anton',sans-serif;font-size:20px;letter-spacing:4px;">진행 중인 이벤트가 없습니다</p>
    <a href="{{ route('events.index') }}" style="color:var(--yellow);font-size:12px;letter-spacing:2px;text-decoration:none;margin-top:12px;display:block;">전체 이벤트 보기 →</a>
  </div>
  @endif
</section>
