<x-admin-layout>

    <div class="space-y-6">

        {{-- 페이지 헤더 --}}
        <div>
            <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">관리자 대시보드</h1>
            <p class="font-body text-sm text-pac-black-400 mt-1">{{ now()->format('Y년 m월 d일') }} 기준</p>
        </div>

        {{-- 통계 카드 4개 --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-pac-yellow-500">
                <p class="font-body text-xs text-pac-black-400 uppercase tracking-wide mb-2">전체 구성원</p>
                <p class="font-display text-4xl font-bold text-pac-black-900 leading-none">
                    {{ number_format($stats['total_members']) }}
                    <span class="font-body text-sm font-normal text-pac-black-400">명</span>
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-pac-black-400">
                <p class="font-body text-xs text-pac-black-400 uppercase tracking-wide mb-2">누적 러닝 거리</p>
                <p class="font-display text-4xl font-bold text-pac-black-900 leading-none">
                    {{ number_format($stats['total_km'], 0) }}
                    <span class="font-body text-sm font-normal text-pac-black-400">km</span>
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-pac-green-500">
                <p class="font-body text-xs text-pac-black-400 uppercase tracking-wide mb-2">이번 달 기록 수</p>
                <p class="font-display text-4xl font-bold text-pac-black-900 leading-none">
                    {{ number_format($stats['monthly_logs']) }}
                    <span class="font-body text-sm font-normal text-pac-black-400">건</span>
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-pac-pink-500">
                <p class="font-body text-xs text-pac-black-400 uppercase tracking-wide mb-2">진행 중 이벤트</p>
                <p class="font-display text-4xl font-bold text-pac-pink-500 leading-none">
                    {{ $stats['active_events'] }}
                    <span class="font-body text-sm font-normal text-pac-black-400">개</span>
                </p>
            </div>

        </div>

        {{-- 빠른 메뉴 --}}
        <div class="bg-white rounded-2xl shadow-sm p-5">
            <h2 class="font-body text-base font-bold text-pac-black-900 mb-4">빠른 메뉴</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">

                <a href="{{ route('admin.members.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-xl border border-pac-black-100
                          hover:border-pac-yellow-400 hover:bg-pac-yellow-50 transition-colors duration-150 text-center">
                    <svg class="w-6 h-6 text-pac-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="font-body text-sm font-medium text-pac-black-700">구성원 관리</span>
                </a>

                <a href="{{ route('admin.applications.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-xl border border-pac-black-100
                          hover:border-pac-yellow-400 hover:bg-pac-yellow-50 transition-colors duration-150 text-center">
                    <svg class="w-6 h-6 text-pac-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="font-body text-sm font-medium text-pac-black-700">모집 신청서</span>
                </a>

                <a href="{{ route('admin.events.index') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-xl border border-pac-black-100
                          hover:border-pac-yellow-400 hover:bg-pac-yellow-50 transition-colors duration-150 text-center">
                    <svg class="w-6 h-6 text-pac-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="font-body text-sm font-medium text-pac-black-700">이벤트 관리</span>
                </a>

                <a href="{{ route('admin.notices.create') }}"
                   class="flex flex-col items-center gap-2 p-4 rounded-xl border border-pac-black-100
                          hover:border-pac-yellow-400 hover:bg-pac-yellow-50 transition-colors duration-150 text-center">
                    <svg class="w-6 h-6 text-pac-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="font-body text-sm font-medium text-pac-black-700">공지 작성</span>
                </a>

            </div>
        </div>

    </div>

</x-admin-layout>
