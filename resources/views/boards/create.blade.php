<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-8 md:px-6 lg:px-8">

  <div class="flex items-center gap-2 mb-6">
    <a href="{{ route('boards.index', $type) }}"
       class="font-display text-[10px] tracking-[3px] uppercase text-pac-yellow-500 hover:text-pac-yellow-400 transition-colors">
      {{ $meta['label'] }}
    </a>
    <span class="text-pac-black-700">›</span>
    <span class="font-display text-[10px] tracking-[3px] uppercase text-pac-black-600">글쓰기</span>
  </div>

  <div class="bg-pac-black-900 border border-pac-black-100 p-6">
    <h1 class="font-display text-2xl uppercase tracking-wider text-pac-black-900 mb-6">
      글쓰기 <span class="text-pac-yellow-500 text-lg">— {{ $meta['label'] }}</span>
    </h1>
    @include('boards._form')
  </div>

</div>
</x-app-layout>
