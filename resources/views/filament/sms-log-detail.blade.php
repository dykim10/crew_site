<div class="space-y-5 text-sm">

    {{-- 발송 요약 --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-xs text-gray-400 mb-1">발송자</div>
            <div class="font-semibold text-gray-800">{{ $log->sender?->nickname ?? '-' }}</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-xs text-gray-400 mb-1">발송 유형</div>
            <div class="font-semibold text-gray-800">
                {{ match($log->filter_type) { 'generation' => '기수별', 'manual' => '수동입력', default => '전체' } }}
            </div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-xs text-gray-400 mb-1">발송일시</div>
            <div class="font-semibold text-gray-800">{{ $log->created_at?->format('Y.m.d H:i:s') }}</div>
        </div>
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="text-xs text-gray-400 mb-1">상태</div>
            <div class="font-semibold">
                @php
                    $statusLabel = match($log->status) {
                        'delivered' => ['수신 완료', 'text-green-600'],
                        'partial'   => ['일부 수신', 'text-yellow-600'],
                        'failed'    => ['수신 실패', 'text-red-600'],
                        default     => ['발송됨', 'text-gray-500'],
                    };
                @endphp
                <span class="{{ $statusLabel[1] }}">{{ $statusLabel[0] }}</span>
            </div>
        </div>
    </div>

    {{-- 수신 집계 --}}
    <div class="grid grid-cols-3 gap-3 text-center">
        <div class="bg-blue-50 rounded-lg p-3">
            <div class="text-2xl font-black text-blue-700">{{ $log->recipient_cnt }}</div>
            <div class="text-xs text-blue-400 mt-1">총 발송</div>
        </div>
        <div class="bg-green-50 rounded-lg p-3">
            <div class="text-2xl font-black text-green-700">{{ $log->delivered_cnt }}</div>
            <div class="text-xs text-green-400 mt-1">수신 완료</div>
        </div>
        <div class="bg-red-50 rounded-lg p-3">
            <div class="text-2xl font-black text-red-700">{{ $log->failed_cnt }}</div>
            <div class="text-xs text-red-400 mt-1">실패</div>
        </div>
    </div>

    {{-- 메시지 내용 --}}
    <div>
        <div class="text-xs text-gray-400 mb-1">메시지 내용</div>
        <div class="bg-gray-50 rounded-lg p-3 text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $log->message }}</div>
    </div>

    {{-- 개별 수신 현황 --}}
    @if($log->group_id)
        <div>
            <div class="text-xs text-gray-400 mb-2">개별 수신 현황 (Solapi)</div>
            @if(empty($messages))
                <div class="text-center text-gray-400 py-4 bg-gray-50 rounded-lg">
                    메시지 상세 정보를 불러올 수 없습니다.
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full text-xs">
                        <thead class="bg-gray-100 text-gray-500">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">수신번호</th>
                                <th class="px-3 py-2 text-left font-medium">발신번호</th>
                                <th class="px-3 py-2 text-center font-medium">상태</th>
                                <th class="px-3 py-2 text-left font-medium">수신일시</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($messages as $msg)
                                @php
                                    $to = $msg['to'] ?? '';
                                    $masked = strlen($to) === 11
                                        ? substr($to,0,3).'-****-'.substr($to,7)
                                        : substr($to,0,3).'****'.substr($to,-4);
                                    $isSuccess = ($msg['status_code'] ?? '') === '4000';
                                    $receivedAt = $msg['date_received']
                                        ? \Carbon\Carbon::parse($msg['date_received'])->format('Y.m.d H:i:s')
                                        : '-';
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 font-mono text-gray-700">{{ $masked }}</td>
                                    <td class="px-3 py-2 font-mono text-gray-500">{{ $msg['from'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @if($isSuccess)
                                            <span class="inline-block bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">수신 완료</span>
                                        @else
                                            <span class="inline-block bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full">
                                                실패 ({{ $msg['status_code'] ?? '?' }})
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-gray-500">{{ $receivedAt }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @else
        <div class="text-xs text-gray-400 text-center py-2">
            Solapi 그룹 ID가 없어 개별 수신 현황을 조회할 수 없습니다.
        </div>
    @endif

</div>
