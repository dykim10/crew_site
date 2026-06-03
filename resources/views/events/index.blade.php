<x-app-layout>
<div class="max-w-6xl mx-auto px-4 py-6 md:px-6 lg:px-8 space-y-8">

  {{-- 헤더 --}}
  <div>
    <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">B-TYPE EVENTS</p>
    <h1 class="font-display text-3xl font-bold text-pac-black-900 uppercase tracking-tight">이벤트</h1>
    <p class="font-body text-sm text-pac-black-400 mt-1">PAC-RUN 크루 참가형 이벤트 목록입니다.</p>
  </div>

  {{-- ① 진행 중 --}}
  @if($active->isNotEmpty())
  <section>
    <div class="flex items-center gap-3 mb-4">
      <span class="w-2 h-2 rounded-full bg-pac-pink-500 animate-pulse"></span>
      <h2 class="font-display text-xs font-bold text-pac-pink-500 uppercase tracking-widest">진행 중 LIVE</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach($active as $event)
      @include('events._card', ['event' => $event, 'statusColor' => 'pac-pink'])
      @endforeach
    </div>
  </section>
  @endif

  {{-- ② 예정 --}}
  @if($upcoming->isNotEmpty())
  <section>
    <div class="flex items-center gap-3 mb-4">
      <span class="w-2 h-2 rounded-full bg-pac-yellow-500"></span>
      <h2 class="font-display text-xs font-bold text-pac-yellow-600 uppercase tracking-widest">모집 예정 UPCOMING</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach($upcoming as $event)
      @include('events._card', ['event' => $event, 'statusColor' => 'pac-yellow'])
      @endforeach
    </div>
  </section>
  @endif

  {{-- ③ 종료 --}}
  @if($ended->isNotEmpty())
  <section>
    <div class="flex items-center gap-3 mb-4">
      <span class="w-2 h-2 rounded-full bg-pac-black-300"></span>
      <h2 class="font-display text-xs font-bold text-pac-black-400 uppercase tracking-widest">종료 ENDED</h2>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach($ended as $event)
      @include('events._card', ['event' => $event, 'statusColor' => 'pac-black'])
      @endforeach
    </div>
  </section>
  @endif

  {{-- 이벤트 없을 때 --}}
  @if($active->isEmpty() && $upcoming->isEmpty() && $ended->isEmpty())
  <div class="py-20 text-center">
    <svg class="w-12 h-12 mx-auto mb-3 text-pac-black-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
    </svg>
    <p class="font-body text-sm text-pac-black-400">등록된 이벤트가 없습니다.</p>
  </div>
  @endif

</div>
</x-app-layout>
