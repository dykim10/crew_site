<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 페이지 헤더 --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('running-logs.index') }}"
               class="font-display text-xs font-bold uppercase tracking-widest text-pac-black-400 hover:text-pac-black-600 transition-colors">
                ← 목록
            </a>
            <span class="text-pac-black-200">|</span>
            <h1 class="font-display text-xl font-bold text-pac-black-900 uppercase tracking-tight">기록 상세</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('running-logs.edit', $runningLog) }}"
               class="font-display text-xs font-bold uppercase tracking-widest px-3 py-1.5
                      border border-pac-black-300 text-pac-black-600 rounded-lg
                      hover:bg-pac-black-50 transition-colors">
                수정
            </a>
            <form method="POST" action="{{ route('running-logs.destroy', $runningLog) }}"
                  onsubmit="return confirm('이 기록을 삭제하시겠습니까?')">
                @csrf @method('DELETE')
                <button class="font-display text-xs font-bold uppercase tracking-widest px-3 py-1.5
                               border border-pac-pink-200 text-pac-pink-500 rounded-lg
                               hover:bg-pac-pink-50 transition-colors">
                    삭제
                </button>
            </form>
        </div>
    </div>

    {{-- 메인 카드 --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

        {{-- 이미지 --}}
        @if($runningLog->image_url)
            <img src="{{ $runningLog->image_url }}" class="w-full max-h-64 object-cover" alt="러닝 기록 이미지">
        @endif

        {{-- 날짜 + 유형 헤더 --}}
        <div class="flex items-center justify-between px-6 py-4 bg-pac-black-900">
            <h2 class="font-display text-xl font-bold text-white uppercase tracking-tight">
                {{ $runningLog->run_date->format('Y.m.d') }}
            </h2>
            <span class="font-display text-xs font-bold uppercase tracking-widest px-2.5 py-1 rounded
                         {{ $runningLog->is_indoor
                            ? 'bg-pac-black-700 text-pac-black-300'
                            : 'bg-pac-yellow-500 text-pac-black-900' }}">
                {{ $runningLog->is_indoor ? '실내' : '야외' }}
            </span>
        </div>

        {{-- 핵심 수치 3개 --}}
        <div class="grid grid-cols-3 divide-x divide-pac-black-50 border-b border-pac-black-50">
            <div class="p-5 text-center">
                <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-2">거리</p>
                <p class="font-display text-3xl font-bold text-pac-yellow-500 leading-none">
                    {{ number_format($runningLog->distance_km, 2) }}
                </p>
                <p class="font-body text-xs text-pac-black-400 mt-1">km</p>
            </div>
            <div class="p-5 text-center">
                <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-2">운동 시간</p>
                <p class="font-display text-3xl font-bold text-pac-black-900 leading-none">
                    {{ $runningLog->duration_formatted }}
                </p>
                <p class="font-body text-xs text-pac-black-400 mt-1">시간</p>
            </div>
            <div class="p-5 text-center">
                <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-2">평균 페이스</p>
                <p class="font-display text-3xl font-bold text-pac-black-900 leading-none">
                    {{ $runningLog->avg_pace_formatted ?? '—' }}
                </p>
                <p class="font-body text-xs text-pac-black-400 mt-1">/km</p>
            </div>
        </div>

        {{-- 부가 수치 --}}
        @if($runningLog->calories || $runningLog->avg_heart_rate || $runningLog->elevation_m)
            <div class="grid grid-cols-3 divide-x divide-pac-black-50 border-b border-pac-black-50">
                @if($runningLog->calories)
                    <div class="p-4 text-center">
                        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">칼로리</p>
                        <p class="font-display text-xl font-bold text-pac-pink-500">{{ number_format($runningLog->calories) }}</p>
                        <p class="font-body text-xs text-pac-black-400">kcal</p>
                    </div>
                @endif
                @if($runningLog->avg_heart_rate)
                    <div class="p-4 text-center">
                        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">평균 심박수</p>
                        <p class="font-display text-xl font-bold text-pac-pink-500">{{ $runningLog->avg_heart_rate }}</p>
                        <p class="font-body text-xs text-pac-black-400">bpm</p>
                    </div>
                @endif
                @if($runningLog->elevation_m)
                    <div class="p-4 text-center">
                        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">고도</p>
                        <p class="font-display text-xl font-bold text-pac-green-500">{{ $runningLog->elevation_m }}</p>
                        <p class="font-body text-xs text-pac-black-400">m</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- 메모 --}}
        @if($runningLog->memo)
            <div class="px-6 py-4 border-b border-pac-black-50">
                <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-2">메모</p>
                <p class="font-body text-sm text-pac-black-700">{{ $runningLog->memo }}</p>
            </div>
        @endif

        {{-- AI 파싱 결과 --}}
        @if($runningLog->parsed_data)
            <div class="px-6 py-4">
                <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-2">AI 파싱 결과</p>
                <pre class="font-body text-xs text-pac-black-500 bg-pac-black-50 rounded-xl p-4 overflow-auto">{{ json_encode($runningLog->parsed_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif

    </div>

</div>
</x-app-layout>
