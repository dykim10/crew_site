@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge([
    'class' => 'w-full px-4 py-3 rounded-xl border border-pac-black-200
                font-body text-base text-pac-black-900
                placeholder:text-pac-black-300
                focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent
                disabled:bg-pac-black-50 disabled:text-pac-black-400 disabled:cursor-not-allowed
                transition duration-200 min-h-[44px]'
]) }}>
