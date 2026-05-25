<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 헤더 --}}
    <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">NOTICES</p>
        <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">공지사항</h1>
    </div>

    @if($notices->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm px-5 py-16 text-center">
            <p class="font-display text-3xl font-bold text-pac-black-100 uppercase tracking-widest mb-3">NO NOTICES</p>
            <p class="font-body text-sm text-pac-black-400">등록된 공지사항이 없습니다.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            @foreach($notices as $notice)
                <div class="px-5 py-4 border-b border-pac-black-50 last:border-0">
                    <div class="flex items-start gap-3">
                        @if($notice->is_pinned)
                            <span class="font-display text-[10px] font-bold uppercase tracking-widest shrink-0 mt-0.5
                                         bg-pac-pink-500 text-white px-2 py-0.5 rounded">
                                고정
                            </span>
                        @else
                            <span class="w-1.5 h-1.5 rounded-full bg-pac-yellow-500 shrink-0 mt-2"></span>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-body text-sm font-semibold text-pac-black-900">{{ $notice->title }}</p>
                            @if($notice->content)
                                <p class="font-body text-xs text-pac-black-500 mt-1 line-clamp-2">
                                    {!! strip_tags($notice->content) !!}
                                </p>
                            @endif
                            <div class="flex items-center gap-3 mt-2">
                                <span class="font-body text-xs text-pac-black-400">
                                    {{ $notice->created_at->format('Y.m.d') }}
                                </span>
                                <span class="font-body text-xs text-pac-black-400">
                                    {{ $notice->target_type === 'all' ? '전체' : '지역' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 페이지네이션 --}}
        @if($notices->hasPages())
            <div class="flex justify-center pt-2">
                {{ $notices->links() }}
            </div>
        @endif
    @endif

</div>
</x-app-layout>
