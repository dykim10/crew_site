<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-12 md:px-6 lg:px-8">
  <div class="mb-10">
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-4">Application</p>
    <h1 class="font-display text-[clamp(32px,5vw,48px)] leading-none tracking-wide text-pac-black-900 uppercase">
      {{ $generation->display_name }}
      <span class="text-pac-yellow-500">신청</span>
    </h1>
    <div class="w-20 h-0.5 bg-pac-yellow-500 mt-6"></div>
  </div>

  <div class="bg-white rounded-2xl border border-pac-black-100 p-6 md:p-8 space-y-5">
    <p class="font-body text-sm text-pac-black-800 leading-relaxed">
      이 기수는 <strong>구글 폼</strong>으로 신청을 받고 있습니다.
      아래 버튼을 누르면 외부(Google) 페이지로 이동합니다.
    </p>
    <p class="font-body text-xs text-pac-black-500 leading-relaxed">
      개인정보 수집·이용의 주체는 PAC RUN CREW이며, 구글 폼에 입력한 내용은 모집·연락 목적으로만 사용됩니다.
      제출 후 구글 계정에 로그인된 상태일 수 있으니 공용 PC에서는 로그아웃을 권장합니다.
    </p>

    @if($googleForm?->form_url)
      <a href="{{ $googleForm->form_url }}"
         target="_blank" rel="noopener noreferrer"
         class="pac-btn-primary inline-flex items-center justify-center w-full sm:w-auto">
        구글 폼으로 이동
      </a>
      <p class="font-body text-xs text-pac-black-400">{{ $googleForm->title }}</p>
    @else
      <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        연결된 구글 폼 URL이 아직 등록되지 않았습니다. 관리자에게 문의해주세요.
      </div>
    @endif

    <a href="{{ route('generation.show') }}" class="inline-block font-body text-sm text-pac-black-500 hover:text-pac-yellow-500">
      ← 기수 소개로
    </a>
  </div>
</div>
</x-app-layout>
