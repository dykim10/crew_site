<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 헤더 --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('bug-reports.create') }}"
           class="p-2 text-pac-black-400 hover:text-pac-black-700 transition-colors duration-150">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-0.5">SUPPORT</p>
            <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">버그 제보 상세</h1>
        </div>
    </div>

    {{-- 제보 내용 --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 bg-pac-black-900 flex items-center justify-between">
            <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">제보 내용</h3>
            @php
                $statusStyle = match($bugReport->status) {
                    'pending'     => 'bg-amber-500 text-white',
                    'in_progress' => 'bg-blue-500 text-white',
                    'resolved'    => 'bg-green-500 text-white',
                    'closed'      => 'bg-pac-black-500 text-white',
                    default       => 'bg-pac-black-500 text-white',
                };
            @endphp
            <span class="font-display text-[9px] font-bold uppercase tracking-widest px-2.5 py-1 rounded {{ $statusStyle }}">
                {{ $bugReport->status_label }}
            </span>
        </div>

        <div class="p-6 space-y-5">

            {{-- 제목 + 메타 --}}
            <div>
                <h2 class="font-body text-lg font-bold text-pac-black-900">{{ $bugReport->title }}</h2>
                <div class="flex items-center gap-3 mt-2">
                    @php
                        $priorityColor = match($bugReport->priority) {
                            'critical' => 'text-red-600',
                            'high'     => 'text-orange-500',
                            'medium'   => 'text-blue-600',
                            'low'      => 'text-pac-black-400',
                            default    => 'text-pac-black-400',
                        };
                    @endphp
                    <span class="font-display text-[9px] font-bold uppercase tracking-widest {{ $priorityColor }}">
                        {{ $bugReport->priority_label }}
                    </span>
                    <span class="text-pac-black-200">·</span>
                    <span class="font-body text-xs text-pac-black-400">
                        {{ $bugReport->created_at->format('Y.m.d H:i') }}
                    </span>
                </div>
            </div>

            <hr class="border-pac-black-100">

            {{-- 상세 내용 --}}
            <div>
                <p class="font-body text-sm font-semibold text-pac-black-600 mb-2">상세 내용</p>
                <p class="font-body text-sm text-pac-black-800 whitespace-pre-wrap leading-relaxed">{{ $bugReport->description }}</p>
            </div>

            {{-- 첨부 파일 --}}
            @if($bugReport->attachment_url)
                <div>
                    <p class="font-body text-sm font-semibold text-pac-black-600 mb-2">첨부 파일</p>
                    @php
                        $ext = strtolower(pathinfo($bugReport->attachment_name ?? '', PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    @endphp
                    @if($isImage)
                        <a href="{{ $bugReport->attachment_url }}" target="_blank" class="block">
                            <img src="{{ $bugReport->attachment_url }}"
                                 alt="첨부 이미지"
                                 class="max-w-full rounded-xl border border-pac-black-100 hover:opacity-90 transition-opacity duration-150">
                        </a>
                    @else
                        <a href="{{ $bugReport->attachment_url }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2.5 border border-pac-black-200 rounded-xl
                                  font-body text-sm text-pac-black-700 hover:bg-pac-black-50 transition-colors duration-150">
                            <svg class="w-4 h-4 text-pac-black-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $bugReport->attachment_name ?? '첨부파일 다운로드' }}
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>

    {{-- 관리자 응답 --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 bg-pac-black-900">
            <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">관리자 응답</h3>
        </div>

        <div class="p-6">
            @if($bugReport->admin_note)
                <div class="flex items-start gap-3">
                    <div class="shrink-0 w-8 h-8 rounded-full bg-pac-yellow-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-pac-black-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="font-body text-sm font-bold text-pac-black-900">PAC-RUN 관리자</span>
                            @if($bugReport->resolved_at)
                                <span class="font-body text-xs text-pac-black-400">
                                    {{ $bugReport->resolved_at->format('Y.m.d H:i') }}
                                </span>
                            @endif
                        </div>
                        <div class="bg-pac-black-50 rounded-xl px-4 py-3">
                            <p class="font-body text-sm text-pac-black-800 whitespace-pre-wrap leading-relaxed">{{ $bugReport->admin_note }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="py-8 text-center">
                    @if($bugReport->status === 'pending')
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-200 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            <span class="font-body text-sm text-amber-700">접수 완료 · 검토 대기 중입니다</span>
                        </div>
                    @elseif($bugReport->status === 'in_progress')
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                            <span class="font-body text-sm text-blue-700">현재 처리 중입니다</span>
                        </div>
                    @else
                        <p class="font-body text-sm text-pac-black-400">응답이 없습니다.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>
</x-app-layout>
