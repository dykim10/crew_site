<x-app-layout>
<div class="max-w-5xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- ── 페이지 헤더 ── --}}
  <div class="mb-8">
    <p class="font-display text-[10px] tracking-[4px] uppercase text-pac-yellow-500 mb-2">Community</p>
    <h1 class="font-display text-4xl lg:text-5xl uppercase tracking-tight text-pac-black-900 leading-none">
      공지사항
    </h1>
    <p class="font-body text-sm text-pac-black-600 mt-2">크루 공지 및 안내</p>
  </div>

  @if($notices->isEmpty())
  <div class="border border-pac-black-100 bg-pac-black-900 py-20 text-center">
    <p class="font-display text-4xl font-black text-pac-black-700 uppercase tracking-widest mb-2">NO NOTICES</p>
    <p class="font-body text-sm text-pac-black-600">등록된 공지사항이 없습니다.</p>
  </div>

  @else
  <div class="border border-pac-black-100 bg-pac-black-900 overflow-hidden">
    <div class="hidden sm:grid grid-cols-[1fr_auto_auto] gap-x-4 px-5 py-2.5
                border-b border-pac-black-100 bg-pac-black-800">
      <span class="font-display text-[9px] tracking-[3px] uppercase text-pac-black-600">제목</span>
      <span class="font-display text-[9px] tracking-[3px] uppercase text-pac-black-600 w-16 text-center">구분</span>
      <span class="font-display text-[9px] tracking-[3px] uppercase text-pac-black-600 w-20 text-center">날짜</span>
    </div>

    @foreach($notices as $notice)
    <a href="{{ route('notices.show', $notice) }}"
       class="group flex sm:grid sm:grid-cols-[1fr_auto_auto] sm:gap-x-4
              items-start sm:items-center px-5 py-3.5
              border-b border-pac-black-100/50 last:border-0
              hover:bg-white/[0.025] transition-colors duration-150
              {{ $notice->is_pinned ? 'border-l-2 border-l-pac-pink-500' : '' }}"
       style="text-decoration:none;">
      <div class="flex-1 min-w-0 flex items-start gap-3">
        @if($notice->is_pinned)
          <span class="shrink-0 mt-0.5 font-display text-[9px] tracking-wider uppercase bg-pac-pink-500 text-white px-1.5 py-0.5">고정</span>
        @else
          <span class="w-1 h-1 rounded-full bg-pac-yellow-500 shrink-0 mt-2.5"></span>
        @endif
        <div class="min-w-0">
          <p class="font-body text-sm font-semibold text-pac-black-200 leading-snug
                    group-hover:text-pac-yellow-400 transition-colors truncate">
            {{ $notice->title }}
          </p>
          @if($notice->content)
            <p class="font-body text-xs text-pac-black-600 mt-0.5 line-clamp-1">
              {!! strip_tags($notice->content) !!}
            </p>
          @endif
          <div class="sm:hidden flex items-center gap-2 mt-1">
            <span class="font-display text-[9px] tracking-wider text-pac-black-600">{{ $notice->created_at->format('Y.m.d') }}</span>
          </div>
        </div>
      </div>
      <span class="hidden sm:block font-display text-[9px] tracking-wider uppercase w-16 text-center shrink-0
                   {{ $notice->target_type === 'all' ? 'text-pac-yellow-600' : 'text-pac-black-500' }}">
        {{ $notice->target_type === 'all' ? '전체' : '지역' }}
      </span>
      <span class="hidden sm:block font-display text-[10px] text-pac-black-600 w-20 text-center shrink-0">
        {{ $notice->created_at->format('Y.m.d') }}
      </span>
    </a>
    @endforeach
  </div>

  @if($notices->hasPages())
    <div class="flex justify-center mt-6">{{ $notices->links() }}</div>
  @endif
  @endif

</div>
</x-app-layout>
