<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">러닝 기록 등록</h2>
            <a href="{{ route('running-logs.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← 목록으로</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8" x-data="runLogCreate()">

            {{-- 단계 표시 --}}
            <div class="flex items-center gap-2 mb-6 px-1">
                <div class="flex items-center gap-2">
                    <div :class="phase === 'upload' || phase === 'parsing'
                            ? 'bg-blue-600 text-white'
                            : 'bg-green-500 text-white'"
                        class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition-colors">
                        <template x-if="phase === 'confirm'">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="phase !== 'confirm'">
                            <span>1</span>
                        </template>
                    </div>
                    <span class="text-sm font-medium"
                        :class="phase !== 'confirm' ? 'text-blue-600' : 'text-green-600'">
                        이미지 업로드
                    </span>
                </div>
                <div class="flex-1 h-px bg-gray-200 mx-1"></div>
                <div class="flex items-center gap-2">
                    <div :class="phase === 'confirm' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-400'"
                        class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition-colors">
                        2
                    </div>
                    <span class="text-sm font-medium"
                        :class="phase === 'confirm' ? 'text-blue-600' : 'text-gray-400'">
                        기록 확인 및 등록
                    </span>
                </div>
            </div>

            {{-- [1단계] 업로드 영역 --}}
            <div x-show="phase === 'upload'">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        <input type="file" id="imageInput" accept="image/*" class="hidden"
                            @change="handleFileSelect($event)">

                        <label for="imageInput" class="block cursor-pointer group">
                            <div class="border-2 border-dashed border-gray-200 group-hover:border-blue-400
                                        rounded-xl p-10 text-center transition-colors group-hover:bg-blue-50">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl
                                            bg-blue-50 group-hover:bg-blue-100 mb-4 transition-colors">
                                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                    </svg>
                                </div>
                                <p class="text-gray-700 font-semibold text-base mb-1">러닝 앱 스크린샷 업로드</p>
                                <p class="text-gray-400 text-sm mb-5">AI가 기록·날짜를 자동으로 파싱합니다</p>
                                <span class="inline-flex items-center gap-2 px-7 py-3 bg-blue-600 text-white
                                             text-base rounded-xl font-semibold shadow-md shadow-blue-200
                                             group-hover:bg-blue-700 group-hover:shadow-blue-300 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    이미지 선택하기
                                </span>
                                <p class="text-xs text-gray-300 mt-4">JPG · PNG · WEBP · 최대 10MB</p>
                            </div>
                        </label>
                    </div>

                    <div class="border-t border-gray-50 px-6 py-3 text-center">
                        <button type="button" @click="phase = 'confirm'"
                            class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                            이미지 없이 직접 입력하기 →
                        </button>
                    </div>
                </div>
            </div>

            {{-- [파싱 중] --}}
            <div x-show="phase === 'parsing'">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <template x-if="imagePreview">
                        <div class="w-full rounded-xl overflow-hidden bg-gray-50 mb-6 flex items-center justify-center" style="max-height: 40vh">
                            <img :src="imagePreview" class="max-w-full max-h-full object-contain" style="max-height: 40vh">
                        </div>
                    </template>
                    <div class="text-center py-4">
                        <div class="inline-flex items-center gap-3 px-5 py-3 bg-blue-50 rounded-full">
                            <svg class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <span class="text-blue-700 font-medium text-sm">AI가 기록을 분석하고 있습니다...</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">잠시만 기다려주세요 (10~20초 소요)</p>
                    </div>
                </div>
            </div>

            {{-- [2단계] 파싱 결과 확인 + 최종 등록 --}}
            <div x-show="phase === 'confirm'" class="space-y-4">

                {{-- 이미지 파싱 성공 --}}
                <template x-if="parsed.image_url">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-green-400"></div>
                                <span class="text-sm font-medium text-gray-700">AI 파싱 완료</span>
                            </div>
                            <button type="button" @click="resetForm()"
                                class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                                다시 업로드
                            </button>
                        </div>
                        <div class="w-full bg-gray-50 flex items-center justify-center overflow-hidden" style="max-height: 40vh">
                            <img :src="parsed.image_url" class="max-w-full max-h-full object-contain" style="max-height: 40vh">
                        </div>
                    </div>
                </template>

                {{-- 파싱 실패 or 직접 입력 --}}
                <template x-if="errorMsg || (!parsed.image_url && !logId)">
                    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-amber-800" x-text="errorMsg || '직접 입력 모드입니다.'"></p>
                    </div>
                </template>

                {{-- 최종 등록 폼 --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="px-6 pt-5 pb-2">
                        <h3 class="text-base font-semibold text-gray-800">기록 확인 및 수정</h3>
                        <p class="text-xs text-gray-400 mt-0.5">파싱된 값을 확인하고 필요하면 수정하세요.</p>
                    </div>

                    <form method="POST"
                        :action="logId
                            ? `/running-logs/${logId}/confirm`
                            : '{{ route('running-logs.store') }}'">
                        @csrf

                        <input type="hidden" name="avg_pace_seconds"  :value="parsed.avg_pace_seconds">
                        <input type="hidden" name="best_pace_seconds" :value="parsed.best_pace_seconds">

                        <div class="px-6 pb-6 space-y-4 mt-3">

                            {{-- 날짜 / 운동유형 --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">
                                        날짜 <span class="text-red-400 normal-case">*</span>
                                    </label>
                                    <input type="date" name="run_date" x-model="parsed.run_date"
                                        class="w-full text-sm border-gray-200 rounded-lg shadow-sm
                                               focus:border-blue-500 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">운동 유형</label>
                                    <div class="flex gap-3 mt-2">
                                        <label class="flex items-center gap-1.5 cursor-pointer text-sm text-gray-700">
                                            <input type="radio" name="is_indoor" value="0"
                                                x-model="parsed.is_indoor" class="text-blue-600"> 야외
                                        </label>
                                        <label class="flex items-center gap-1.5 cursor-pointer text-sm text-gray-700">
                                            <input type="radio" name="is_indoor" value="1"
                                                x-model="parsed.is_indoor" class="text-blue-600"> 실내
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- 거리 / 시간 --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">
                                        거리 (km) <span class="text-red-400 normal-case">*</span>
                                    </label>
                                    <input type="number" name="distance_km" x-model="parsed.distance_km"
                                        step="0.01" min="0.1" max="999" placeholder="5.00"
                                        class="w-full text-sm border-gray-200 rounded-lg shadow-sm
                                               focus:border-blue-500 focus:ring-blue-500" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">
                                        운동 시간 <span class="text-red-400 normal-case">*</span>
                                    </label>
                                    <input type="text" name="duration" x-model="parsed.duration"
                                        placeholder="0:30:00"
                                        class="w-full text-sm border-gray-200 rounded-lg shadow-sm
                                               focus:border-blue-500 focus:ring-blue-500" required>
                                </div>
                            </div>

                            {{-- 평균 페이스 (읽기전용) --}}
                            <template x-if="parsed.avg_pace_seconds">
                                <div class="flex items-center gap-2 py-2 px-3 bg-gray-50 rounded-lg">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    <span class="text-xs text-gray-500">평균 페이스</span>
                                    <span class="text-sm font-semibold text-gray-700 ml-auto"
                                        x-text="formatPace(parsed.avg_pace_seconds)"></span>
                                </div>
                            </template>

                            {{-- 칼로리 / 심박수 / 고도 --}}
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">칼로리</label>
                                    <div class="relative">
                                        <input type="number" name="calories" x-model="parsed.calories"
                                            min="0" placeholder="—"
                                            class="w-full text-sm border-gray-200 rounded-lg shadow-sm
                                                   focus:border-blue-500 focus:ring-blue-500 pr-8">
                                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-300">kcal</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">심박수</label>
                                    <div class="relative">
                                        <input type="number" name="avg_heart_rate" x-model="parsed.avg_heart_rate"
                                            min="0" max="300" placeholder="—"
                                            class="w-full text-sm border-gray-200 rounded-lg shadow-sm
                                                   focus:border-blue-500 focus:ring-blue-500 pr-8">
                                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-300">bpm</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">고도</label>
                                    <div class="relative">
                                        <input type="number" name="elevation_m" x-model="parsed.elevation_m"
                                            step="0.1" placeholder="—"
                                            class="w-full text-sm border-gray-200 rounded-lg shadow-sm
                                                   focus:border-blue-500 focus:ring-blue-500 pr-5">
                                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs text-gray-300">m</span>
                                    </div>
                                </div>
                            </div>

                            {{-- 메모 --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">메모</label>
                                <textarea name="memo" rows="2" maxlength="500" x-model="parsed.memo"
                                    class="w-full text-sm border-gray-200 rounded-lg shadow-sm
                                           focus:border-blue-500 focus:ring-blue-500 resize-none"
                                    placeholder="오늘의 러닝 한 줄 메모"></textarea>
                            </div>

                        </div>

                        {{-- 하단 액션 --}}
                        <div class="flex items-center justify-between px-6 py-4 bg-gray-50 rounded-b-2xl border-t border-gray-100">
                            <a href="{{ route('running-logs.index') }}"
                                class="text-sm text-gray-400 hover:text-gray-600 transition-colors">취소</a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-7 py-3 bg-blue-600 text-white
                                       text-base font-bold rounded-xl hover:bg-blue-700 transition-all
                                       shadow-md shadow-blue-200 hover:shadow-blue-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7"/>
                                </svg>
                                최종 기록 등록
                            </button>
                        </div>
                    </form>
                </div>
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
