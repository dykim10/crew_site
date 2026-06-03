<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-6 md:px-6 lg:px-8 space-y-6">

  <div>
    <p class="font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-[0.3em] mb-1">LEADERBOARD</p>
    <h1 class="font-display text-4xl lg:text-5xl font-black text-white uppercase tracking-tight leading-none">
      랭킹 <span class="text-pac-yellow-400">순위</span>
    </h1>
  </div>

  {{-- 이번달 개인 순위 --}}
  <section>
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-display text-[10px] font-bold text-pac-yellow-500 uppercase tracking-[0.2em]">
        {{ $year }}년 {{ $month }}월 개인 순위
      </h2>
      <span class="font-display text-[9px] text-pac-black-600 uppercase tracking-wider">확정 기록 기준</span>
    </div>

    @if($monthly->isEmpty())
      <div class="bg-pac-black-900 border border-white/[0.05] px-5 py-12 text-center">
        <p class="font-body text-sm text-pac-black-600">이번 달 확정 기록이 없습니다.</p>
      </div>
    @else
      <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
        @foreach($monthly as $i => $row)
          @php $isMe = auth()->id() == $row->id; @endphp
          <div class="flex items-center gap-4 px-5 py-4 border-b border-white/[0.04] last:border-0
                      {{ $isMe ? 'bg-pac-yellow-500/8 border-l-2 border-l-pac-yellow-400' : 'hover:bg-white/[0.02]' }}
                      transition-colors">

            {{-- 순위 --}}
            <div class="w-10 shrink-0 flex items-center justify-center">
              @if($i === 0)
                <span class="font-display text-2xl font-black text-pac-yellow-400 leading-none">1</span>
              @elseif($i === 1)
                <span class="font-display text-2xl font-black text-pac-black-400 leading-none">2</span>
              @elseif($i === 2)
                <span class="font-display text-2xl font-black leading-none" style="color:#cd7f32">3</span>
              @else
                <span class="font-display text-sm font-bold text-pac-black-600">{{ $i + 1 }}</span>
              @endif
            </div>

            <div class="flex-1 min-w-0">
              <p class="font-body text-sm font-semibold truncate
                         {{ $isMe ? 'text-pac-yellow-400' : 'text-pac-black-200' }}">
                {{ $row->nickname ?? '(이름 없음)' }}
                @if($isMe)
                  <span class="font-display text-[9px] text-pac-yellow-600 ml-1 uppercase tracking-wider">ME</span>
                @endif
              </p>
              <p class="font-display text-[9px] text-pac-black-600 uppercase tracking-wider mt-0.5">
                {{ $row->run_count }}회 달림
              </p>
            </div>

            <p class="font-display text-xl font-black shrink-0 {{ $isMe ? 'text-pac-yellow-400' : 'text-white' }}">
              {{ number_format($row->total_km, 1) }}
              <span class="font-body text-xs font-normal text-pac-black-500">km</span>
            </p>
          </div>
        @endforeach
      </div>
    @endif
  </section>

  {{-- 조별 순위 --}}
  @if($groupRanking->isNotEmpty())
  <section>
    <h2 class="font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-[0.2em] mb-3">
      {{ $year }}년 {{ $month }}월 조별 순위
    </h2>
    <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
      @foreach($groupRanking as $i => $row)
        <div class="flex items-center gap-4 px-5 py-4 border-b border-white/[0.04] last:border-0 hover:bg-white/[0.02]">
          <div class="w-10 flex items-center justify-center shrink-0">
            <span class="font-display text-sm font-bold text-pac-black-600">{{ $i + 1 }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="font-body text-sm font-semibold text-pac-black-200 truncate">{{ $row->group_name }}</p>
            <p class="font-display text-[9px] text-pac-black-600 uppercase tracking-wider mt-0.5">{{ $row->member_count }}명 참여</p>
          </div>
          <p class="font-display text-xl font-black text-white shrink-0">
            {{ number_format($row->total_km, 1) }}
            <span class="font-body text-xs font-normal text-pac-black-500">km</span>
          </p>
        </div>
      @endforeach
    </div>
  </section>
  @endif

  {{-- 연간 Top 10 --}}
  <section>
    <h2 class="font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-[0.2em] mb-3">
      {{ $year }}년 연간 Top 10
    </h2>
    @if($yearly->isEmpty())
      <div class="bg-pac-black-900 border border-white/[0.05] px-5 py-12 text-center">
        <p class="font-body text-sm text-pac-black-600">연간 확정 기록이 없습니다.</p>
      </div>
    @else
      <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
        @foreach($yearly as $i => $row)
          @php $isMe = auth()->id() == $row->id; @endphp
          <div class="flex items-center gap-4 px-5 py-4 border-b border-white/[0.04] last:border-0
                      {{ $isMe ? 'bg-pac-yellow-500/8 border-l-2 border-l-pac-yellow-400' : 'hover:bg-white/[0.02]' }}">
            <div class="w-10 flex items-center justify-center shrink-0">
              <span class="font-display text-sm font-bold text-pac-black-600">{{ $i + 1 }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-body text-sm font-semibold truncate {{ $isMe ? 'text-pac-yellow-400' : 'text-pac-black-200' }}">
                {{ $row->nickname ?? '(이름 없음)' }}
                @if($isMe) <span class="font-display text-[9px] text-pac-yellow-600 ml-1 uppercase tracking-wider">ME</span> @endif
              </p>
            </div>
            <p class="font-display text-xl font-black shrink-0 {{ $isMe ? 'text-pac-yellow-400' : 'text-white' }}">
              {{ number_format($row->total_km, 1) }}
              <span class="font-body text-xs font-normal text-pac-black-500">km</span>
            </p>
          </div>
        @endforeach
      </div>
    @endif
  </section>

</div>
</x-app-layout>
