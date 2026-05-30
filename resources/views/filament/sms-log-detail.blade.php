@php
    $statusLabel = match($log->status) {
        'delivered' => '수신 완료',
        'partial'   => '일부 수신',
        'failed'    => '수신 실패',
        default     => '발송됨',
    };
    $statusColor = match($log->status) {
        'delivered' => 'text-green-600 bg-green-50',
        'partial'   => 'text-orange-600 bg-orange-50',
        'failed'    => 'text-red-600 bg-red-50',
        default     => 'text-blue-600 bg-blue-50',
    };
    $filterLabel = match($log->filter_type) {
        'generation' => '기수별',
        'manual'     => '수동입력',
        default      => '전체',
    };
@endphp

<div class="space-y-4 text-sm">

    {{-- 기본 정보 --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-xs text-gray-500 mb-1">발송자</div>
            <div class="font-medium text-gray-800">{{ $log->sender?->nickname ?? '시스템' }}</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-xs text-gray-500 mb-1">발송일시</div>
            <div class="font-medium text-gray-800">{{ $log->created_at?->format('Y.m.d H:i:s') }}</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-xs text-gray-500 mb-1">발송 대상</div>
            <div class="font-medium text-gray-800">{{ $filterLabel }}</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-xs text-gray-500 mb-1">상태</div>
            <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $statusColor }}">{{ $statusLabel }}</span>
        </div>
    </div>

    {{-- 수치 요약 --}}
    <div class="grid grid-cols-3 gap-3 text-center">
        <div class="border border-gray-200 rounded-lg p-3">
            <div class="text-2xl font-bold text-gray-800">{{ $log->recipient_cnt }}</div>
            <div class="text-xs text-gray-500 mt-1">총 발송</div>
        </div>
        <div class="border border-green-200 rounded-lg p-3">
            <div class="text-2xl font-bold text-green-600">{{ $log->delivered_cnt }}</div>
            <div class="text-xs text-gray-500 mt-1">수신 완료</div>
        </div>
        <div class="border border-red-200 rounded-lg p-3">
            <div class="text-2xl font-bold text-red-500">{{ $log->failed_cnt }}</div>
            <div class="text-xs text-gray-500 mt-1">실패</div>
        </div>
    </div>

    {{-- 메시지 내용 --}}
    <div>
        <div class="text-xs text-gray-500 mb-1">메시지 내용</div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $log->message }}</div>
    </div>

    {{-- 수신자 목록 --}}
    @if($log->group_id)
        <div>
            <div class="text-xs text-gray-500 mb-2">수신자 목록</div>
            @if(empty($messages))
                <div class="text-center py-6 text-gray-400 bg-gray-50 rounded-lg border border-gray-200">
                    수신 정보를 불러올 수 없습니다
                </div>
            @else
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-3 py-2 text-gray-500 font-medium">#</th>
                                <th class="text-left px-3 py-2 text-gray-500 font-medium">수신번호</th>
                                <th class="text-left px-3 py-2 text-gray-500 font-medium">발신번호</th>
                                <th class="text-left px-3 py-2 text-gray-500 font-medium">상태</th>
                                <th class="text-left px-3 py-2 text-gray-500 font-medium">수신일시</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($messages as $i => $msg)
                                @php
                                    $to = $msg['to'] ?? '';
                                    $masked = strlen($to) === 11
                                        ? substr($to,0,3).'-****-'.substr($to,7)
                                        : (strlen($to) >= 7 ? substr($to,0,3).'****'.substr($to,-4) : $to);
                                    $isOk = ($msg['status_code'] ?? '') === '4000';
                                    $receivedAt = !empty($msg['date_received'])
                                        ? \Carbon\Carbon::parse($msg['date_received'])->format('m.d H:i:s')
                                        : '-';
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-gray-400">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2 font-mono text-gray-700">{{ $masked }}</td>
                                    <td class="px-3 py-2 font-mono text-gray-500">{{ $msg['from'] ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        @if($isOk)
                                            <span class="text-green-600 font-medium">수신완료</span>
                                        @else
                                            <span class="text-red-500">실패 ({{ $msg['status_code'] ?? '?' }})</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-gray-400 font-mono">{{ $receivedAt }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

</div>
