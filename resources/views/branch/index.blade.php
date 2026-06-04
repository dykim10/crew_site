<x-app-layout>
<div class="max-w-6xl mx-auto px-4 py-12 md:px-6 lg:px-8">

  {{-- 페이지 헤더 --}}
  <div class="mb-16">
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-4">지부 소개</p>
    <h1 class="font-display text-[clamp(48px,7vw,88px)] leading-none tracking-wide text-pac-black-900 uppercase">
      4개 지부<br>
      <span class="text-pac-yellow-500">하나의</span> 크루
    </h1>
    <div class="w-20 h-0.5 bg-pac-yellow-500 mt-6"></div>
  </div>

  {{-- 지부 카드 --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-px bg-pac-black-100 border border-pac-black-100">

    @php
    $branches = [
      [
        'num'     => '01',
        'region'  => 'BANPO · 반포',
        'name'    => '반포 지부',
        'slogan'  => '한강변을 달리며 자유를 느끼는 반포 러너들',
        'desc'    => '한강 시민공원을 따라 달리는 반포 지부. 한강의 일출과 함께 하루를 시작하는 러너들이 모여 있습니다.',
        'color'   => 'linear-gradient(160deg, #2d1a00 0%, #0d0d0d 100%)',
        'badge'   => '#E5AD16',
      ],
      [
        'num'     => '02',
        'region'  => 'YONSEI · 연대',
        'name'    => '연대 지부',
        'slogan'  => '캠퍼스와 도심을 누비는 연대 러닝팀',
        'desc'    => '연세대 캠퍼스와 신촌 도심을 배경으로 활동하는 연대 지부. 대학가의 에너지를 달리기로 표현합니다.',
        'color'   => 'linear-gradient(160deg, #001a2d 0%, #0d0d0d 100%)',
        'badge'   => '#E80043',
      ],
      [
        'num'     => '03',
        'region'  => 'GUNPO · 군포',
        'name'    => '군포 지부',
        'slogan'  => '수리산 트레일과 함께하는 군포 러너들',
        'desc'    => '수리산 둘레길을 기반으로 하는 군포 지부. 트레일과 로드를 넘나드는 다채로운 코스가 특징입니다.',
        'color'   => 'linear-gradient(160deg, #0d2200 0%, #0d0d0d 100%)',
        'badge'   => '#10b981',
      ],
      [
        'num'     => '04',
        'region'  => 'INCHEON · 인천',
        'name'    => '인천 지부',
        'slogan'  => '바다 내음과 함께 달리는 인천 러닝 패밀리',
        'desc'    => '인천 바다와 송도를 달리는 인천 지부. 탁 트인 해안선을 배경으로 달리는 특별한 경험을 제공합니다.',
        'color'   => 'linear-gradient(160deg, #001a2d 0%, #1a001a 100%)',
        'badge'   => '#6366f1',
      ],
    ];
    @endphp

    @foreach($branches as $branch)
    <div class="bg-pac-black-900 overflow-hidden group">
      {{-- 이미지 영역 --}}
      <div class="h-48 relative overflow-hidden" style="background:{{ $branch['color'] }};">
        <div class="absolute inset-0 flex items-center justify-center">
          <span class="font-display text-[100px] leading-none tracking-tight"
                style="color:rgba(255,255,255,0.04);">{{ $branch['num'] }}</span>
        </div>
        <div class="absolute bottom-4 left-5">
          <span class="font-display text-[10px] tracking-[4px] uppercase px-2 py-1"
                style="background:{{ $branch['badge'] }};color:#1A1212;">
            {{ explode(' · ', $branch['region'])[0] }}
          </span>
        </div>
      </div>
      {{-- 내용 --}}
      <div class="p-6 border-t border-pac-black-100">
        <p class="font-display text-[11px] tracking-[4px] uppercase text-pac-yellow-500 mb-2">
          {{ $branch['region'] }}
        </p>
        <h3 class="font-display text-3xl uppercase text-pac-black-900 mb-3 group-hover:text-pac-yellow-500 transition-colors duration-200">
          {{ $branch['name'] }}
        </h3>
        <p class="font-body text-sm text-pac-black-600 leading-relaxed mb-4">
          {{ $branch['desc'] }}
        </p>
        <p class="font-body text-xs text-pac-black-500 italic">{{ $branch['slogan'] }}</p>
      </div>
    </div>
    @endforeach

  </div>

  {{-- 하단 CTA --}}
  <div class="mt-16 border-t border-pac-black-100 pt-12 flex flex-col sm:flex-row items-start gap-4">
    <a href="{{ route('apply') }}" class="pac-btn">크루 합류하기 →</a>
    <a href="{{ route('events.index') }}" class="pac-btn-ghost">이벤트 보기</a>
  </div>

</div>
</x-app-layout>
