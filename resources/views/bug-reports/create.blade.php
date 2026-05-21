<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 헤더 --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('bug-reports.index') }}"
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

    <form method="POST" action="{{ route('bug-reports.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm overflow-hidden"
          x-data="bugReportForm()">
        @csrf

        <div class="px-5 py-3.5 bg-pac-black-900">
            <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">제보 내용</h3>
        </div>

        <div class="p-6 space-y-5">

            {{-- 제목 --}}
            <div>
                <label class="block font-body text-sm font-semibold text-pac-black-700 mb-1.5">
                    제목 <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}"
                       placeholder="버그 내용을 간략하게 요약해주세요"
                       class="w-full px-4 py-2.5 border rounded-xl font-body text-sm text-pac-black-900
                              focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-pac-yellow-400
                              @error('title') border-red-400 bg-red-50 @else border-pac-black-200 @enderror">
                @error('title')
                    <p class="mt-1 font-body text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- 우선순위 --}}
            <div>
                <label class="block font-body text-sm font-semibold text-pac-black-700 mb-1.5">
                    심각도 <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-4 gap-2">
                    @foreach([
                        ['value' => 'low',      'label' => '낮음',  'desc' => '사소한 불편', 'color' => 'border-pac-black-200 text-pac-black-500 peer-checked:border-pac-black-500 peer-checked:bg-pac-black-50'],
                        ['value' => 'medium',   'label' => '보통',  'desc' => '기능 오류',   'color' => 'border-pac-black-200 text-pac-black-500 peer-checked:border-blue-400 peer-checked:bg-blue-50 peer-checked:text-blue-700'],
                        ['value' => 'high',     'label' => '높음',  'desc' => '심각한 오류', 'color' => 'border-pac-black-200 text-pac-black-500 peer-checked:border-orange-400 peer-checked:bg-orange-50 peer-checked:text-orange-700'],
                        ['value' => 'critical', 'label' => '긴급',  'desc' => '서비스 불가', 'color' => 'border-pac-black-200 text-pac-black-500 peer-checked:border-red-400 peer-checked:bg-red-50 peer-checked:text-red-700'],
                    ] as $item)
                        <label class="cursor-pointer">
                            <input type="radio" name="priority" value="{{ $item['value'] }}" class="peer sr-only"
                                   {{ old('priority', 'medium') === $item['value'] ? 'checked' : '' }}>
                            <div class="border-2 rounded-xl p-3 text-center transition-all duration-150 {{ $item['color'] }}">
                                <p class="font-display text-sm font-bold uppercase tracking-wide">{{ $item['label'] }}</p>
                                <p class="font-body text-[10px] mt-0.5">{{ $item['desc'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('priority')
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
                                 @error('description') border-red-400 bg-red-50 @else border-pac-black-200 @enderror">{{ old('description') }}</textarea>
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

            {{-- 파일 첨부 --}}
            <div>
                <label class="block font-body text-sm font-semibold text-pac-black-700 mb-1.5">
                    스크린샷 첨부 <span class="font-normal text-pac-black-400">(선택)</span>
                </label>
                <div class="relative border-2 border-dashed rounded-xl transition-colors duration-150"
                     :class="fileName ? 'border-pac-yellow-400 bg-pac-yellow-50' : 'border-pac-black-200 hover:border-pac-yellow-300'"
                     @dragover.prevent
                     @drop.prevent="handleDrop($event)">
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                           @change="handleFile($event)">
                    <div class="px-5 py-8 text-center" x-show="!fileName">
                        <svg class="w-8 h-8 mx-auto text-pac-black-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="font-body text-sm text-pac-black-500">클릭하거나 파일을 드래그하세요</p>
                        <p class="font-body text-xs text-pac-black-400 mt-1">JPG, PNG, GIF, WEBP, PDF · 최대 5MB</p>
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
                @error('attachment')
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
                <a href="{{ route('bug-reports.index') }}"
                   class="px-5 py-2.5 font-display text-sm font-bold uppercase tracking-wide text-pac-black-500
                          border border-pac-black-200 rounded-xl hover:bg-pac-black-100 transition-colors duration-150">
                    취소
                </a>
                <button type="submit"
                        class="px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400
                               text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                               rounded-xl transition-colors duration-150">
                    제보 접수
                </button>
            </div>
        </div>

    </form>

</div>

<script>
function bugReportForm() {
    return {
        description: '{{ old('description', '') }}',
        fileName: '',
        fileInput: null,
        handleFile(e) {
            const file = e.target.files[0];
            if (file) this.fileName = file.name;
        },
        handleDrop(e) {
            const file = e.dataTransfer.files[0];
            if (file) {
                this.fileName = file.name;
                // DataTransfer로 input에 파일 주입
                const dt = new DataTransfer();
                dt.items.add(file);
                document.querySelector('input[name="attachment"]').files = dt.files;
            }
        },
        clearFile() {
            this.fileName = '';
            const input = document.querySelector('input[name="attachment"]');
            input.value = '';
        },
    }
}
</script>
</x-app-layout>
