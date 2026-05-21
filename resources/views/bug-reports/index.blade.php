<x-app-layout>
<div class="max-w-4xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 헤더 --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-0.5">SUPPORT</p>
            <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">버그 제보 내역</h1>
        </div>
        <a href="{{ route('bug-reports.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5
                  bg-pac-yellow-500 hover:bg-pac-yellow-400
                  text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                  rounded-xl transition-colors duration-150">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            버그 제보
        </a>
    </div>

    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- 목록 --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 bg-pac-black-900 flex items-center justify-between">
            <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">제보 목록</h3>
            <span class="font-body text-xs text-pac-black-400">총 {{ $reports->total() }}건</span>
        </div>

        @forelse($reports as $report)
            <a href="{{ route('bug-reports.show', $report) }}"
               class="flex items-start gap-4 px-5 py-4 border-b border-pac-black-50 last:border-0
                      hover:bg-pac-black-50 transition-colors duration-150 group">

                {{-- 상태 배지 --}}
                <div class="shrink-0 pt-0.5">
                    @php
                        $statusStyle = match($report->status) {
                            'pending'     => 'bg-amber-100 text-amber-700',
                            'in_progress' => 'bg-blue-100 text-blue-700',
                            'resolved'    => 'bg-green-100 text-green-700',
                            'closed'      => 'bg-pac-black-100 text-pac-black-500',
                            default       => 'bg-pac-black-100 text-pac-black-500',
                        };
                    @endphp
                    <span class="inline-block font-display text-[9px] font-bold uppercase tracking-widest
                                 px-2 py-0.5 rounded {{ $statusStyle }}">
                        {{ $report->status_label }}
                    </span>
                </div>

                {{-- 내용 --}}
                <div class="flex-1 min-w-0">
                    <p class="font-body text-sm font-semibold text-pac-black-900 group-hover:text-pac-yellow-600 truncate transition-colors duration-150">
                        {{ $report->title }}
                    </p>
                    <p class="font-body text-xs text-pac-black-400 mt-0.5 line-clamp-1">
                        {{ $report->description }}
                    </p>
                </div>

                {{-- 우측 메타 --}}
                <div class="shrink-0 text-right">
                    @php
                        $priorityStyle = match($report->priority) {
                            'critical' => 'text-red-600',
                            'high'     => 'text-orange-500',
                            'medium'   => 'text-pac-black-400',
                            'low'      => 'text-pac-black-300',
                            default    => 'text-pac-black-400',
                        };
                    @endphp
                    <p class="font-display text-[9px] font-bold uppercase tracking-widest {{ $priorityStyle }}">
                        {{ $report->priority_label }}
                    </p>
                    <p class="font-body text-xs text-pac-black-400 mt-0.5">
                        {{ $report->created_at->format('Y.m.d') }}
                    </p>
                </div>
            </a>
        @empty
            <div class="px-5 py-16 text-center">
                <p class="font-display text-3xl font-bold text-pac-black-100 uppercase tracking-widest mb-3">NO BUGS</p>
                <p class="font-body text-sm text-pac-black-400 mb-5">제보된 버그가 없습니다.<br>사이트 이용 중 불편한 점이 있으면 알려주세요.</p>
                <a href="{{ route('bug-reports.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5
                          bg-pac-yellow-500 hover:bg-pac-yellow-400
                          text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                          rounded-xl transition-colors duration-150">
                    첫 버그 제보하기
                </a>
            </div>
        @endforelse
    </div>

    @if($reports->hasPages())
        <div>{{ $reports->links() }}</div>
    @endif

</div>
</x-app-layout>
