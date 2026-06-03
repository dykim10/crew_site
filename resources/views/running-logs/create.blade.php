<x-app-layout>
<div class="max-w-4xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4"
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
            <div :class="phase === 'review' ? 'bg-pac-green-500 text-white' : 'bg-pac-yellow-500 text-pac-black-900'"
                 class="w-6 h-6 rounded-full flex items-center justify-center transition-colors">
                <template x-if="phase === 'review'">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <template x-if="phase !== 'review'">
                    <span class="font-display text-xs font-bold">1</span>
                </template>
            </div>
            <span class="font-display text-xs font-bold uppercase tracking-widest"
                  :class="phase !== 'review' ? 'text-pac-yellow-600' : 'text-pac-green-500'">
                이미지 선택
            </span>
        </div>

        <div class="flex-1 h-px bg-pac-black-100 mx-2"></div>

        <div class="flex items-center gap-2">
            <div :class="phase === 'parsing' ? 'bg-pac-yellow-500 text-pac-black-900' : (phase === 'review' ? 'bg-pac-green-500 text-white' : 'bg-pac-black-100 text-pac-black-400')"
                 class="w-6 h-6 rounded-full flex items-center justify-center transition-colors">
                <template x-if="phase === 'review'">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </template>
                <template x-if="phase !== 'review'">
                    <span class="font-display text-xs font-bold">2</span>
                </template>
            </div>
            <span class="font-display text-xs font-bold uppercase tracking-widest"
                  :class="phase === 'parsing' ? 'text-pac-yellow-600' : (phase === 'review' ? 'text-pac-green-500' : 'text-pac-black-400')">
                AI 파싱
            </span>
        </div>

        <div class="flex-1 h-px bg-pac-black-100 mx-2"></div>

        <div class="flex items-center gap-2">
            <div :class="phase === 'review' ? 'bg-pac-yellow-500 text-pac-black-900' : 'bg-pac-black-100 text-pac-black-400'"
                 class="w-6 h-6 rounded-full flex items-center justify-center transition-colors">
                <span class="font-display text-xs font-bold">3</span>
            </div>
            <span class="font-display text-xs font-bold uppercase tracking-widest"
                  :class="phase === 'review' ? 'text-pac-yellow-600' : 'text-pac-black-400'">
                확인 및 등록
            </span>
        </div>
    </div>

    {{-- [1단계] 업로드 --}}
    <div x-show="phase === 'upload'" x-transition>
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6">
                <input type="file" id="imageInput" accept="image/*" multiple class="hidden"
                       @change="handleFileSelect($event)">

                <label for="imageInput" class="block cursor-pointer group">
                    <div class="border-2 border-dashed border-pac-black-100
                                group-hover:border-pac-yellow-400 group-hover:bg-pac-black-50
                                rounded-2xl p-12 text-center transition-all duration-200">
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
                            여러 장을 한번에 선택하면 AI가 기록을 순서대로 자동 파싱합니다
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
                        <p class="font-body text-xs text-pac-black-300 mt-4">JPG · PNG · WEBP · 최대 10MB · 다중 선택 가능</p>
                    </div>
                </label>
            </div>
        </div>
    </div>

    {{-- [2단계] 파싱 중 --}}
    <div x-show="phase === 'parsing'" x-transition>
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-pac-black-900">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <svg class="animate-spin h-4 w-4 text-pac-yellow-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span class="font-display text-sm font-bold text-white uppercase tracking-wide">AI 파싱 진행 중</span>
                    </div>
                    <span class="font-display text-xs font-bold text-pac-yellow-400"
                          x-text="`${currentIdx + 1} / ${files.length}`"></span>
                </div>
                <div class="w-full bg-pac-black-700 rounded-full h-1.5">
                    <div class="bg-pac-yellow-400 h-1.5 rounded-full transition-all duration-500"
                         :style="`width: ${files.length ? Math.round(((currentIdx) / files.length) * 100) : 0}%`"></div>
                </div>
            </div>

            <div class="p-5">
                <div class="flex flex-wrap gap-3">
                    <template x-for="(f, idx) in files" :key="idx">
                        <div class="relative w-20 h-20 rounded-xl overflow-hidden bg-pac-black-50 flex-shrink-0">
                            <img :src="f.preview" class="w-full h-full object-cover" alt="">
                            <div class="absolute inset-0 flex items-center justify-center"
                                 :class="{
                                     'bg-pac-green-500/75': f.status === 'done',
                                     'bg-red-500/75':       f.status === 'error',
                                     'bg-pac-black-900/60': f.status === 'parsing',
                                     'bg-pac-black-900/25': f.status === 'pending',
                                 }">
                                <template x-if="f.status === 'done'">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </template>
                                <template x-if="f.status === 'parsing'">
                                    <svg class="animate-spin w-6 h-6 text-pac-yellow-400" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                </template>
                                <template x-if="f.status === 'error'">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </template>
                                <template x-if="f.status === 'pending'">
                                    <span class="font-display text-xs font-bold text-white" x-text="idx + 1"></span>
                                </template>
                            </div>
                            <div class="absolute top-1 left-1 bg-pac-black-900/70 rounded px-1 font-display text-[9px] font-bold text-white"
                                 x-text="idx + 1"></div>
                        </div>
                    </template>
                </div>
                <p class="font-body text-xs text-pac-black-400 mt-4 text-center">
                    잠시만 기다려주세요. 이미지 한 장당 10~20초 소요됩니다.
                </p>
            </div>
        </div>
    </div>

    {{-- [3단계] 검토 및 등록 --}}
    <div x-show="phase === 'review'" x-transition>
        <div class="flex items-center gap-3 mb-4">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-pac-green-500/10 border border-pac-green-500/30 rounded-lg">
                <svg class="w-3.5 h-3.5 text-pac-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="font-display text-xs font-bold text-pac-green-500 uppercase tracking-widest"
                      x-text="`파싱 완료 ${doneCount}건`"></span>
            </span>
            <template x-if="errorCount > 0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 border border-red-200 rounded-lg">
                    <svg class="w-3.5 h-3.5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-display text-xs font-bold text-red-500 uppercase tracking-widest"
                          x-text="`파싱 실패 ${errorCount}건`"></span>
                </span>
            </template>
        </div>

        {{-- 카드 그리드 --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-24">
            <template x-for="(f, idx) in files" :key="idx">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden"
                     :class="f.status === 'error' ? 'opacity-60' : ''">

                    <div class="flex items-center justify-between px-4 py-2.5"
                         :class="f.status === 'done' ? 'bg-pac-black-900' : 'bg-red-700'">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full"
                                  :class="f.status === 'done' ? 'bg-pac-green-500' : 'bg-red-300'"></span>
                            <span class="font-display text-xs font-bold text-white uppercase tracking-widest"
                                  x-text="f.status === 'done' ? `기록 ${idx + 1}` : `파싱 실패 ${idx + 1}`"></span>
                        </div>
                        <button type="button" @click="removeFile(idx)"
                                class="font-display text-xs font-bold text-pac-yellow-400 hover:text-pac-yellow-300 uppercase tracking-widest transition-colors">
                            제거
                        </button>
                    </div>

                    <div class="w-full bg-pac-black-50 flex items-center justify-center overflow-hidden" style="height:180px">
                        <img :src="f.preview" class="max-w-full max-h-full object-contain" alt="" style="max-height:180px">
                    </div>

                    <template x-if="f.status === 'error'">
                        <div class="p-4">
                            <div class="flex items-start gap-2 bg-red-50 border border-red-100 rounded-xl p-3">
                                <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                                <p class="font-body text-sm text-red-700" x-text="f.error"></p>
                            </div>
                        </div>
                    </template>

                    <template x-if="f.status === 'done'">
                        <div class="p-4 space-y-3">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-widest mb-1">
                                        날짜 <span class="text-red-400">*</span>
                                    </label>
                                    <input type="date" x-model="f.parsed.run_date"
                                           class="w-full text-sm border-pac-black-200 rounded-lg shadow-sm
                                                  focus:border-pac-yellow-500 focus:ring-pac-yellow-400" required>
                                </div>
                                <div>
                                    <label class="block font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-widest mb-1">
                                        운동 유형
                                    </label>
                                    <div class="flex gap-3 mt-2">
                                        <label class="flex items-center gap-1.5 cursor-pointer font-body text-sm text-pac-black-700">
                                            <input type="radio" :name="`is_indoor_${idx}`" value="0"
                                                   x-model="f.parsed.is_indoor"
                                                   class="text-pac-yellow-500 focus:ring-pac-yellow-400"> 야외
                                        </label>
                                        <label class="flex items-center gap-1.5 cursor-pointer font-body text-sm text-pac-black-700">
                                            <input type="radio" :name="`is_indoor_${idx}`" value="1"
                                                   x-model="f.parsed.is_indoor"
                                                   class="text-pac-yellow-500 focus:ring-pac-yellow-400"> 실내
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-widest mb-1">
                                        거리 (km) <span class="text-red-400">*</span>
                                    </label>
                                    <input type="number" x-model="f.parsed.distance_km"
                                           step="0.01" min="0.1" max="999" placeholder="5.00"
                                           class="w-full text-sm border-pac-black-200 rounded-lg shadow-sm
                                                  focus:border-pac-yellow-500 focus:ring-pac-yellow-400" required>
                                </div>
                                <div>
                                    <label class="block font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-widest mb-1">
                                        운동 시간 <span class="text-red-400">*</span>
                                    </label>
                                    <input type="text" x-model="f.parsed.duration"
                                           placeholder="0:30:00"
                                           class="w-full text-sm border-pac-black-200 rounded-lg shadow-sm
                                                  focus:border-pac-yellow-500 focus:ring-pac-yellow-400" required>
                                </div>
                            </div>

                            <template x-if="f.parsed.avg_pace_seconds">
                                <div class="flex items-center gap-2 py-2 px-3 bg-pac-black-50 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-pac-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    <span class="font-display text-[9px] font-bold text-pac-black-400 uppercase tracking-widest">평균 페이스</span>
                                    <span class="font-display text-sm font-bold text-pac-black-900 ml-auto"
                                          x-text="formatPace(f.parsed.avg_pace_seconds)"></span>
                                </div>
                            </template>

                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-widest mb-1">칼로리</label>
                                    <div class="relative">
                                        <input type="number" x-model="f.parsed.calories"
                                               min="0" placeholder="—"
                                               class="w-full text-sm border-pac-black-200 rounded-lg shadow-sm pr-9
                                                      focus:border-pac-yellow-500 focus:ring-pac-yellow-400">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 font-body text-[10px] text-pac-black-300">kcal</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-widest mb-1">심박수</label>
                                    <div class="relative">
                                        <input type="number" x-model="f.parsed.avg_heart_rate"
                                               min="0" max="300" placeholder="—"
                                               class="w-full text-sm border-pac-black-200 rounded-lg shadow-sm pr-9
                                                      focus:border-pac-yellow-500 focus:ring-pac-yellow-400">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 font-body text-[10px] text-pac-black-300">bpm</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-widest mb-1">고도</label>
                                    <div class="relative">
                                        <input type="number" x-model="f.parsed.elevation_m"
                                               step="0.1" placeholder="—"
                                               class="w-full text-sm border-pac-black-200 rounded-lg shadow-sm pr-5
                                                      focus:border-pac-yellow-500 focus:ring-pac-yellow-400">
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 font-body text-[10px] text-pac-black-300">m</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-widest mb-1">메모</label>
                                <textarea x-model="f.parsed.memo" rows="2" maxlength="500"
                                          class="w-full text-sm border-pac-black-200 rounded-lg shadow-sm resize-none
                                                 focus:border-pac-yellow-500 focus:ring-pac-yellow-400 font-body"
                                          placeholder="오늘의 러닝 한 줄 메모"></textarea>
                            </div>

                            {{-- 디버그 패널 --}}
                            <template x-if="f.raw">
                                <div class="border border-pac-black-100 rounded-xl overflow-hidden">
                                    <button type="button" @click="f.debugOpen = !f.debugOpen"
                                            class="w-full flex items-center justify-between px-3 py-2
                                                   bg-pac-black-50 hover:bg-pac-black-100 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-3.5 h-3.5 text-pac-black-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                                            </svg>
                                            <span class="font-display text-[9px] font-bold text-pac-black-400 uppercase tracking-widest">
                                                CORE API 파싱 원본
                                            </span>
                                            {{-- null 필드 경고 --}}
                                            <template x-if="Object.values(f.raw).filter(v => v === null).length > 0">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-amber-100 text-amber-700 rounded text-[9px] font-bold">
                                                    <span x-text="Object.values(f.raw).filter(v => v === null).length"></span> null
                                                </span>
                                            </template>
                                        </div>
                                        <svg class="w-3.5 h-3.5 text-pac-black-400 transition-transform duration-150"
                                             :class="f.debugOpen ? 'rotate-180' : ''"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    <div x-show="f.debugOpen" x-cloak>
                                        {{-- 필드별 파싱 결과 테이블 --}}
                                        <div class="px-3 pt-3 pb-1">
                                            <table class="w-full text-xs font-mono border-collapse">
                                                <template x-for="[k, v] in Object.entries(f.raw)" :key="k">
                                                    <tr class="border-b border-pac-black-50">
                                                        <td class="py-1 pr-3 text-pac-black-400 w-40 align-top" x-text="k"></td>
                                                        <td class="py-1 align-top"
                                                            :class="v === null ? 'text-red-400' : 'text-emerald-600'"
                                                            x-text="v === null ? 'null' : (typeof v === 'object' ? JSON.stringify(v) : String(v))">
                                                        </td>
                                                    </tr>
                                                </template>
                                            </table>
                                        </div>
                                        {{-- 전체 JSON --}}
                                        <div class="px-3 pt-2 pb-3">
                                            <p class="font-display text-[8px] font-bold text-pac-black-300 uppercase tracking-widest mb-1">RAW JSON</p>
                                            <pre class="text-[10px] font-mono bg-pac-black-900 text-pac-green-400 p-2 rounded-lg overflow-x-auto whitespace-pre-wrap break-all"
                                                 x-text="JSON.stringify(f.raw, null, 2)"></pre>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    {{-- Sticky 하단 바 (review 단계) --}}
    <div x-show="phase === 'review'" x-cloak
         class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-pac-black-100 shadow-xl">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('running-logs.index') }}"
                   class="font-body text-sm text-pac-black-400 hover:text-pac-black-600 transition-colors">
                    취소
                </a>
                <template x-if="errorCount > 0">
                    <span class="font-body text-sm text-pac-black-400"
                          x-text="`(실패 ${errorCount}건 제외)`"></span>
                </template>
            </div>
            <button type="button" @click="submitAll()"
                    :disabled="doneCount === 0"
                    class="inline-flex items-center gap-2 px-7 py-3
                           bg-pac-yellow-500 hover:bg-pac-yellow-400 disabled:opacity-40 disabled:cursor-not-allowed
                           text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                           rounded-xl transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                <span x-text="`${doneCount}건 모두 등록`"></span>
            </button>
        </div>
    </div>

</div>

<script>
function runLogCreate() {
    return {
        phase: 'upload',
        files: [],
        currentIdx: 0,

        get doneCount()  { return this.files.filter(f => f.status === 'done').length; },
        get errorCount() { return this.files.filter(f => f.status === 'error').length; },

        handleFileSelect(event) {
            const selected = Array.from(event.target.files);
            if (!selected.length) return;
            this.files = selected.map(file => ({
                file,
                preview: URL.createObjectURL(file),
                status: 'pending',
                logId: null,
                parsed: {
                    image_url:         null,
                    run_date:          new Date().toISOString().slice(0, 10),
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
                raw:       null,
                debugOpen: false,
                error: null,
            }));
            this.phase = 'parsing';
            this.currentIdx = 0;
            this.processNext();
        },

        async processNext() {
            const idx = this.files.findIndex(f => f.status === 'pending');
            if (idx === -1) { this.phase = 'review'; return; }
            this.currentIdx = idx;
            this.files[idx].status = 'parsing';

            const fd = new FormData();
            fd.append('image', this.files[idx].file);
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            try {
                const res = await fetch('{{ route("running-logs.parse-image") }}', { method: 'POST', body: fd });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.files[idx].status = 'done';
                    this.files[idx].logId  = data.log_id;
                    this.files[idx].raw    = data.raw ?? null;
                    Object.assign(this.files[idx].parsed, {
                        image_url:         data.image_url,
                        run_date:          data.parsed.run_date          || new Date().toISOString().slice(0, 10),
                        distance_km:       data.parsed.distance_km       ?? '',
                        duration:          data.parsed.duration          ?? '',
                        avg_pace_seconds:  data.parsed.avg_pace_seconds  ?? null,
                        best_pace_seconds: data.parsed.best_pace_seconds ?? null,
                        is_indoor:         data.parsed.is_indoor ? '1' : '0',
                        calories:          data.parsed.calories           ?? '',
                        avg_heart_rate:    data.parsed.avg_heart_rate    ?? '',
                        elevation_m:       data.parsed.elevation_m       ?? '',
                    });
                } else {
                    this.files[idx].status = 'error';
                    this.files[idx].error  = data.message || '파싱 실패';
                }
            } catch (e) {
                this.files[idx].status = 'error';
                this.files[idx].error  = 'CORE API 연결 실패';
            }
            this.processNext();
        },

        async submitAll() {
            const items = this.files
                .filter(f => f.status === 'done')
                .map(f => ({ log_id: f.logId, ...f.parsed }));

            const res = await fetch('{{ route("running-logs.batch-confirm") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ items }),
            });
            const data = await res.json();
            if (data.success) window.location.href = data.redirect;
        },

        formatPace(sec) {
            if (!sec) return '';
            return `${Math.floor(sec / 60)}'${String(sec % 60).padStart(2, '0')}"`;
        },

        removeFile(idx) {
            this.files.splice(idx, 1);
            if (!this.files.length) this.phase = 'upload';
        },
    };
}
</script>
</x-app-layout>
