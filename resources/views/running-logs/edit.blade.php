<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">러닝 기록 수정</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

                <form method="POST" action="{{ route('running-logs.update', $runningLog) }}">
                    @csrf
                    @method('PUT')

                    @if ($runningLog->image_url)
                        <div class="mb-6">
                            <p class="text-sm font-medium text-gray-700 mb-2">등록된 이미지</p>
                            <img src="{{ $runningLog->image_url }}" class="h-32 rounded-lg object-cover" alt="">
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">날짜 <span class="text-red-500">*</span></label>
                            <input type="date" name="run_date"
                                value="{{ old('run_date', $runningLog->run_date->format('Y-m-d')) }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error('run_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">운동 유형</label>
                            <div class="flex gap-3 mt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="is_indoor" value="0"
                                        {{ !$runningLog->is_indoor ? 'checked' : '' }} class="text-blue-600"> 야외
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="is_indoor" value="1"
                                        {{ $runningLog->is_indoor ? 'checked' : '' }} class="text-blue-600"> 실내(트레드밀)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">거리 (km) <span class="text-red-500">*</span></label>
                            <input type="number" name="distance_km"
                                value="{{ old('distance_km', $runningLog->distance_km) }}"
                                step="0.01" min="0.1" max="999"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error('distance_km') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">운동 시간 <span class="text-red-500">*</span></label>
                            <input type="text" name="duration"
                                value="{{ old('duration', $runningLog->duration_formatted) }}"
                                placeholder="0:30:00 (시:분:초)"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error('duration') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">칼로리 (kcal)</label>
                            <input type="number" name="calories"
                                value="{{ old('calories', $runningLog->calories) }}" min="0"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">평균 심박수</label>
                            <input type="number" name="avg_heart_rate"
                                value="{{ old('avg_heart_rate', $runningLog->avg_heart_rate) }}" min="0" max="300"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">고도 (m)</label>
                            <input type="number" name="elevation_m"
                                value="{{ old('elevation_m', $runningLog->elevation_m) }}" step="0.1"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">메모</label>
                        <textarea name="memo" rows="2" maxlength="500"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('memo', $runningLog->memo) }}</textarea>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('running-logs.index') }}" class="text-sm text-gray-500 hover:underline">취소</a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            수정 저장
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
