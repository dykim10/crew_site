<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-24 md:px-6 text-center">

  <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-6">
    {{ $board ?? '게시판' }}
  </p>

  <h1 class="font-display text-[clamp(48px,8vw,96px)] leading-none tracking-wide text-pac-black-900 uppercase mb-4">
    COMING<br><span class="text-pac-yellow-500">SOON</span>
  </h1>

  <div class="w-20 h-0.5 bg-pac-yellow-500 mx-auto my-8"></div>

  <p class="font-body text-base text-pac-black-600 mb-10">
    {{ $board ?? '게시판' }}은 현재 준비 중입니다.<br>
    빠른 시일 내에 오픈할 예정입니다.
  </p>

  <a href="{{ route('home') }}" class="pac-btn-ghost inline-flex">← 홈으로 돌아가기</a>

</div>
</x-app-layout>
