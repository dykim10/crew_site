<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('notices.index') }}"
       class="font-display text-[10px] tracking-[3px] uppercase text-pac-yellow-500 hover:text-pac-yellow-400 transition-colors">
      공지사항
    </a>
    <span class="text-pac-black-700">›</span>
    <span class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600">상세</span>
  </div>

  <article class="bg-pac-black-900 border border-pac-black-100">
    <div class="px-6 pt-6 pb-5 border-b border-pac-black-100">
      @if($notice->is_pinned)
        <span class="inline-block font-display text-[9px] tracking-wider uppercase
                     bg-pac-pink-500 text-white px-1.5 py-0.5 mb-3">공지</span>
      @endif
      <h1 class="font-body text-xl font-bold text-pac-black-900 leading-snug mb-4">
        {{ $notice->title }}
      </h1>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="font-display text-[9px] tracking-wider uppercase
                       {{ $notice->target_type === 'all' ? 'text-pac-yellow-600' : 'text-pac-black-500' }}">
            {{ $notice->target_type === 'all' ? '전체 공지' : '지역 공지' }}
          </span>
          <span class="font-display text-[9px] tracking-wider uppercase text-pac-black-600">
            {{ $notice->created_at->format('Y.m.d H:i') }}
          </span>
        </div>
      </div>
    </div>

    <div class="px-6 py-6 min-h-[200px]">
      @if($notice->content)
        <div class="font-body text-sm text-pac-black-400 leading-relaxed whitespace-pre-wrap">
          {{ $notice->content }}
        </div>
      @else
        <p class="font-body text-sm text-pac-black-700 italic">내용 없음</p>
      @endif
    </div>
  </article>

  <div class="mt-6">
    <a href="{{ route('notices.index') }}" class="pac-btn-ghost">← 목록으로</a>
  </div>

</div>
</x-app-layout>
