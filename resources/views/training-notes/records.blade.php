<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-6 md:px-6 space-y-5">

  <div>
    <a href="{{ route('training-notes.index') }}" class="font-body text-sm text-white/40 hover:text-pac-yellow-400">&larr; 훈련노트</a>
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mt-4 mb-2">PERSONAL BEST</p>
    <h1 class="font-display text-3xl text-white uppercase tracking-wide">PB 관리</h1>
    <div class="w-12 h-0.5 bg-pac-yellow-500 mt-3"></div>
  </div>

  @if(session('success'))
    <div class="px-4 py-3 bg-pac-green-500/10 border border-pac-green-500/30 text-pac-green-500 text-sm">{{ session('success') }}</div>
  @endif

  {{-- 최신 PB 요약 --}}
  @if($latestByDistance->isNotEmpty())
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
      @foreach($latestByDistance as $rec)
        <div class="bg-pac-black-900 border-t-2 border-t-pac-yellow-500 border border-white/[0.05] px-4 py-3">
          <p class="font-display text-[9px] text-pac-black-500 uppercase tracking-widest">{{ $rec->distance_type }}</p>
          <p class="font-display text-xl text-white mt-1">{{ $rec->record_formatted }}</p>
          <p class="font-body text-xs text-white/40 mt-1">{{ $rec->achieved_at->format('Y.m.d') }}</p>
        </div>
      @endforeach
    </div>
  @endif

  {{-- 등록 폼 --}}
  <form method="POST" action="{{ route('training-notes.records.store') }}"
        class="bg-pac-black-900 border border-white/[0.05] p-5 space-y-4">
    @csrf
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">거리</label>
        <select name="distance_type" required
                class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
          @foreach(['1K','5K','10K','HALF','FULL'] as $d)
            <option value="{{ $d }}" @selected(old('distance_type') === $d)>{{ $d }}</option>
          @endforeach
        </select>
        @error('distance_type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">기록 (MM:SS 또는 H:MM:SS)</label>
        <input type="text" name="record_time" value="{{ old('record_time') }}" required placeholder="22:30"
               class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        @error('record_time')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">달성일</label>
        <input type="date" name="achieved_at" value="{{ old('achieved_at', now()->toDateString()) }}" required max="{{ now()->toDateString() }}"
               class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        @error('achieved_at')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
    </div>
    <button type="submit"
            class="px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900 font-display text-sm font-black uppercase tracking-wider">
      PB 등록
    </button>
  </form>

  {{-- 이력 --}}
  <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
    <div class="px-5 py-3 border-b border-white/[0.06] font-display text-xs text-white uppercase tracking-widest">전체 이력</div>
    @forelse($records as $rec)
      <div class="flex items-center justify-between px-5 py-3 border-b border-white/[0.04] last:border-0">
        <div>
          <span class="font-display text-sm text-pac-yellow-400">{{ $rec->distance_type }}</span>
          <span class="font-body text-white ml-3">{{ $rec->record_formatted }}</span>
          <span class="font-body text-white/40 text-sm ml-2">{{ $rec->achieved_at->format('Y.m.d') }}</span>
        </div>
        <form method="POST" action="{{ route('training-notes.records.destroy', $rec) }}"
              onsubmit="return confirm('삭제하시겠습니까?')">
          @csrf @method('DELETE')
          <button type="submit" class="text-white/30 hover:text-red-400 text-xs">삭제</button>
        </form>
      </div>
    @empty
      <p class="px-5 py-8 text-center text-white/30 font-body text-sm">등록된 PB가 없습니다.</p>
    @endforelse
  </div>
</div>
</x-app-layout>
