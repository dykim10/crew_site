<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-10 md:px-6 lg:px-8 space-y-10">

  <div>
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-4">Generation</p>
    <h1 class="font-display text-[clamp(32px,5vw,48px)] leading-none tracking-wide text-pac-black-900 uppercase">
      기수 <span class="text-pac-yellow-500">소개</span>
    </h1>
    <div class="w-20 h-0.5 bg-pac-yellow-500 mt-6"></div>
  </div>

  @if(session('error'))
    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
      {{ session('error') }}
    </div>
  @endif

  @if($empty)
    <div class="border border-pac-black-100 bg-white py-16 text-center rounded-2xl">
      <p class="font-display text-[10px] tracking-[4px] uppercase text-pac-black-500 mb-4">Not Available</p>
      <p class="font-body text-sm text-pac-black-600 leading-relaxed">
        현재 모집·운영 중인 기수가 없습니다.
      </p>
      <a href="{{ route('home') }}" class="inline-block mt-8 pac-btn-ghost">메인으로</a>
    </div>
  @else
    @foreach($cards as $card)
      @php
        $g = $card['generation'];
      @endphp
      <section class="bg-white rounded-2xl shadow-sm border border-pac-black-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-pac-black-100 flex flex-wrap items-start justify-between gap-3">
          <div>
            <h2 class="font-display text-xl font-bold text-pac-black-900 uppercase tracking-tight">
              {{ $g->display_name }}
            </h2>
            <p class="font-body text-sm text-pac-black-500 mt-2">
              운영 기간
              {{ $g->start_date?->format('Y.m.d') ?? '—' }}
              ~
              {{ $g->end_date?->format('Y.m.d') ?? '—' }}
            </p>
          </div>
          <span class="font-display text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full
            {{ $g->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-pac-yellow-500 text-pac-black-900' }}">
            {{ $g->status === 'active' ? '운영 중' : '모집·예정' }}
          </span>
        </div>

        <div class="px-6 py-5 space-y-6">
          <div>
            <h3 class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-2">목표 대회</h3>
            @if(count($card['main_races']))
              <ul class="space-y-1">
                @foreach($card['main_races'] as $race)
                  <li class="font-body text-sm text-pac-black-900">{{ $race['name'] ?: '—' }}</li>
                @endforeach
              </ul>
            @else
              <p class="font-body text-sm text-pac-black-500">등록된 목표 대회가 없습니다.</p>
            @endif
          </div>

          <div>
            <h3 class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-3">지부별 구성</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              @forelse($card['branches'] as $branch)
                <a href="{{ route('branch.show', $branch) }}"
                   class="block rounded-xl border border-pac-black-100 px-4 py-3 hover:border-pac-yellow-500 transition-colors">
                  <p class="font-body text-sm font-semibold text-pac-black-900">{{ $branch->name }}</p>
                  <p class="font-body text-xs text-pac-black-500 mt-1">
                    {{ $card['counts'][$branch->id] ?? 0 }}명
                  </p>
                </a>
              @empty
                <p class="font-body text-sm text-pac-black-500">활성화 지부가 없습니다.</p>
              @endforelse
            </div>
          </div>

          @if($card['recruiting'])
            <div class="pt-2">
              <a href="{{ route('apply', ['generation' => $g->id]) }}"
                 class="pac-btn inline-flex items-center justify-center min-w-[10rem]">
                {{ $g->number }}기 접수하기 →
              </a>
            </div>
          @endif
        </div>
      </section>
    @endforeach
  @endif
</div>
</x-app-layout>
