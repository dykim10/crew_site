<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  {{-- 브레드크럼 --}}
  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('notices.index') }}"
       class="font-body text-xs text-pac-yellow-500 hover:text-pac-yellow-400 transition-colors">
      공지사항
    </a>
    <span class="text-pac-black-600 text-xs">›</span>
    <span class="font-body text-xs text-pac-black-500">상세</span>
  </div>

  <article class="bg-pac-black-900 border border-pac-black-100">

    {{-- 헤더 --}}
    <div class="px-6 pt-6 pb-5 border-b border-pac-black-100">
      @if($notice->is_pinned)
        <span class="inline-block font-display text-[9px] tracking-wider uppercase
                     bg-pac-pink-500 text-white px-1.5 py-0.5 mb-3">공지</span>
      @endif
      <h1 class="font-body text-xl font-bold text-white leading-snug mb-4">
        {{ $notice->title }}
      </h1>
      <div class="flex items-center justify-between flex-wrap gap-3">
        {{-- 작성자 --}}
        @php
          $author = $notice->author;
          $nick   = $author?->nickname ?? $author?->name ?? '관리자';
          $role   = $author?->role ?? 'operator';
          $bg     = match($role) { 'super_admin' => '#E80043', 'region_admin' => '#E5AD16', default => '#2D2020' };
          $txt    = ($role === 'region_admin') ? '#1A1212' : '#fff';
        @endphp
        <div class="flex items-center gap-2.5">
          <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0"
               style="background:{{ $bg }};">
            <span class="font-display text-xs leading-none" style="color:{{ $txt }};">
              {{ mb_strtoupper(mb_substr($nick, 0, 1)) }}
            </span>
          </div>
          <div>
            <p class="font-body text-sm font-semibold text-pac-black-200">{{ $nick }}</p>
            <p class="font-body text-xs text-pac-black-500">
              {{ $notice->created_at->format('Y.m.d H:i') }}
            </p>
          </div>
        </div>
        {{-- 대상 --}}
        <span class="font-display text-[9px] tracking-wider uppercase
                     {{ $notice->target_type === 'all' ? 'text-pac-yellow-600' : 'text-pac-black-500' }}">
          {{ $notice->target_type === 'all' ? '전체 공지' : '지역 공지' }}
        </span>
      </div>
    </div>

    {{-- 본문 --}}
    <div class="px-6 py-6 min-h-[200px]">
      @if($notice->content)
        <div class="tiptap-output font-body text-sm text-pac-black-400 leading-relaxed">
          {!! $notice->content !!}
        </div>
      @else
        <p class="font-body text-sm text-pac-black-700 italic">내용 없음</p>
      @endif
    </div>

  </article>

  <div class="flex items-center justify-between mt-6">
    <a href="{{ route('notices.index') }}" class="pac-btn-ghost text-sm">← 목록으로</a>
  </div>

</div>
</x-app-layout>
