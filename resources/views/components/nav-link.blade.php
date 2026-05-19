@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-1 pt-1 border-b-2 border-pac-yellow-500 text-sm font-semibold leading-5 text-pac-yellow-600 focus:outline-none focus:border-pac-yellow-600 transition duration-150 ease-in-out'
    : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-pac-black-500 hover:text-pac-black-700 hover:border-pac-black-300 focus:outline-none focus:text-pac-black-700 focus:border-pac-black-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
