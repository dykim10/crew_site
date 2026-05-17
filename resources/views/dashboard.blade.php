<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            대시보드
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <p class="text-sm text-gray-500">이번 달 거리</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1">- km</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <p class="text-sm text-gray-500">이번 달 기록 수</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">- 회</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <p class="text-sm text-gray-500">평균 페이스</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">-'--"</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">최근 러닝 기록</h3>
                    <a href="{{ route('running-logs.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        기록 등록
                    </a>
                </div>
                <p class="text-gray-500 text-sm">
                    <a href="{{ route('running-logs.index') }}" class="text-blue-600 hover:underline">
                        러닝 기록 전체 보기
                    </a>
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
