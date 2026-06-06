<x-app-layout>
<div class="max-w-6xl mx-auto px-4 py-12 md:px-6 lg:px-8">

  {{-- 페이지 헤더 --}}
  <div class="mb-16">
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-4">About PAC-RUN</p>
    <h1 class="font-display text-[clamp(52px,8vw,96px)] leading-none tracking-wide text-pac-black-900 uppercase">
      PAC<span class="text-pac-yellow-500">-</span>RUN<br>
      <span class="text-pac-black-400" style="-webkit-text-stroke:1px rgba(255,255,255,0.15);">CREW</span>
    </h1>
    <div class="w-20 h-0.5 bg-pac-yellow-500 mt-6 mb-8"></div>
    <p class="font-body text-base text-pac-black-600 max-w-3xl leading-relaxed">
      PAC-RUN은 <strong class="text-pac-black-900">H</strong>igh <strong class="text-pac-black-900">I</strong>ntensity <strong class="text-pac-black-900">I</strong>nterval <strong class="text-pac-black-900">T</strong>raining을 기반으로 한
      <strong class="text-pac-black-900">P</strong>artnership <strong class="text-pac-black-900">A</strong>ctivation <strong class="text-pac-black-900">C</strong>rew입니다. 
    </p>
    <p class="font-body text-base text-pac-black-600 max-w-2xl leading-relaxed">
      2024년 창단 이래 반포·연대·군포·인천 {{ $branchCount }}개 지부에서 함께 달리고 있습니다.
    </p>
  </div>

  {{-- 가치 카드 --}}
  <div class="grid grid-cols-1 md:grid-cols-3 gap-px bg-pac-black-100 border border-pac-black-100 mb-16">
    <div class="bg-pac-black-900 p-8">
      <div class="font-display text-[11px] tracking-[4px] uppercase text-pac-yellow-500 mb-3">INTENSITY</div>
      <h3 class="font-display text-3xl uppercase text-pac-black-900 mb-4">고강도 훈련</h3>
      <p class="font-body text-sm text-pac-black-600 leading-relaxed">
        인터벌, 페이스 런, 장거리까지. 체계적인 훈련 프로그램으로 매 세션 최대치를 끌어냅니다.
      </p>
    </div>
    <div class="bg-pac-black-900 p-8">
      <div class="font-display text-[11px] tracking-[4px] uppercase text-pac-yellow-500 mb-3">PARTNERSHIP</div>
      <h3 class="font-display text-3xl uppercase text-pac-black-900 mb-4">함께 성장</h3>
      <p class="font-body text-sm text-pac-black-600 leading-relaxed">
        혼자 달리는 것과는 다릅니다. 크루원들과 함께 목표를 세우고, 달성하고, 기록합니다.
      </p>
    </div>
    <div class="bg-pac-black-900 p-8">
      <div class="font-display text-[11px] tracking-[4px] uppercase text-pac-yellow-500 mb-3">ACTIVATION</div>
      <h3 class="font-display text-3xl uppercase text-pac-black-900 mb-4">지역 활성화</h3>
      <p class="font-body text-sm text-pac-black-600 leading-relaxed">
        각 지부는 지역 러닝 문화를 만들어갑니다. 반포 한강, 연대 캠퍼스, 군포 수리산, 인천 바다.
      </p>
    </div>
  </div>

  {{-- 수치 --}}
  <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-pac-black-100 border border-pac-black-100 mb-16">
    @foreach([['240+','Active Runners'], ['4','지부'], ['12','진행 이벤트'], ['10,840km','Total Distance']] as $stat)
    <div class="bg-pac-black-900 px-8 py-10 text-center">
      <div class="font-display text-5xl text-pac-yellow-500 mb-2">{{ $stat[0] }}</div>
      <div class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-500">{{ $stat[1] }}</div>
    </div>
    @endforeach
  </div>

  {{-- CTA --}}
  <div class="flex flex-col sm:flex-row items-start gap-4 mb-24">
    <a href="{{ route('apply') }}" class="pac-btn">크루 합류하기 →</a>
    <a href="{{ route('branch') }}" class="pac-btn-ghost">지부 소개 보기</a>
  </div>

  {{-- 스폰서 / 협약업체 --}}
  @php
    $sponsors = \App\Models\Sponsor::where('is_active', true)->orderBy('sort_order')->get();
  @endphp
  @if($sponsors->isNotEmpty())
  <div class="border-t border-pac-black-100 pt-16">
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-4">Partners</p>
    <h2 class="font-display text-3xl uppercase text-pac-black-900 mb-8">스폰서 / 협약업체</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
      @foreach($sponsors as $sponsor)
      <div class="group flex flex-col items-center gap-3">
        @if($sponsor->link_url)
          <a href="{{ $sponsor->link_url }}" target="_blank" rel="noopener"
             class="w-full flex items-center justify-center rounded-lg border border-pac-black-100 bg-white p-4 h-20 hover:border-pac-yellow-500 hover:shadow-sm transition-all">
            @if($sponsor->logo_url)
              <img src="{{ $sponsor->logo_url }}" alt="{{ e($sponsor->name) }}"
                   class="max-h-10 max-w-full object-contain grayscale group-hover:grayscale-0 transition-all">
            @else
              <span class="font-display text-sm uppercase tracking-wide text-pac-black-600">{{ $sponsor->name }}</span>
            @endif
          </a>
        @else
          <div class="w-full flex items-center justify-center rounded-lg border border-pac-black-100 bg-white p-4 h-20">
            @if($sponsor->logo_url)
              <img src="{{ $sponsor->logo_url }}" alt="{{ e($sponsor->name) }}"
                   class="max-h-10 max-w-full object-contain grayscale">
            @else
              <span class="font-display text-sm uppercase tracking-wide text-pac-black-600">{{ $sponsor->name }}</span>
            @endif
          </div>
        @endif
        <p class="font-body text-xs text-pac-black-500 text-center">{{ $sponsor->name }}</p>
      </div>
      @endforeach
    </div>
  </div>
  @endif

</div>
</x-app-layout>
