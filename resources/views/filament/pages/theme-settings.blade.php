<x-filament-panels::page>
    <div class="adm-card">
        <h2 class="adm-card__title">메인 페이지 테마</h2>
        <p class="adm-card__desc">crew.pac-run.com 홈에 노출되는 디자인 테마를 선택합니다. 변경은 즉시 반영됩니다.</p>

        <div class="adm-theme-grid">
            <button
                wire:click="switchTheme('v1')"
                type="button"
                class="adm-theme-tile {{ $activeTheme === 'v1' ? 'is-active' : '' }}"
            >
                <div class="adm-theme-tile__preview" style="background:#0D0D0D;">
                    <div style="position:absolute;top:16px;left:20px;font-size:10px;letter-spacing:4px;color:#E5AD16;font-weight:700;">DARK EDITORIAL</div>
                    <div style="position:absolute;top:36px;left:20px;font-size:40px;font-weight:900;color:#fff;line-height:1;">PAC</div>
                    <div style="position:absolute;top:74px;left:20px;font-size:40px;font-weight:900;color:#E5AD16;line-height:1;">RUN</div>
                    <div style="position:absolute;bottom:16px;right:16px;background:#E5AD16;color:#1A1212;font-size:9px;font-weight:700;padding:4px 10px;letter-spacing:2px;">CREW →</div>
                </div>
                <div class="adm-theme-tile__body">
                    <div>
                        <div class="adm-theme-tile__name">V1 — Dark Editorial</div>
                        <div class="adm-theme-tile__meta">검정 배경 · Bebas Neue · pac-yellow 강조</div>
                    </div>
                    @if($activeTheme === 'v1')
                        <span class="adm-badge">적용중</span>
                    @endif
                </div>
            </button>

            <button
                wire:click="switchTheme('v2')"
                type="button"
                class="adm-theme-tile {{ $activeTheme === 'v2' ? 'is-active' : '' }}"
            >
                <div class="adm-theme-tile__preview" style="background:#E5AD16;">
                    <div style="position:absolute;top:0;right:0;bottom:0;width:50%;background:#1A1212;clip-path:polygon(20% 0,100% 0,100% 100%,0% 100%);"></div>
                    <div style="position:absolute;top:16px;left:20px;font-size:10px;letter-spacing:4px;color:#1A1212;font-weight:700;">ENERGY BURST</div>
                    <div style="position:absolute;top:32px;left:20px;font-size:44px;font-weight:900;color:#1A1212;line-height:1;">PAC</div>
                    <div style="position:absolute;top:74px;left:20px;font-size:44px;font-weight:900;color:#1A1212;line-height:1;">RUN</div>
                    <div style="position:absolute;bottom:16px;right:24px;background:#E5AD16;color:#1A1212;font-size:9px;font-weight:700;padding:4px 10px;letter-spacing:2px;">CREW →</div>
                </div>
                <div class="adm-theme-tile__body">
                    <div>
                        <div class="adm-theme-tile__name">V2 — Energy Burst</div>
                        <div class="adm-theme-tile__meta">pac-yellow 히어로 · Anton · 대각선 분할</div>
                    </div>
                    @if($activeTheme === 'v2')
                        <span class="adm-badge">적용중</span>
                    @endif
                </div>
            </button>
        </div>

        <div class="adm-hint">
            미리보기:
            <a href="/" target="_blank" rel="noopener">crew.pac-run.com</a>
            ·
            <a href="/design/v1" target="_blank" rel="noopener">V1 시안</a>
            ·
            <a href="/design/v2" target="_blank" rel="noopener">V2 시안</a>
        </div>
    </div>
</x-filament-panels::page>
