{{-- 지부 섹션 v2 — Energy Burst: DB 업로드 이미지 + branch-top 영역 --}}
@php
  $branches = $branches ?? collect();
  $fallbackBgs = ['branch-banpo-color', 'branch-yonsei-color', 'branch-gunpo-color', 'branch-incheon-color'];
@endphp
<section class="branch-section">
  <div class="section-eyebrow">지부 소개</div>
  <div class="section-heading">{{ $branches->count() ?: 4 }}개 지부,<br><em>하나의</em> 크루</div>
  <div class="swiper swiper-branches">
    <div class="swiper-wrapper">
      @forelse($branches as $i => $branch)
        @php
          $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
          $fallbackBg = $fallbackBgs[$i % count($fallbackBgs)];
        @endphp
        <div class="swiper-slide" style="width:300px">
          <div class="branch-card">
            <div class="branch-top {{ $branch->public_image_url ? '' : $fallbackBg }}">
              @if($branch->public_image_url)
                <img class="branch-top-img" src="{{ $branch->public_image_url }}" alt="{{ $branch->name }}" loading="lazy">
                <div class="branch-top-overlay"></div>
              @endif
              <div class="branch-top-num">{{ $num }}</div>
              <div class="branch-top-region">BRANCH · {{ $num }}</div>
              <div class="branch-top-name">{{ $branch->name }}</div>
            </div>
            <div class="branch-bottom">
              <div class="branch-slogan">{{ $branch->branch_desc ?: '소개 준비 중' }}</div>
              <a href="{{ route('branch') }}" class="branch-arrow-btn">지부 보기</a>
            </div>
          </div>
        </div>
      @empty
        <div class="swiper-slide" style="width:300px">
          <div class="branch-card">
            <div class="branch-top branch-banpo-color">
              <div class="branch-top-name">지부 정보 준비 중</div>
            </div>
            <div class="branch-bottom">
              <a href="{{ route('branch') }}" class="branch-arrow-btn">지부 보기</a>
            </div>
          </div>
        </div>
      @endforelse
    </div>
  </div>
</section>
