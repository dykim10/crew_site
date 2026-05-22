<div class="space-y-4 text-sm p-1">

    <div class="grid grid-cols-2 gap-3">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">이름</p>
            <p class="font-medium text-gray-900">{{ $pii['name'] }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">이메일</p>
            <p class="font-medium text-gray-900 break-all">{{ $pii['email'] }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">연락처</p>
            <p class="font-medium text-gray-900">{{ $pii['phone'] }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">신청일</p>
            <p class="font-medium text-gray-900">{{ $record->created_at->format('Y.m.d H:i') }}</p>
        </div>
    </div>

    @if(count($fieldLines) > 0)
        <hr class="border-gray-200">
        <div class="space-y-3">
            @foreach($fieldLines as $line)
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">{{ $line['label'] }}</p>
                    <p class="text-gray-800 whitespace-pre-wrap leading-relaxed">{{ $line['value'] }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <hr class="border-gray-200">
    <div class="flex items-center gap-2">
        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">상태:</span>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
            {{ match($record->status) {
                'approved'   => 'bg-green-100 text-green-800',
                'rejected'   => 'bg-red-100 text-red-800',
                'waitlisted' => 'bg-yellow-100 text-yellow-800',
                default      => 'bg-gray-100 text-gray-800',
            } }}">
            {{ \App\Models\Application::STATUS_LABELS[$record->status] ?? $record->status }}
        </span>
    </div>

    @if($record->admin_memo)
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">관리자 메모</p>
            <p class="text-gray-700 whitespace-pre-wrap">{{ $record->admin_memo }}</p>
        </div>
    @endif
</div>
