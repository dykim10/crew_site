<x-filament-panels::page>
    @if($this->activeRecipientCount() === 0)
        <div class="adm-alert adm-alert--warning">
            <strong>테스트 수신자 없음</strong>
            <span>테스트 문자 없이 본 발송이 진행됩니다. 운영 전 관리자 1명 이상 등록을 권장합니다.</span>
        </div>
    @endif

    <div class="adm-stack">
        {{ $this->form }}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
