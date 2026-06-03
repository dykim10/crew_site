<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-6 md:px-6 lg:px-8 space-y-5">

  <div>
    <p class="font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-[0.3em] mb-1">COMMUNITY</p>
    <h1 class="font-display text-4xl lg:text-5xl font-black text-white uppercase tracking-tight leading-none">
      공지 <span class="text-pac-yellow-400">사항</span>
    </h1>
  </div>

  @if($notices->isEmpty())
    <div class="bg-pac-black-900 border border-white/[0.05] px-5 py-20 text-center">
      <p class="font-display text-4xl font-black text-pac-black-700 uppercase tracking-widest mb-2">NO NOTICES</p>
      <p class="font-body text-sm text-pac-black-600">등록된 공지사항이 없습니다.</p>
    </div>
  @else
    <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
      @foreach($notices as $notice)
        <div class="px-5 py-4 border-b border-white/[0.04] last:border-0
                    hover:bg-white/[0.02] transition-colors
                    {{ $notice->is_pinned ? 'border-l-2 border-l-pac-pink-500' : '' }}">
          <div class="flex items-start gap-3">

            @if($notice->is_pinned)
              <span class="shrink-0 mt-0.5 font-display text-[9px] font-black uppercase tracking-wider
                           bg-pac-pink-500 text-white px-1.5 py-0.5">고정</span>
            @else
              <span class="w-1 h-1 rounded-full bg-pac-yellow-500 shrink-0 mt-2.5"></span>
            @endif

            <div class="flex-1 min-w-0">
              <p class="font-body text-sm font-semibold text-pac-black-200 leading-snug">
                {{ $notice->title }}
              </p>
              @if($notice->content)
                <p class="font-body text-xs text-pac-black-600 mt-1 line-clamp-2">
                  {!! strip_tags($notice->content) !!}
                </p>
              @endif
              <div class="flex items-center gap-3 mt-2">
                <span class="font-display text-[9px] text-pac-black-600 uppercase tracking-wider">
                  {{ $notice->created_at->format('Y.m.d') }}
                </span>
                <span class="font-display text-[9px] uppercase tracking-wider
                             {{ $notice->target_type === 'all' ? 'text-pac-yellow-600' : 'text-pac-black-500' }}">
                  {{ $notice->target_type === 'all' ? '전체' : '지역' }}
                </span>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    @if($notices->hasPages())
      <div class="flex justify-center">{{ $notices->links() }}</div>
    @endif
  @endif

</div>
</x-app-layout>
