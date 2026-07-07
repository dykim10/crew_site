<style>
.sd-label {
    font-size: 0.65rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}
.sd-stat {
    flex: 1;
    padding: 0.875rem 1rem;
    border-right: 1px solid var(--adm-border);
}
.sd-stat:last-child { border-right: none; }
.sd-stat-num {
    font-family: ui-monospace, 'Cascadia Code', monospace;
    font-size: 2rem;
    font-weight: 500;
    line-height: 1;
    letter-spacing: -0.04em;
}
.sd-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
}
.sd-cell {
    padding: 0.6rem 0.875rem;
    border-right: 1px solid var(--adm-border);
    border-bottom: 1px solid var(--adm-border);
}
.sd-cell:nth-child(even) { border-right: none; }
.sd-cell:nth-last-child(-n+2) { border-bottom: none; }
.sd-cell-val {
    font-size: 0.82rem;
    margin-top: 0.2rem;
}
.sd-msg {
    padding: 0.875rem 1rem;
    font-size: 0.82rem;
    line-height: 1.7;
    white-space: pre-wrap;
}
.sd-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.78rem;
}
.sd-table th {
    font-size: 0.62rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 0.5rem 0.875rem;
    text-align: left;
    border-bottom: 1px solid var(--adm-border);
}
.sd-table td {
    padding: 0.55rem 0.875rem;
    border-bottom: 1px solid var(--adm-border-2);
    vertical-align: middle;
}
.sd-table tr:last-child td { border-bottom: none; }
.sd-status-badge {
    display: inline-block;
    font-size: 0.62rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 0.15rem 0.5rem;
}
</style>

@php
    $statusLabel = match($log->status) {
        'delivered' => '수신 완료',
        'partial'   => '일부 수신',
        'failed'    => '수신 실패',
        default     => '발송됨',
    };
    $statusClass = match($log->status) {
        'delivered' => 'sd-status--delivered',
        'partial'   => 'sd-status--partial',
        'failed'    => 'sd-status--failed',
        default     => 'sd-status--default',
    };
    $filterLabel = match($log->filter_type) {
        'generation' => '기수별',
        'manual'     => '수동입력',
        default      => '전체',
    };
@endphp

<div class="sd">

    <div style="display:flex; border-bottom:1px solid var(--adm-border);">
        <div class="sd-stat">
            <div class="sd-label">Total</div>
            <div class="sd-stat-num">{{ $log->recipient_cnt }}</div>
            <div class="sd-muted" style="font-size:0.7rem;margin-top:0.2rem;">총 발송</div>
        </div>
        <div class="sd-stat">
            <div class="sd-label">Delivered</div>
            <div class="sd-stat-num ok">{{ $log->delivered_cnt }}</div>
            <div class="sd-muted" style="font-size:0.7rem;margin-top:0.2rem;">수신 완료</div>
        </div>
        <div class="sd-stat">
            <div class="sd-label">Failed</div>
            <div class="sd-stat-num fail">{{ $log->failed_cnt }}</div>
            <div class="sd-muted" style="font-size:0.7rem;margin-top:0.2rem;">실패</div>
        </div>
    </div>

    <div class="sd-row" style="border-bottom:1px solid var(--adm-border);">
        <div class="sd-cell">
            <div class="sd-label">발송자</div>
            <div class="sd-cell-val">{{ $log->sender?->nickname ?? '시스템' }}</div>
        </div>
        <div class="sd-cell">
            <div class="sd-label">발송일시</div>
            <div class="sd-cell-val sd-mono" style="font-size:0.78rem;">{{ $log->created_at?->format('Y.m.d H:i:s') }}</div>
        </div>
        <div class="sd-cell">
            <div class="sd-label">발송 대상</div>
            <div class="sd-cell-val">{{ $filterLabel }}</div>
        </div>
        <div class="sd-cell" style="display:flex;flex-direction:column;justify-content:center;">
            <div class="sd-label" style="margin-bottom:0.25rem;">상태</div>
            <span class="sd-status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>
    </div>

    <div class="sd-msg-block">
        <div class="sd-section-head" style="border-bottom:none;padding-bottom:0;">Message</div>
        <div class="sd-msg">{{ $log->message }}</div>
    </div>

    @if($log->group_id)
        @if(empty($messages))
            <div class="sd-empty">수신 정보를 불러올 수 없습니다</div>
        @else
            <div>
                <div class="sd-section-head">Recipients — {{ count($messages) }}명</div>
                <table class="sd-table">
                    <thead>
                        <tr>
                            <th style="width:36px;">#</th>
                            <th>수신번호</th>
                            <th>발신번호</th>
                            <th>상태</th>
                            <th>수신일시</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $i => $msg)
                            @php
                                $to = $msg['to'] ?? '';
                                $masked = strlen($to) === 11
                                    ? substr($to,0,3).'-****-'.substr($to,7)
                                    : (strlen($to) >= 7 ? substr($to,0,3).'****'.substr($to,-4) : $to);
                                $isOk = ($msg['status_code'] ?? '') === '4000';
                                $receivedAt = !empty($msg['date_received'])
                                    ? \Carbon\Carbon::parse($msg['date_received'])->format('m.d H:i:s')
                                    : '—';
                            @endphp
                            <tr>
                                <td class="sd-muted" style="font-size:0.7rem;">{{ $i + 1 }}</td>
                                <td class="sd-mono">{{ $masked }}</td>
                                <td class="sd-mono sd-muted">{{ $msg['from'] ?? '—' }}</td>
                                <td>
                                    @if($isOk)
                                        <span class="sd-ok" style="font-size:0.78rem;">수신 완료</span>
                                    @else
                                        <span class="sd-fail" style="font-size:0.78rem;">실패 ({{ $msg['status_code'] ?? '?' }})</span>
                                    @endif
                                </td>
                                <td class="sd-mono sd-muted" style="font-size:0.72rem;">{{ $receivedAt }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @else
        <div class="sd-empty" style="padding:1rem 0.875rem;text-align:left;">그룹 ID 없음 — 개별 수신 현황 조회 불가</div>
    @endif

</div>
