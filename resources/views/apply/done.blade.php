<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-20 md:px-6 lg:px-8 text-center">

  <div class="inline-flex items-center justify-center w-20 h-20 border-2 border-pac-yellow-500/40 mb-8">
    <svg class="w-10 h-10 text-pac-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
  </div>

  <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-4">Submitted</p>
  <h1 class="font-display text-[clamp(36px,6vw,56px)] leading-none tracking-wide text-pac-black-900 uppercase mb-6">
    신청 <span class="text-pac-yellow-500">완료</span>
  </h1>
  <div class="w-20 h-0.5 bg-pac-yellow-500 mx-auto mb-8"></div>

  <p class="font-body text-base text-pac-black-600 leading-relaxed mb-10 max-w-md mx-auto">
    기수 신청서가 정상적으로 접수되었습니다.<br>
    검토 후 입력하신 이메일로 결과를 안내드릴게요.
  </p>

  <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
    <a href="{{ route('home') }}" class="pac-btn">메인으로 →</a>
    <a href="{{ route('login') }}" class="pac-btn-ghost">로그인</a>
  </div>

</div>
</x-app-layout>
