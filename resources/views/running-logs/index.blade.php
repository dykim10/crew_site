<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">내 러닝 기록</h2>
            <a href="{{ route('running-logs.create') }}"
                class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                기록 등록
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 이달 통계 --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 text-center">
                    <p class="text-xs text-gray-500">이번 달 거리</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($monthlyStats['total_km'], 1) }} km</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 text-center">
                    <p class="text-xs text-gray-500">기록 횟수</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $monthlyStats['total_count'] }} 회</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 text-center">
                    <p class="text-xs text-gray-500">평균 페이스</p>
                    @php
                        $pace = $monthlyStats['avg_pace'];
                        $paceStr = $pace
                            ? sprintf("%d'%02d\"", intdiv($pace, 60), $pace % 60)
                            : '-';
                    @endphp
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $paceStr }}</p>
                </div>
            </div>

            {{-- 기록 목록 --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                @forelse ($logs as $log)
                    <div class="flex items-center justify-between p-4 border-b border-gray-50 last:border-0 hover:bg-gray-50">
                        <div class="flex items-center gap-4">
                            @if ($log->image_url)
                                <img src="{{ $log->image_url }}" class="w-12 h-12 rounded-lg object-cover" alt="">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-400 text-xl">
                                    🏃
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-800">
                                    {{ $log->run_date->format('Y.m.d') }}
                                    @if ($log->is_indoor)
                                        <span class="ml-1 text-xs px-1.5 py-0.5 bg-gray-100 text-gray-500 rounded">실내</span>
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 mt-0.5">
                                    {{ number_format($log->distance_km, 2) }} km
                                    · {{ $log->duration_formatted }}
                                    @if ($log->avg_pace_formatted)
                                        · {{ $log->avg_pace_formatted }}/km
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('running-logs.show', $log) }}"
                                class="text-sm text-blue-600 hover:underline">상세</a>
                            <a href="{{ route('running-logs.edit', $log) }}"
                                class="text-sm text-gray-500 hover:underline">수정</a>
                            <form method="POST" action="{{ route('running-logs.destroy', $log) }}"
                                onsubmit="return confirm('이 기록을 삭제하시겠습니까?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm text-red-400 hover:underline">삭제</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-400">
                        <p class="text-lg">아직 러닝 기록이 없습니다.</p>
                        <a href="{{ route('running-logs.create') }}"
                            class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            첫 기록 등록하기
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
