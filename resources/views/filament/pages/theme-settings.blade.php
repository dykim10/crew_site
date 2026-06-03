<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900 mb-1">현재 적용 테마</h2>
            <p class="text-sm text-gray-500 mb-6">메인 페이지(crew.pac-run.com)에 노출되는 디자인 테마를 선택합니다.</p>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                {{-- V1: Dark Editorial --}}
                <button
                    wire:click="switchTheme('v1')"
                    type="button"
                    class="group relative flex flex-col overflow-hidden rounded-lg border-2 text-left transition-all
                        {{ $activeTheme === 'v1' ? 'border-yellow-400 ring-2 ring-yellow-400 ring-offset-2' : 'border-gray-200 hover:border-gray-300' }}"
                >
                    {{-- 미리보기 썸네일 --}}
                    <div class="h-40 w-full overflow-hidden" style="background:#0D0D0D; position:relative;">
                        <div style="position:absolute;top:16px;left:20px;font-family:sans-serif;font-size:10px;letter-spacing:4px;color:#E5AD16;font-weight:700;">DARK EDITORIAL</div>
                        <div style="position:absolute;top:36px;left:20px;font-family:sans-serif;font-size:40px;font-weight:900;color:#fff;line-height:1;">PAC</div>
                        <div style="position:absolute;top:74px;left:20px;font-family:sans-serif;font-size:40px;font-weight:900;color:#E5AD16;line-height:1;">RUN</div>
                        <div style="position:absolute;bottom:16px;right:16px;background:#E5AD16;color:#1A1212;font-size:9px;font-weight:700;padding:4px 10px;letter-spacing:2px;">CREW →</div>
                        <div style="position:absolute;bottom:0;left:0;right:0;height:1px;background:rgba(229,173,22,.3);"></div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-white">
                        <div>
                            <div class="font-semibold text-gray-900 text-sm">V1 — Dark Editorial</div>
                            <div class="text-xs text-gray-500 mt-0.5">검정 배경 · Bebas Neue · pac-yellow 강조</div>
                        </div>
                        @if($activeTheme === 'v1')
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                ✓ 적용중
                            </span>
                        @endif
                    </div>
                </button>

                {{-- V2: Energy Burst --}}
                <button
                    wire:click="switchTheme('v2')"
                    type="button"
                    class="group relative flex flex-col overflow-hidden rounded-lg border-2 text-left transition-all
                        {{ $activeTheme === 'v2' ? 'border-yellow-400 ring-2 ring-yellow-400 ring-offset-2' : 'border-gray-200 hover:border-gray-300' }}"
                >
                    <div class="h-40 w-full overflow-hidden" style="position:relative; background:#E5AD16;">
                        <div style="position:absolute;top:0;right:0;bottom:0;width:50%;background:#1A1212;clip-path:polygon(20% 0,100% 0,100% 100%,0% 100%);"></div>
                        <div style="position:absolute;top:16px;left:20px;font-family:sans-serif;font-size:10px;letter-spacing:4px;color:#1A1212;font-weight:700;">ENERGY BURST</div>
                        <div style="position:absolute;top:32px;left:20px;font-family:sans-serif;font-size:44px;font-weight:900;color:#1A1212;line-height:1;">PAC</div>
                        <div style="position:absolute;top:74px;left:20px;font-family:sans-serif;font-size:44px;font-weight:900;color:#1A1212;line-height:1;">RUN</div>
                        <div style="position:absolute;bottom:16px;right:24px;background:#E5AD16;color:#1A1212;font-size:9px;font-weight:700;padding:4px 10px;letter-spacing:2px;">CREW →</div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-white">
                        <div>
                            <div class="font-semibold text-gray-900 text-sm">V2 — Energy Burst</div>
                            <div class="text-xs text-gray-500 mt-0.5">pac-yellow 히어로 · Anton · 대각선 분할</div>
                        </div>
                        @if($activeTheme === 'v2')
                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800">
                                ✓ 적용중
                            </span>
                        @endif
                    </div>
                </button>
            </div>

            <div class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-500">
                💡 테마 변경은 즉시 메인 페이지에 반영됩니다. 미리보기:
                <a href="/" target="_blank" class="text-yellow-600 underline ml-1">crew.pac-run.com</a>
                &nbsp;|&nbsp;
                <a href="/design/v1" target="_blank" class="text-yellow-600 underline">V1 시안</a>
                &nbsp;|&nbsp;
                <a href="/design/v2" target="_blank" class="text-yellow-600 underline">V2 시안</a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
