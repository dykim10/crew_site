<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'inline-flex items-center justify-center px-6 py-3
                bg-pac-yellow-500 hover:bg-pac-yellow-600 active:bg-pac-yellow-700
                text-pac-black-900 font-body font-bold text-sm
                rounded-xl min-h-[44px]
                focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:ring-offset-2
                transition-colors duration-200'
]) }}>
    {{ $slot }}
</button>
