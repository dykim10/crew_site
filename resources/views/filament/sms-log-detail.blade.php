<style>
@import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap');

.sms-detail-wrap {
    --pac-yellow: #E5AD16;
    --pac-black: #1A1212;
    --pac-pink: #E80043;
    --pac-dark: #111;
    font-family: 'Barlow Condensed', system-ui, sans-serif;
}

/* Header stripe */
.sms-header {
    background: var(--pac-black);
    position: relative;
    overflow: hidden;
    padding: 1.25rem 1.5rem 1.25rem 1.75rem;
    border-radius: 0.75rem 0.75rem 0 0;
}
.sms-header::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0;
    width: 6px;
    background: var(--pac-yellow);
}
.sms-header::after {
    content: '';
    position: absolute;
    top: -30px; right: 60px;
    width: 120px; height: 160px;
    background: var(--pac-yellow);
    opacity: 0.06;
    transform: skewX(-18deg);
}

/* LED stat cards */
.sms-stat-card {
    background: #1c1c1e;
    border: 1px solid #2a2a2a;
    border-radius: 0.5rem;
    padding: 0.875rem 1rem;
    position: relative;
    overflow: hidden;
    transition: border-color 0.2s;
}
.sms-stat-card::before {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
}
.sms-stat-card.blue::before  { background: #3b82f6; }
.sms-stat-card.green::before { background: #22c55e; }
.sms-stat-card.red::before   { background: var(--pac-pink); }

.sms-stat-num {
    font-family: 'JetBrains Mono', monospace;
    font-size: 2.25rem;
    font-weight: 700;
    line-height: 1;
    letter-spacing: -0.04em;
}
.sms-stat-card.blue  .sms-stat-num { color: #60a5fa; }
.sms-stat-card.green .sms-stat-num { color: #4ade80; }
.sms-stat-card.red   .sms-stat-num { color: #f87171; }

/* Message terminal box */
.sms-message-box {
    background: #0d0d0d;
    border: 1px solid #2a2a2a;
    border-left: 3px solid var(--pac-yellow);
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.8rem;
    color: #e2e2e2;
    white-space: pre-wrap;
    line-height: 1.7;
    position: relative;
}
.sms-message-box .msg-label {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.15em;
    color: var(--pac-yellow);
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

/* Table */
.sms-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.78rem;
}
.sms-table thead tr {
    background: #1a1a1a;
    border-bottom: 1px solid #2a2a2a;
}
.sms-table thead th {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #555;
    padding: 0.6rem 0.875rem;
    text-align: left;
}
.sms-table tbody tr {
    border-bottom: 1px solid #1e1e1e;
    transition: background 0.15s;
}
.sms-table tbody tr:hover { background: #161616; }
.sms-table tbody tr:last-child { border-bottom: none; }
.sms-table td {
    padding: 0.65rem 0.875rem;
    color: #ccc;
}
.sms-table .mono {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.75rem;
    letter-spacing: -0.01em;
}

/* Status dot */
.status-dot {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    font-family: 'Barlow Condensed', sans-serif;
}
.status-dot::before {
    content: '';
    display: block;
    width: 6px; height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
}
.status-dot.ok { color: #4ade80; }
.status-dot.ok::before { background: #4ade80; box-shadow: 0 0 6px #4ade80; }
.status-dot.fail { color: #f87171; }
.status-dot.fail::before { background: #f87171; box-shadow: 0 0 6px #f87171; }

/* Status badge (header) */
.sms-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 0.2rem 0.6rem;
    border-radius: 0.25rem;
}
</style>

@php
    $statusConfig = match($log->status) {
        'delivered' => ['label' => 'DELIVERED',  'bg' => 'background:#14532d;color:#4ade80;'],
        'partial'   => ['label' => 'PARTIAL',    'bg' => 'background:#422006;color:#fb923c;'],
        'failed'    => ['label' => 'FAILED',     'bg' => 'background:#450a0a;color:#f87171;'],
        default     => ['label' => 'SENT',       'bg' => 'background:#1e1b4b;color:#a5b4fc;'],
    };
    $filterLabel = match($log->filter_type) {
        'generation' => '기수별',
        'manual'     => '수동입력',
        default      => '전체',
    };
@endphp

<div class="sms-detail-wrap" style="background:#0a0a0a; border-radius:0.75rem; overflow:hidden; border:1px solid #222;">

    {{-- ── HEADER ── --}}
    <div class="sms-header">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; position:relative; z-index:1;">
            <div>
                <div style="font-size:0.6rem; font-weight:800; letter-spacing:0.2em; color:#E5AD16; text-transform:uppercase; margin-bottom:0.35rem;">
                    PAC-RUN CREW · SMS DISPATCH
                </div>
                <div style="font-size:1.4rem; font-weight:900; color:#fff; letter-spacing:-0.01em; line-height:1.1;">
                    {{ $log->sender?->nickname ?? '시스템' }}
                    <span style="font-weight:400; color:#666; font-size:1rem;">발송</span>
                </div>
                <div style="margin-top:0.4rem; font-size:0.75rem; color:#555; display:flex; gap:1rem; align-items:center;">
                    <span style="font-family:'JetBrains Mono',monospace; font-size:0.7rem;">
                        {{ $log->created_at?->format('Y.m.d  H:i:s') }}
                    </span>
                    <span style="color:#333;">·</span>
                    <span>{{ $filterLabel }}</span>
                    @if($log->group_id)
                        <span style="color:#333;">·</span>
                        <span style="font-family:'JetBrains Mono',monospace; font-size:0.65rem; color:#444;">
                            {{ Str::limit($log->group_id, 20) }}
                        </span>
                    @endif
                </div>
            </div>
            <div>
                <span class="sms-status-badge" style="{{ $statusConfig['bg'] }}">
                    {{ $statusConfig['label'] }}
                </span>
            </div>
        </div>
    </div>

    <div style="padding:1.25rem 1.5rem; display:flex; flex-direction:column; gap:1.25rem;">

        {{-- ── STAT CARDS ── --}}
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:0.75rem;">
            <div class="sms-stat-card blue">
                <div style="font-size:0.6rem; font-weight:700; letter-spacing:0.15em; color:#3b82f6; text-transform:uppercase; margin-bottom:0.5rem;">TOTAL</div>
                <div class="sms-stat-num">{{ $log->recipient_cnt }}</div>
                <div style="font-size:0.7rem; color:#444; margin-top:0.25rem;">총 발송</div>
            </div>
            <div class="sms-stat-card green">
                <div style="font-size:0.6rem; font-weight:700; letter-spacing:0.15em; color:#22c55e; text-transform:uppercase; margin-bottom:0.5rem;">DELIVERED</div>
                <div class="sms-stat-num">{{ $log->delivered_cnt }}</div>
                <div style="font-size:0.7rem; color:#444; margin-top:0.25rem;">수신 완료</div>
            </div>
            <div class="sms-stat-card red">
                <div style="font-size:0.6rem; font-weight:700; letter-spacing:0.15em; color:#E80043; text-transform:uppercase; margin-bottom:0.5rem;">FAILED</div>
                <div class="sms-stat-num">{{ $log->failed_cnt }}</div>
                <div style="font-size:0.7rem; color:#444; margin-top:0.25rem;">실패</div>
            </div>
        </div>

        {{-- ── MESSAGE ── --}}
        <div class="sms-message-box">
            <div class="msg-label">▶ Message</div>
            {{ $log->message }}
        </div>

        {{-- ── RECIPIENT TABLE ── --}}
        @if($log->group_id)
            <div>
                <div style="font-size:0.6rem; font-weight:800; letter-spacing:0.15em; color:#444; text-transform:uppercase; margin-bottom:0.6rem;">
                    Individual Recipients
                </div>
                @if(empty($messages))
                    <div style="text-align:center; padding:1.5rem; background:#111; border-radius:0.5rem; border:1px solid #1e1e1e; color:#444; font-size:0.78rem;">
                        수신 정보를 불러올 수 없습니다
                    </div>
                @else
                    <div style="background:#111; border:1px solid #1e1e1e; border-radius:0.5rem; overflow:hidden;">
                        <table class="sms-table">
                            <thead>
                                <tr>
                                    <th>#</th>
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
                                        $isOk  = ($msg['status_code'] ?? '') === '4000';
                                        $receivedAt = !empty($msg['date_received'])
                                            ? \Carbon\Carbon::parse($msg['date_received'])->format('H:i:s')
                                            : '-';
                                    @endphp
                                    <tr>
                                        <td style="color:#333; font-size:0.65rem; width:32px;">{{ $i + 1 }}</td>
                                        <td class="mono" style="color:#e2e2e2;">{{ $masked }}</td>
                                        <td class="mono" style="color:#555;">{{ $msg['from'] ?? '-' }}</td>
                                        <td>
                                            <span class="status-dot {{ $isOk ? 'ok' : 'fail' }}">
                                                {{ $isOk ? '수신완료' : '실패 (' . ($msg['status_code'] ?? '?') . ')' }}
                                            </span>
                                        </td>
                                        <td class="mono" style="color:#444; font-size:0.68rem;">{{ $receivedAt }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @else
            <div style="text-align:center; padding:1rem; background:#111; border-radius:0.5rem; border:1px dashed #222; color:#333; font-size:0.75rem;">
                Solapi 그룹 ID 없음 — 개별 수신 현황 조회 불가
            </div>
        @endif

    </div>{{-- /body --}}

</div>{{-- /wrap --}}
