<x-app-layout>
<div class="max-w-6xl mx-auto px-4 py-12 md:px-6 lg:px-8">

  {{-- 헤더 --}}
  <div class="mb-16">
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-4">Our Team</p>
    <h1 class="font-display text-[clamp(48px,7vw,88px)] leading-none tracking-wide text-pac-black-900 uppercase">
      PAC-RUN<br>
      <span class="text-pac-yellow-500">운영진</span>
    </h1>
    <div class="w-20 h-0.5 bg-pac-yellow-500 mt-6 mb-8"></div>
    <p class="font-body text-base text-pac-black-600 max-w-2xl leading-relaxed">
      PAC-RUN CREW를 이끄는 지부장·운영진·포토팀을 소개합니다.<br>
      함께 달리고, 기록하고, 크루를 만들어갑니다.
    </p>
  </div>

  @php
    $total = $members->flatten()->count();
  @endphp

  @if($total === 0)
    <div class="border border-pac-black-100 bg-pac-black-900 py-24 text-center">
      <p class="font-display text-2xl text-pac-black-500 uppercase tracking-widest">운영진 정보 준비 중</p>
    </div>
  @else
    @foreach($roleOrder as $roleKey)
      @if(!($members->get($roleKey)?->isNotEmpty()))
        @continue
      @endif
      @php $roleLabel = \App\Models\Administrator::ROLES[$roleKey]; @endphp

      <section class="mb-16 last:mb-0">
        <div class="flex items-end gap-4 mb-8">
          <h2 class="font-display text-3xl uppercase text-pac-black-900 tracking-wide">{{ $roleLabel }}</h2>
          <span class="font-display text-xs tracking-[3px] uppercase text-pac-black-500 pb-1">{{ $members->get($roleKey)->count() }}명</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-pac-black-100 border border-pac-black-100">
          @foreach($members->get($roleKey) as $member)
          <article class="bg-pac-black-900 p-8 flex flex-col group hover:bg-[#181818] transition-colors duration-300">

            {{-- 프로필 --}}
            <div class="flex items-start gap-5 mb-5">
              @if($member->public_profile_image_url)
                <img src="{{ $member->public_profile_image_url }}" alt="{{ $member->display_name }}"
                     class="w-20 h-20 rounded-full object-cover shrink-0 ring-2 ring-pac-yellow-500/30 group-hover:ring-pac-yellow-500/60 transition-all duration-300"
                     loading="lazy">
              @else
                <div class="w-20 h-20 rounded-full shrink-0 bg-pac-black-800 flex items-center justify-center ring-2 ring-white/5">
                  <span class="font-display text-2xl text-pac-yellow-500">{{ mb_substr($member->display_name, 0, 1) }}</span>
                </div>
              @endif

              <div class="min-w-0 pt-1">
                <h3 class="font-display text-xl uppercase text-pac-black-900 tracking-wide leading-tight">{{ $member->display_name }}</h3>
                <p class="font-body text-xs text-pac-yellow-500 mt-1">{{ $member->branch_display }}</p>
                <span class="inline-block mt-2 font-display text-[9px] tracking-[2px] uppercase px-2 py-0.5 border border-pac-black-100 text-pac-black-500">
                  {{ $member->role_label }}
                </span>
              </div>
            </div>

            @if($member->bio)
              <p class="font-body text-sm text-pac-black-600 leading-relaxed flex-1 mb-5 line-clamp-4">{{ $member->bio }}</p>
            @else
              <div class="flex-1 mb-5"></div>
            @endif

            {{-- SNS --}}
            @if($member->instagram_url || $member->youtube_url)
            <div class="flex items-center gap-3 pt-4 border-t border-pac-black-100">
              @if($member->instagram_url)
                <a href="{{ $member->instagram_url }}" target="_blank" rel="noopener"
                   class="font-display text-[10px] tracking-[2px] uppercase text-pac-black-500 hover:text-pac-yellow-500 transition-colors">
                  Instagram →
                </a>
              @endif
              @if($member->youtube_url)
                <a href="{{ $member->youtube_url }}" target="_blank" rel="noopener"
                   class="font-display text-[10px] tracking-[2px] uppercase text-pac-black-500 hover:text-pac-yellow-500 transition-colors">
                  YouTube →
                </a>
              @endif
            </div>
            @endif

          </article>
          @endforeach
        </div>
      </section>
    @endforeach
  @endif

  {{-- CTA --}}
  <div class="flex flex-col sm:flex-row items-start gap-4 mt-20 pt-12 border-t border-pac-black-100">
    <a href="{{ route('apply') }}" class="pac-btn">크루 합류하기 →</a>
    <a href="{{ route('branch') }}" class="pac-btn-ghost">지부 소개 보기</a>
  </div>

</div>
</x-app-layout>
