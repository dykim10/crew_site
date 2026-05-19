@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-body font-medium text-sm text-pac-green-600 bg-pac-green-500/10 px-4 py-3 rounded-xl']) }}>
        {{ $status }}
    </div>
@endif
