<div class="adm-detail">

    <div class="adm-detail-grid">
        <div>
            <p class="adm-detail-label">이름</p>
            <p class="adm-detail-value">{{ $pii['name'] }}</p>
        </div>
        <div>
            <p class="adm-detail-label">이메일</p>
            <p class="adm-detail-value">{{ $pii['email'] }}</p>
        </div>
        <div>
            <p class="adm-detail-label">연락처</p>
            <p class="adm-detail-value">{{ $pii['phone'] }}</p>
        </div>
        <div>
            <p class="adm-detail-label">신청일</p>
            <p class="adm-detail-value">{{ $record->created_at->format('Y.m.d H:i') }}</p>
        </div>
    </div>

    @if(count($fieldLines) > 0)
        <hr class="adm-detail-divider">
        <div class="adm-detail" style="gap:0.75rem;">
            @foreach($fieldLines as $line)
                <div>
                    <p class="adm-detail-label">{{ $line['label'] }}</p>
                    <p class="adm-detail-body">{{ $line['value'] }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <hr class="adm-detail-divider">
    <div style="display:flex;align-items:center;gap:0.5rem;">
        <span class="adm-detail-label" style="margin:0;">상태</span>
        <span class="adm-status {{ match($record->status) {
            'approved'   => 'adm-status--approved',
            'rejected'   => 'adm-status--rejected',
            'waitlisted' => 'adm-status--waitlisted',
            default      => 'adm-status--default',
        } }}">
            {{ \App\Models\Application::STATUS_LABELS[$record->status] ?? $record->status }}
        </span>
    </div>

    @if($record->admin_memo)
        <div>
            <p class="adm-detail-label">관리자 메모</p>
            <p class="adm-detail-body">{{ $record->admin_memo }}</p>
        </div>
    @endif
</div>
