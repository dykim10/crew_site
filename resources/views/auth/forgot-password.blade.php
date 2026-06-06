<x-guest-layout>
    <div class="px-6 py-8">

        <h2 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight mb-2">비밀번호 찾기</h2>
        <p class="font-body text-sm text-pac-black-500 mb-6">
            가입한 이메일 주소를 입력하시면 비밀번호 재설정 링크를 보내드립니다.
        </p>

        {{-- 발송 성공 안내 --}}
        @if (session('status'))
            <div class="mb-6 flex items-start gap-3 rounded border-l-4 border-pac-yellow-500 bg-pac-yellow-50 px-4 py-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-pac-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-sm text-pac-yellow-800">이메일을 발송했습니다.</p>
                    <p class="text-xs text-pac-yellow-700 mt-0.5">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <x-input-label for="email" value="이메일" />
                    <x-text-input
                        id="email"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="가입 시 사용한 이메일" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center">
                    재설정 링크 발송
                </x-primary-button>
            </div>

            <div class="mt-6 pt-6 border-t border-pac-black-100 text-center">
                <a href="{{ route('login') }}"
                   class="font-body text-sm text-pac-black-400 hover:text-pac-yellow-600 transition-colors duration-200">
                    ← 로그인으로 돌아가기
                </a>
            </div>
        </form>

    </div>
</x-guest-layout>
