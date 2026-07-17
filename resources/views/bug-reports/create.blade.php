<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 헤더 --}}
    <div class="flex items-center gap-3">
        <a href="{{ url()->previous('/') }}"
           class="p-2 text-pac-black-400 hover:text-pac-black-700 transition-colors duration-150">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-0.5">SUPPORT</p>
            <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">버그 제보</h1>
        </div>
    </div>

    @if (session('success'))
        <div class="flex items-start gap-3 rounded border-l-4 border-pac-yellow-500 bg-pac-yellow-50 px-4 py-3">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-pac-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="font-body text-sm text-pac-yellow-800">{{ session('success') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('bug-reports.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm overflow-hidden relative"
          x-data="bugReportForm()" @submit="loading = true">
        @csrf

        {{-- 로딩 바 --}}
        <div x-show="loading" x-cloak class="absolute top-0 left-0 right-0 h-0.5 overflow-hidden z-10">
            <div class="absolute inset-y-0 pac-loading-bar bg-pac-yellow-500"></div>
        </div>

        <div class="px-5 py-3.5 bg-pac-black-900">
            <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">제보 내용</h3>
        </div>

        <div class="p-6 space-y-5">

            {{-- 제목 --}}
            <div>
                <label class="block font-body text-sm font-semibold text-pac-black-700 mb-1.5">
                    제목 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $prefillTitle ?? '') }}"
                       placeholder="버그 내용을 간략하게 요약해주세요"
                       class="w-full px-4 py-2.5 border rounded-xl font-body text-sm text-pac-black-900
                              focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-pac-yellow-400
                              @error('title') border-red-400 bg-red-50 @else border-pac-black-200 @enderror">
                @error('title')
                    <p class="mt-1 font-body text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- 발생 경로 --}}
            <div>
                <label class="block font-body text-sm font-semibold text-pac-black-700 mb-1.5">
                    발생 경로 <span class="font-normal text-pac-black-400">(선택 — 버그가 발생한 페이지 URL)</span>
                </label>
                <input type="text" name="path" value="{{ old('path', $previousPath) }}"
                       placeholder="/dashboard, /events/3 등"
                       class="w-full px-4 py-2.5 border border-pac-black-200 rounded-xl font-body text-sm text-pac-black-900
                              focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-pac-yellow-400">
            </div>

            {{-- 심각도 — Alpine.js 기반 선택 (peer-checked CSS 없이 동작 보장) --}}
            <div>
                <label class="block font-body text-sm font-semibold text-pac-black-700 mb-1.5">
                    심각도 <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    @foreach([
                        ['value' => 'low',    'label' => '낮음',  'desc' => '사소한 불편',
                         'active' => 'border-pac-black-600 bg-pac-black-900 text-white',
                         'inactive' => 'border-pac-black-200 bg-white text-pac-black-600 hover:border-pac-black-400'],
                        ['value' => 'medium', 'label' => '보통',  'desc' => '기능 오류',
                         'active' => 'border-blue-500 bg-blue-500 text-white',
                         'inactive' => 'border-pac-black-200 bg-white text-pac-black-600 hover:border-blue-400'],
                        ['value' => 'high',   'label' => '높음',  'desc' => '심각한 오류',
                         'active' => 'border-red-500 bg-red-500 text-white',
                         'inactive' => 'border-pac-black-200 bg-white text-pac-black-600 hover:border-red-400'],
                    ] as $item)
                    <label class="cursor-pointer" @click="severity = '{{ $item['value'] }}'">
                        <input type="radio" name="severity" value="{{ $item['value'] }}" class="sr-only"
                               x-bind:checked="severity === '{{ $item['value'] }}'">
                        <div class="border-2 rounded-xl p-3 text-center transition-all duration-150 select-none"
                             x-bind:class="severity === '{{ $item['value'] }}' ? '{{ $item['active'] }}' : '{{ $item['inactive'] }}'">
                            <p class="font-display text-sm font-bold uppercase tracking-wide">{{ $item['label'] }}</p>
                            <p class="font-body text-[10px] mt-0.5 opacity-80">{{ $item['desc'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('severity')
                    <p class="mt-1 font-body text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- 내용 --}}
            <div>
                <label class="block font-body text-sm font-semibold text-pac-black-700 mb-1.5">
                    상세 내용 <span class="text-red-500">*</span>
                </label>
                <textarea name="description" rows="6"
                          placeholder="버그가 발생한 상황, 재현 방법, 기대했던 동작 등을 구체적으로 설명해주세요."
                          x-model="description"
                          class="w-full px-4 py-2.5 border rounded-xl font-body text-sm text-pac-black-900 resize-none
                                 focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-pac-yellow-400
                                 @error('description') border-red-400 bg-red-50 @else border-pac-black-200 @enderror"></textarea>
                <div class="flex items-center justify-between mt-1">
                    @error('description')
                        <p class="font-body text-xs text-red-500">{{ $message }}</p>
                    @else
                        <span></span>
                    @enderror
                    <p class="font-body text-xs text-pac-black-400">
                        <span x-text="description.length">0</span> / 5000
                    </p>
                </div>
            </div>

            {{-- 스크린샷 첨부 --}}
            <div>
                <label class="block font-body text-sm font-semibold text-pac-black-700 mb-1.5">
                    스크린샷 첨부 <span class="font-normal text-pac-black-400">(선택)</span>
                </label>
                <div class="relative border-2 border-dashed rounded-xl transition-colors duration-150"
                     :class="fileName ? 'border-pac-yellow-400 bg-pac-yellow-50' : 'border-pac-black-200 hover:border-pac-yellow-300'"
                     @dragover.prevent
                     @drop.prevent="handleDrop($event)">
                    <input type="file" name="screenshot" accept=".jpg,.jpeg,.png,.webp"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                           @change="handleFile($event)">
                    <div class="px-5 py-8 text-center" x-show="!fileName">
                        <svg class="w-8 h-8 mx-auto text-pac-black-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="font-body text-sm text-pac-black-500">클릭하거나 파일을 드래그하세요</p>
                        <p class="font-body text-xs text-pac-black-400 mt-1">JPG, PNG, WEBP · 최대 10MB</p>
                    </div>
                    <div class="px-5 py-4 flex items-center gap-3" x-show="fileName" style="display:none">
                        <svg class="w-6 h-6 text-pac-yellow-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="font-body text-sm text-pac-black-700 flex-1 truncate" x-text="fileName"></span>
                        <button type="button" @click.stop="clearFile()"
                                class="font-body text-xs text-pac-black-400 hover:text-red-500 transition-colors duration-150">
                            제거
                        </button>
                    </div>
                </div>
                @error('screenshot')
                    <p class="mt-1 font-body text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- 푸터 --}}
        <div class="px-6 py-4 bg-pac-black-50 border-t border-pac-black-100 flex items-center justify-between">
            <p class="font-body text-xs text-pac-black-400">
                접수 후 관리자가 확인하여 처리 상태를 업데이트합니다.
            </p>
            <div class="flex gap-3">
                <a href="{{ url()->previous('/') }}"
                   class="px-5 py-2.5 font-display text-sm font-bold uppercase tracking-wide text-pac-black-500
                          border border-pac-black-200 rounded-xl hover:bg-pac-black-100 transition-colors duration-150">
                    취소
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400
                               text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                               rounded-xl transition-colors duration-150"
                        :disabled="loading" :class="loading ? 'opacity-70 cursor-not-allowed' : ''">
                    <span x-show="!loading">제보 접수</span>
                    <span x-show="loading" x-cloak class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        접수 중...
                    </span>
                </button>
            </div>
        </div>

    </form>

</div>

<script>
function bugReportForm() {
    return {
        loading: false,
        severity: '{{ old('severity', 'medium') }}',
        description: @js(old('description', $prefillDescription ?? '')),
        fileName: '',
        handleFile(e) {
            const file = e.target.files[0];
            if (file) this.fileName = file.name;
        },
        handleDrop(e) {
            const file = e.dataTransfer.files[0];
            if (file) {
                this.fileName = file.name;
                const dt = new DataTransfer();
                dt.items.add(file);
                document.querySelector('input[name="screenshot"]').files = dt.files;
            }
        },
        clearFile() {
            this.fileName = '';
            const input = document.querySelector('input[name="screenshot"]');
            if (input) input.value = '';
        },
    }
}
</script>
</x-app-layout>
