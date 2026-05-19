@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-body text-xs font-medium text-pac-black-500 uppercase tracking-wide mb-1.5']) }}>
    {{ $value ?? $slot }}
</label>
