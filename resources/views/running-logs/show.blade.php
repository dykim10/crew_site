<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('running-logs.index') }}" class="text-gray-400 hover:text-gray-600">← 목록</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">러닝 기록 상세</h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                @if ($runningLog->image_url)
                    <img src="{{ $runningLog->image_url }}" class="w-full max-h-64 object-cover" alt="러닝 기록 이미지">
                @endif

                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-2xl font-bold text-gray-800">
                                {{ $runningLog->run_date->format('Y년 m월 d일') }}
                            </p>
                            @if ($runningLog->is_indoor)
                                <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-500 rounded">실내 (트레드밀)</span>
                            @else
                                <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-500 rounded">야외</span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('running-logs.edit', $runningLog) }}"
                                class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">수정</a>
                            <form method="POST" action="{{ route('running-logs.destroy', $runningLog) }}"
                                onsubmit="return confirm('이 기록을 삭제하시겠습니까?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-sm border border-red-200 text-red-500 rounded-lg hover:bg-red-50">삭제</button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4 py-4 border-t border-gray-100">
                        <div class="text-center">
                            <p class="text-xs text-gray-400">거리</p>
                            <p class="text-xl font-bold text-blue-600">{{ number_format($runningLog->distance_km, 2) }} km</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-400">운동 시간</p>
                            <p class="text-xl font-bold text-gray-700">{{ $runningLog->duration_formatted }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-400">평균 페이스</p>
                            <p class="text-xl font-bold text-gray-700">{{ $runningLog->avg_pace_formatted ?? '-' }}</p>
                        </div>
                    </div>

                    @if ($runningLog->calories || $runningLog->avg_heart_rate || $runningLog->elevation_m)
                        <div class="grid grid-cols-3 gap-4 py-4 border-t border-gray-100">
                            @if ($runningLog->calories)
                                <div class="text-center">
                                    <p class="text-xs text-gray-400">칼로리</p>
                                    <p class="text-lg font-semibold text-orange-500">{{ number_format($runningLog->calories) }} kcal</p>
                                </div>
                            @endif
                            @if ($runningLog->avg_heart_rate)
                                <div class="text-center">
                                    <p class="text-xs text-gray-400">평균 심박수</p>
                                    <p class="text-lg font-semibold text-red-500">{{ $runningLog->avg_heart_rate }} bpm</p>
                                </div>
                            @endif
                            @if ($runningLog->elevation_m)
                                <div class="text-center">
                                    <p class="text-xs text-gray-400">고도</p>
                                    <p class="text-lg font-semibold text-green-600">{{ $runningLog->elevation_m }} m</p>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if ($runningLog->memo)
                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-sm text-gray-400 mb-1">메모</p>
                            <p class="text-gray-700">{{ $runningLog->memo }}</p>
                        </div>
                    @endif

                    @if ($runningLog->parsed_data)
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg border-t border-gray-100">
                            <p class="text-xs font-medium text-blue-700 mb-2">AI 파싱 결과</p>
                            <pre class="text-xs text-blue-600 overflow-auto">{{ json_encode($runningLog->parsed_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
