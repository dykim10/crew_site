<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 페이지 헤더 --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('running-logs.index') }}"
           class="font-display text-xs font-bold uppercase tracking-widest text-pac-black-400 hover:text-pac-black-600 transition-colors">
            ← 목록
        </a>
        <span class="text-pac-black-200">|</span>
        <h1 class="font-display text-xl font-bold text-pac-black-900 uppercase tracking-tight">기록 수정</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-3.5 bg-pac-black-900">
            <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">
                {{ $runningLog->run_date->format('Y.m.d') }} 기록
            </h3>
        </div>

        <form method="POST" action="{{ route('running-logs.update', $runningLog) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            @if($runningLog->image_url)
                <div>
                    <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-2">등록된 이미지</p>
                    <img src="{{ $runningLog->image_url }}" class="h-32 rounded-xl object-cover" alt="">
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">
                        날짜 <span class="text-pac-pink-500">*</span>
                    </label>
                    <input type="date" name="run_date"
                           value="{{ old('run_date', $runningLog->run_date->format('Y-m-d')) }}"
                           class="w-full border-pac-black-200 rounded-xl shadow-sm text-sm
                                  focus:border-pac-yellow-500 focus:ring-pac-yellow-400" required>
                    @error('run_date') <p class="mt-1 font-body text-xs text-pac-pink-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">운동 유형</label>
                    <div class="flex gap-4 mt-2.5">
                        <label class="flex items-center gap-2 cursor-pointer font-body text-sm text-pac-black-700">
                            <input type="radio" name="is_indoor" value="0"
                                   {{ !$runningLog->is_indoor ? 'checked' : '' }}
                                   class="text-pac-yellow-500 focus:ring-pac-yellow-400"> 야외
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer font-body text-sm text-pac-black-700">
                            <input type="radio" name="is_indoor" value="1"
                                   {{ $runningLog->is_indoor ? 'checked' : '' }}
                                   class="text-pac-yellow-500 focus:ring-pac-yellow-400"> 실내
                        </label>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">
                        거리 (km) <span class="text-pac-pink-500">*</span>
                    </label>
                    <input type="number" name="distance_km"
                           value="{{ old('distance_km', $runningLog->distance_km) }}"
                           step="0.01" min="0.1" max="999"
                           class="w-full border-pac-black-200 rounded-xl shadow-sm text-sm
                                  focus:border-pac-yellow-500 focus:ring-pac-yellow-400" required>
                    @error('distance_km') <p class="mt-1 font-body text-xs text-pac-pink-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">
                        운동 시간 <span class="text-pac-pink-500">*</span>
                    </label>
                    <input type="text" name="duration"
                           value="{{ old('duration', $runningLog->duration_formatted) }}"
                           placeholder="0:30:00"
                           class="w-full border-pac-black-200 rounded-xl shadow-sm text-sm
                                  focus:border-pac-yellow-500 focus:ring-pac-yellow-400" required>
                    @error('duration') <p class="mt-1 font-body text-xs text-pac-pink-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">칼로리</label>
                    <div class="relative">
                        <input type="number" name="calories"
                               value="{{ old('calories', $runningLog->calories) }}" min="0"
                               class="w-full border-pac-black-200 rounded-xl shadow-sm text-sm pr-10
                                      focus:border-pac-yellow-500 focus:ring-pac-yellow-400">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 font-body text-xs text-pac-black-300">kcal</span>
                    </div>
                </div>
                <div>
                    <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">심박수</label>
                    <div class="relative">
                        <input type="number" name="avg_heart_rate"
                               value="{{ old('avg_heart_rate', $runningLog->avg_heart_rate) }}" min="0" max="300"
                               class="w-full border-pac-black-200 rounded-xl shadow-sm text-sm pr-10
                                      focus:border-pac-yellow-500 focus:ring-pac-yellow-400">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 font-body text-xs text-pac-black-300">bpm</span>
                    </div>
                </div>
                <div>
                    <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">고도</label>
                    <div class="relative">
                        <input type="number" name="elevation_m"
                               value="{{ old('elevation_m', $runningLog->elevation_m) }}" step="0.1"
                               class="w-full border-pac-black-200 rounded-xl shadow-sm text-sm pr-6
                                      focus:border-pac-yellow-500 focus:ring-pac-yellow-400">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 font-body text-xs text-pac-black-300">m</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">메모</label>
                <textarea name="memo" rows="2" maxlength="500"
                          class="w-full border-pac-black-200 rounded-xl shadow-sm text-sm resize-none
                                 focus:border-pac-yellow-500 focus:ring-pac-yellow-400
                                 font-body">{{ old('memo', $runningLog->memo) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('running-logs.index') }}"
                   class="font-body text-sm text-pac-black-400 hover:text-pac-black-600 transition-colors">
                    취소
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-7 py-3
                               bg-pac-yellow-500 hover:bg-pac-yellow-400
                               text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                               rounded-xl transition-colors duration-200">
                    수정 저장
                </button>
            </div>
        </form>
    </div>

</div>
</x-app-layout>
