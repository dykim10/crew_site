{{-- 지부 섹션 v1 — Dark Editorial: DB 업로드 이미지 + branch-overlay --}}
@php
  $branches = $branches ?? collect();
  $fallbackBgs = ['branch-banpo', 'branch-yonsei', 'branch-gunpo', 'branch-incheon'];
@endphp
<section class="branch-section">
  <div class="pac-section-label">지부 소개</div>
  <div class="pac-section-heading">우리 지부를 만나세요</div>
  <div class="swiper swiper-branches">
    <div class="swiper-wrapper">
      @forelse($branches as $i => $branch)
        @php
          $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
          $fallbackBg = $fallbackBgs[$i % count($fallbackBgs)];
        @endphp
        <div class="swiper-slide" style="width:340px">
          <a href="{{ route('branch.show', $branch) }}" class="branch-card" style="display:block;text-decoration:none;color:inherit;">
            <div class="branch-bg {{ $branch->public_image_url ? 'branch-bg--has-image' : $fallbackBg }}">
              @if($branch->public_image_url)
                <div class="branch-thumb-media">
                  <img src="{{ $branch->public_image_url }}" alt="{{ $branch->name }}" class="branch-thumb-img" loading="lazy" decoding="async">
                </div>
              @endif
            </div>
            <div class="branch-overlay"></div>
            <div class="branch-number">{{ $num }}</div>
            <div class="branch-arrow">→</div>
            <div class="branch-content">
              <div class="branch-region-tag">BRANCH · {{ $num }}</div>
              <div class="branch-name">{{ $branch->name }}</div>
              <div class="branch-slogan">{{ $branch->branch_desc ?: '소개 준비 중' }}</div>
            </div>
          </a>
        </div>
      @empty
        <div class="swiper-slide" style="width:340px">
          <a href="{{ route('branch') }}" class="branch-card" style="display:block;text-decoration:none;color:inherit;">
            <div class="branch-bg branch-banpo"></div>
            <div class="branch-overlay"></div>
            <div class="branch-content">
              <div class="branch-name">지부 정보 준비 중</div>
            </div>
          </a>
        </div>
      @endforelse
    </div>
  </div>
</section>
