<x-app-layout>
<div class="max-w-6xl mx-auto px-4 py-12 md:px-6 lg:px-8">

  {{-- 페이지 헤더 --}}
  <div class="mb-16">
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-4">지부 소개</p>
    <h1 class="font-display text-[clamp(48px,7vw,88px)] leading-none tracking-wide text-pac-black-900 uppercase">
      {{ $branchCount }}개 지부<br>
      <span class="text-pac-yellow-500">하나의</span> 크루
    </h1>
    <div class="w-20 h-0.5 bg-pac-yellow-500 mt-6"></div>
  </div>

  {{-- 지부 카드 (DB 데이터 기반) --}}
  @if($branches->isEmpty())
    <div class="border border-pac-black-100 bg-pac-black-900 py-20 text-center mb-16">
      <p class="font-display text-2xl text-pac-black-700 uppercase tracking-widest">지부 정보 준비 중</p>
    </div>
  @else
  <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-pac-black-100 border border-pac-black-100">
    @foreach($branches as $i => $branch)
    @php
      $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
      $gradients = [
          'linear-gradient(160deg, #2d1a00 0%, #0d0d0d 100%)',
          'linear-gradient(160deg, #001a2d 0%, #0d0d0d 100%)',
          'linear-gradient(160deg, #0d2200 0%, #0d0d0d 100%)',
          'linear-gradient(160deg, #001a2d 0%, #1a001a 100%)',
      ];
      $badgeColors = ['#E5AD16', '#E80043', '#10b981', '#6366f1'];
      $bg     = $gradients[$i % count($gradients)];
      $badge  = $badgeColors[$i % count($badgeColors)];
    @endphp
    <a href="{{ route('branch.show', $branch) }}"
       class="bg-pac-black-900 overflow-hidden group block no-underline text-inherit
              hover:-translate-y-1 transition-all duration-200">

      <x-event-thumbnail :url="$branch->public_image_url" :alt="$branch->name" class="h-48">
        @unless($branch->public_image_url)
          <div class="absolute inset-0 z-0" style="background:{{ $bg }};"></div>
          <div class="absolute inset-0 flex items-center justify-center z-0 pointer-events-none">
            <span class="font-display text-[100px] leading-none tracking-tight"
                  style="color:rgba(255,255,255,0.04);">{{ $num }}</span>
          </div>
        @endunless
        @if($branch->public_image_url)
          <div class="absolute inset-0 bg-gradient-to-t from-pac-black-900/90 via-pac-black-900/20 to-transparent pointer-events-none z-[2]"></div>
        @endif
        <div class="absolute bottom-4 left-5 z-[3]">
          <span class="font-display text-[10px] tracking-[4px] uppercase px-2 py-1"
                style="background:{{ $badge }};color:#1A1212;">
            {{ $num }}
          </span>
        </div>
      </x-event-thumbnail>

      {{-- 내용 --}}
      <div class="p-6 border-t border-pac-black-100">
        <p class="font-display text-[11px] tracking-[4px] uppercase text-pac-yellow-500 mb-2">BRANCH · {{ $num }}</p>
        <h3 class="font-display text-3xl uppercase text-pac-black-900 mb-3 group-hover:text-pac-yellow-500 transition-colors duration-200">
          {{ $branch->name }}
        </h3>

        @if($branch->branch_desc)
          <p class="font-body text-sm text-pac-black-600 leading-relaxed mb-4 line-clamp-3">{{ $branch->branch_desc }}</p>
        @else
          <p class="font-body text-sm text-pac-black-700 italic mb-4">소개 준비 중</p>
        @endif

        @if($branch->admin || $branch->operator)
          <div class="flex flex-wrap gap-4 pt-3 border-t border-pac-black-100">
            @if($branch->admin)
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-pac-yellow-500/20 flex items-center justify-center shrink-0">
                  <span class="font-display text-[9px] text-pac-yellow-500 font-bold">
                    {{ mb_strtoupper(mb_substr($branch->admin->nickname ?? '?', 0, 1)) }}
                  </span>
                </div>
                <div>
                  <p class="font-display text-[9px] tracking-[3px] uppercase text-pac-black-500">Branch Leader</p>
                  <p class="font-body text-xs font-semibold text-pac-black-300 leading-none mt-0.5">
                    {{ $branch->admin->nickname ?? $branch->admin->name ?? '지부장' }}
                  </p>
                </div>
              </div>
            @endif
            @if($branch->operator)
              <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-pac-pink-500/20 flex items-center justify-center shrink-0">
                  <span class="font-display text-[9px] text-pac-pink-500 font-bold">
                    {{ mb_strtoupper(mb_substr($branch->operator->nickname ?? '?', 0, 1)) }}
                  </span>
                </div>
                <div>
                  <p class="font-display text-[9px] tracking-[3px] uppercase text-pac-black-500">Operator</p>
                  <p class="font-body text-xs font-semibold text-pac-black-300 leading-none mt-0.5">
                    {{ $branch->operator->nickname ?? $branch->operator->name }}
                  </p>
                </div>
              </div>
            @endif
          </div>
        @endif
      </div>
    </a>
    @endforeach
  </div>
  @endif

  {{-- 하단 CTA --}}
  <div class="mt-16 border-t border-pac-black-100 pt-12 flex flex-col sm:flex-row items-start gap-4">
    <a href="{{ route('apply') }}" class="pac-btn">크루 합류하기 →</a>
    <a href="{{ route('events.index') }}" class="pac-btn-ghost">이벤트 보기</a>
  </div>

</div>
</x-app-layout>
