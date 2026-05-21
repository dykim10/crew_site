<x-app-layout>
<div class="max-w-xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4"
     x-data="runLogCreate()">

    {{-- 페이지 헤더 --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-0.5">MY RUNNING</p>
            <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">기록 등록</h1>
        </div>
        <a href="{{ route('running-logs.index') }}"
           class="font-display text-xs font-bold uppercase tracking-widest text-pac-black-400 hover:text-pac-black-600 transition-colors">
            ← 목록
        </a>
    </div>

    {{-- 단계 표시 --}}
    <div class="flex items-center gap-2">
        <div class="flex items-center gap-2">
            <div :class="phase === 'upload' || phase === 'parsing'
                    ? 'bg-pac-yellow-500 text-pac-black-900'
                    : 'bg-pac-green-500 text-white'"
                 class="w-6 h-6 rounded-full flex items-center justify-center transition-colors">
                <template x-if="phase === 'confirm'">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <template x-if="phase !== 'confirm'">
                    <span class="font-display text-xs font-bold">1</span>
                </template>
            </div>
            <span class="font-display text-xs font-bold uppercase tracking-widest"
                  :class="phase !== 'confirm' ? 'text-pac-yellow-600' : 'text-pac-green-500'">
                이미지 업로드
            </span>
        </div>
        <div class="flex-1 h-px bg-pac-black-100 mx-2"></div>
        <div class="flex items-center gap-2">
            <div :class="phase === 'confirm' ? 'bg-pac-yellow-500 text-pac-black-900' : 'bg-pac-black-100 text-pac-black-400'"
                 class="w-6 h-6 rounded-full flex items-center justify-center transition-colors">
                <span class="font-display text-xs font-bold">2</span>
            </div>
            <span class="font-display text-xs font-bold uppercase tracking-widest"
                  :class="phase === 'confirm' ? 'text-pac-yellow-600' : 'text-pac-black-400'">
                확인 및 등록
            </span>
        </div>
    </div>

    {{-- [1단계] 업로드 --}}
    <div x-show="phase === 'upload'">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6">
                <input type="file" id="imageInput" accept="image/*" class="hidden"
                       @change="handleFileSelect($event)">

                <label for="imageInput" class="block cursor-pointer group">
                    <div class="border-2 border-dashed border-pac-black-100
                                group-hover:border-pac-yellow-400 group-hover:bg-pac-black-50
                                rounded-2xl p-10 text-center transition-all duration-200">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl
                                    bg-pac-black-900 group-hover:bg-pac-black-800 mb-4 transition-colors">
                            <svg class="w-8 h-8 text-pac-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                            </svg>
                        </div>
                        <p class="font-body text-pac-black-700 font-semibold text-base mb-1">
                            러닝 앱 스크린샷 업로드
                        </p>
                        <p class="font-body text-pac-black-400 text-sm mb-5">
                            AI가 기록·날짜를 자동으로 파싱합니다
                        </p>
                        <span class="inline-flex items-center gap-2 px-7 py-3
                                     bg-pac-yellow-500 group-hover:bg-pac-yellow-400
                                     text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                                     rounded-xl transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            이미지 선택하기
                        </span>
                        <p class="font-body text-xs text-pac-black-300 mt-4">JPG · PNG · WEBP · 최대 10MB</p>
                    </div>
                </label>
            </div>

            <div class="border-t border-pac-black-50 px-6 py-3 text-center">
                <button type="button" @click="phase = 'confirm'"
                        class="font-body text-sm text-pac-black-400 hover:text-pac-black-600 transition-colors">
                    이미지 없이 직접 입력하기 →
                </button>
            </div>
        </div>
    </div>

    {{-- [파싱 중] --}}
    <div x-show="phase === 'parsing'">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <template x-if="imagePreview">
                <div class="w-full rounded-xl overflow-hidden bg-pac-black-50 mb-6 flex items-center justify-center max-h-[40vh]">
                    <img :src="imagePreview" class="max-w-full max-h-full object-contain" style="max-height: 40vh">
                </div>
            </template>
            <div class="text-center py-4">
                <div class="inline-flex items-center gap-3 px-5 py-3 bg-pac-black-900 rounded-full">
                    <svg class="animate-spin h-5 w-5 text-pac-yellow-400" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="font-display text-sm font-bold text-white uppercase tracking-wide">
                        AI 분석 중...
                    </span>
                </div>
                <p class="font-body text-xs text-pac-black-400 mt-3">잠시만 기다려주세요 (10~20초 소요)</p>
            </div>
        </div>
    </div>

    {{-- [2단계] 확인 + 등록 --}}
    <div x-show="phase === 'confirm'" class="space-y-4">

        {{-- 파싱 완료 이미지 --}}
        <template x-if="parsed.image_url">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3 bg-pac-black-900">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-pac-green-500"></span>
                        <span class="font-display text-xs font-bold text-white uppercase tracking-widest">AI 파싱 완료</span>
                    </div>
                    <button type="button" @click="resetForm()"
                            class="font-display text-xs font-bold text-pac-yellow-400 hover:text-pac-yellow-300 uppercase tracking-widest transition-colors">
                        다시 업로드
                    </button>
                </div>
                <div class="w-full bg-pac-black-50 flex items-center justify-center overflow-hidden max-h-[40vh]">
                    <img :src="parsed.image_url" class="max-w-full max-h-full object-contain" style="max-height: 40vh">
                </div>
            </div>
        </template>

        {{-- 파싱 실패 / 직접 입력 안내 --}}
        <template x-if="errorMsg || (!parsed.image_url && !logId)">
            <div class="flex items-start gap-3 bg-pac-yellow-50 border border-pac-yellow-200 rounded-xl px-4 py-3">
                <svg class="w-4 h-4 text-pac-yellow-600 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                <p class="font-body text-sm text-pac-black-700" x-text="errorMsg || '직접 입력 모드입니다.'"></p>
            </div>
        </template>

        {{-- 등록 폼 --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-pac-black-900">
                <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">기록 확인 및 수정</h3>
            </div>

            <form method="POST"
                  :action="logId ? `/running-logs/${logId}/confirm` : '{{ route('running-logs.store') }}'">
                @csrf

                <input type="hidden" name="avg_pace_seconds"  :value="parsed.avg_pace_seconds">
                <input type="hidden" name="best_pace_seconds" :value="parsed.best_pace_seconds">

                <div class="p-6 space-y-4">

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">
                                날짜 <span class="text-pac-pink-500">*</span>
                            </label>
                            <input type="date" name="run_date" x-model="parsed.run_date"
                                   class="w-full text-sm border-pac-black-200 rounded-xl shadow-sm
                                          focus:border-pac-yellow-500 focus:ring-pac-yellow-400" required>
                        </div>
                        <div>
                            <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">
                                운동 유형
                            </label>
                            <div class="flex gap-4 mt-2.5">
                                <label class="flex items-center gap-1.5 cursor-pointer font-body text-sm text-pac-black-700">
                                    <input type="radio" name="is_indoor" value="0"
                                           x-model="parsed.is_indoor"
                                           class="text-pac-yellow-500 focus:ring-pac-yellow-400"> 야외
                                </label>
                                <label class="flex items-center gap-1.5 cursor-pointer font-body text-sm text-pac-black-700">
                                    <input type="radio" name="is_indoor" value="1"
                                           x-model="parsed.is_indoor"
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
                            <input type="number" name="distance_km" x-model="parsed.distance_km"
                                   step="0.01" min="0.1" max="999" placeholder="5.00"
                                   class="w-full text-sm border-pac-black-200 rounded-xl shadow-sm
                                          focus:border-pac-yellow-500 focus:ring-pac-yellow-400" required>
                        </div>
                        <div>
                            <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">
                                운동 시간 <span class="text-pac-pink-500">*</span>
                            </label>
                            <input type="text" name="duration" x-model="parsed.duration"
                                   placeholder="0:30:00"
                                   class="w-full text-sm border-pac-black-200 rounded-xl shadow-sm
                                          focus:border-pac-yellow-500 focus:ring-pac-yellow-400" required>
                        </div>
                    </div>

                    {{-- 평균 페이스 (읽기전용) --}}
                    <template x-if="parsed.avg_pace_seconds">
                        <div class="flex items-center gap-2 py-2.5 px-4 bg-pac-black-50 rounded-xl">
                            <svg class="w-4 h-4 text-pac-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="font-display text-xs font-bold text-pac-black-400 uppercase tracking-widest">평균 페이스</span>
                            <span class="font-display text-sm font-bold text-pac-black-900 ml-auto"
                                  x-text="formatPace(parsed.avg_pace_seconds)"></span>
                        </div>
                    </template>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">칼로리</label>
                            <div class="relative">
                                <input type="number" name="calories" x-model="parsed.calories"
                                       min="0" placeholder="—"
                                       class="w-full text-sm border-pac-black-200 rounded-xl shadow-sm pr-10
                                              focus:border-pac-yellow-500 focus:ring-pac-yellow-400">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 font-body text-xs text-pac-black-300">kcal</span>
                            </div>
                        </div>
                        <div>
                            <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">심박수</label>
                            <div class="relative">
                                <input type="number" name="avg_heart_rate" x-model="parsed.avg_heart_rate"
                                       min="0" max="300" placeholder="—"
                                       class="w-full text-sm border-pac-black-200 rounded-xl shadow-sm pr-10
                                              focus:border-pac-yellow-500 focus:ring-pac-yellow-400">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 font-body text-xs text-pac-black-300">bpm</span>
                            </div>
                        </div>
                        <div>
                            <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">고도</label>
                            <div class="relative">
                                <input type="number" name="elevation_m" x-model="parsed.elevation_m"
                                       step="0.1" placeholder="—"
                                       class="w-full text-sm border-pac-black-200 rounded-xl shadow-sm pr-6
                                              focus:border-pac-yellow-500 focus:ring-pac-yellow-400">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 font-body text-xs text-pac-black-300">m</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-1.5">메모</label>
                        <textarea name="memo" rows="2" maxlength="500" x-model="parsed.memo"
                                  class="w-full text-sm border-pac-black-200 rounded-xl shadow-sm resize-none
                                         focus:border-pac-yellow-500 focus:ring-pac-yellow-400 font-body"
                                  placeholder="오늘의 러닝 한 줄 메모"></textarea>
                    </div>
                </div>

                {{-- 하단 액션 --}}
                <div class="flex items-center justify-between px-6 py-4 bg-pac-black-50 border-t border-pac-black-100">
                    <a href="{{ route('running-logs.index') }}"
                       class="font-body text-sm text-pac-black-400 hover:text-pac-black-600 transition-colors">
                        취소
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-7 py-3
                                   bg-pac-yellow-500 hover:bg-pac-yellow-400
                                   text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                                   rounded-xl transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        최종 기록 등록
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
function runLogCreate() {
    return {
        phase: 'upload',
        logId: null,
        imagePreview: null,
        errorMsg: null,
        parsed: {
            image_url:         null,
            run_date:          '{{ date("Y-m-d") }}',
            distance_km:       '',
            duration:          '',
            avg_pace_seconds:  null,
            best_pace_seconds: null,
            is_indoor:         '0',
            calories:          '',
            avg_heart_rate:    '',
            elevation_m:       '',
            memo:              '',
        },

        async handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            this.imagePreview = URL.createObjectURL(file);
            this.phase = 'parsing';
            this.errorMsg = null;

            const formData = new FormData();
            formData.append('image', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            try {
                const response = await fetch('{{ route("running-logs.parse-image") }}', {
                    method: 'POST',
                    body: formData,
                });
                const data = await response.json();

                if (response.ok && data.success) {
                    this.logId                     = data.log_id;
                    this.parsed.image_url          = data.image_url;
                    this.parsed.run_date           = data.parsed.run_date          || '{{ date("Y-m-d") }}';
                    this.parsed.distance_km        = data.parsed.distance_km       ?? '';
                    this.parsed.duration           = data.parsed.duration          ?? '';
                    this.parsed.avg_pace_seconds   = data.parsed.avg_pace_seconds  ?? null;
                    this.parsed.best_pace_seconds  = data.parsed.best_pace_seconds ?? null;
                    this.parsed.is_indoor          = data.parsed.is_indoor ? '1' : '0';
                    this.parsed.calories           = data.parsed.calories           ?? '';
                    this.parsed.avg_heart_rate     = data.parsed.avg_heart_rate    ?? '';
                    this.parsed.elevation_m        = data.parsed.elevation_m       ?? '';
                } else {
                    this.errorMsg = data.message || 'AI 파싱 결과를 받지 못했습니다. 직접 입력해주세요.';
                }
            } catch (e) {
                this.errorMsg = 'CORE API 연결에 실패했습니다. 직접 입력해주세요.';
            }

            this.phase = 'confirm';
        },

        formatPace(seconds) {
            if (!seconds) return '';
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            return `${m}'${String(s).padStart(2, '0')}"`;
        },

        resetForm() {
            this.phase = 'upload';
            this.logId = null;
            this.imagePreview = null;
            this.errorMsg = null;
            this.parsed = {
                image_url: null, run_date: '{{ date("Y-m-d") }}',
                distance_km: '', duration: '',
                avg_pace_seconds: null, best_pace_seconds: null,
                is_indoor: '0', calories: '', avg_heart_rate: '',
                elevation_m: '', memo: '',
            };
            document.getElementById('imageInput').value = '';
        },
    };
}
</script>
</x-app-layout>
