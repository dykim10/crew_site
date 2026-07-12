<div class="adm-stack text-sm">
    <p><strong>메시지</strong></p>
    <pre class="whitespace-pre-wrap break-words rounded-lg bg-gray-50 p-3 text-gray-800">{{ $record->message }}</pre>

    @if(filled($record->context))
        <p class="mt-4"><strong>context</strong></p>
        <pre class="max-h-96 overflow-auto whitespace-pre-wrap break-words rounded-lg bg-gray-50 p-3 text-xs text-gray-700">{{ json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    @endif
</div>
