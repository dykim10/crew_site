<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">러닝 기록 등록</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

                <form method="POST" action="{{ route('running-logs.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- 이미지 업로드 --}}
                    <div x-data="{ preview: null }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">러닝 앱 스크린샷 (선택)</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" name="image" accept="image/*" class="hidden" id="imageInput"
                                @change="preview = URL.createObjectURL($event.target.files[0])">
                            <label for="imageInput" class="cursor-pointer">
                                <template x-if="!preview">
                                    <div>
                                        <p class="text-gray-400 text-sm">이미지를 선택하면 AI가 기록을 자동 파싱합니다</p>
                                        <span class="mt-2 inline-block px-4 py-2 bg-gray-100 text-gray-600 text-sm rounded-lg hover:bg-gray-200">
                                            이미지 선택
                                        </span>
                                    </div>
                                </template>
                                <template x-if="preview">
                                    <img :src="preview" class="max-h-48 mx-auto rounded-lg">
                                </template>
                            </label>
                        </div>
                        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">날짜 <span class="text-red-500">*</span></label>
                            <input type="date" name="run_date" value="{{ old('run_date', date('Y-m-d')) }}"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error('run_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">운동 유형</label>
                            <div class="flex gap-3 mt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="is_indoor" value="0" checked class="text-blue-600"> 야외
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="is_indoor" value="1" class="text-blue-600"> 실내(트레드밀)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">거리 (km) <span class="text-red-500">*</span></label>
                            <input type="number" name="distance_km" value="{{ old('distance_km') }}"
                                step="0.01" min="0.1" max="999" placeholder="5.00"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error('distance_km') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">운동 시간 <span class="text-red-500">*</span></label>
                            <input type="text" name="duration" value="{{ old('duration') }}"
                                placeholder="0:30:00 (시:분:초)"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            @error('duration') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">칼로리 (kcal)</label>
                            <input type="number" name="calories" value="{{ old('calories') }}"
                                min="0" placeholder="300"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">평균 심박수</label>
                            <input type="number" name="avg_heart_rate" value="{{ old('avg_heart_rate') }}"
                                min="0" max="300" placeholder="150"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">고도 (m)</label>
                            <input type="number" name="elevation_m" value="{{ old('elevation_m') }}"
                                step="0.1" placeholder="0"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">메모</label>
                        <textarea name="memo" rows="2" maxlength="500"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="오늘의 러닝 한 줄 메모">{{ old('memo') }}</textarea>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <a href="{{ route('running-logs.index') }}"
                            class="text-sm text-gray-500 hover:underline">취소</a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            기록 저장
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
