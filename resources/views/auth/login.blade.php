<x-guest-layout>
    <div class="px-6 py-8">

        <h2 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight mb-6">로그인</h2>

        @if (session('status') === 'email-verified')
            <div class="mb-6 flex items-start gap-3 rounded border-l-4 border-pac-yellow-500 bg-pac-yellow-50 px-4 py-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-pac-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-sm text-pac-yellow-800">이메일 인증이 완료되었습니다.</p>
                    <p class="text-xs text-pac-yellow-700 mt-0.5">아래에서 로그인해 주세요.</p>
                </div>
            </div>
        @elseif (session('status') === 'already-verified')
            <div class="mb-6 flex items-start gap-3 rounded border-l-4 border-blue-400 bg-blue-50 px-4 py-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-sm text-blue-800">이미 인증된 계정입니다.</p>
                    <p class="text-xs text-blue-700 mt-0.5">로그인해 주세요.</p>
                </div>
            </div>
        @else
            <x-auth-session-status class="mb-4" :status="session('status')" />
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <x-input-label for="email" value="이메일" />
                    <x-text-input id="email" type="email" name="email"
                        :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="비밀번호" />
                    <x-text-input id="password" type="password"
                        name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center">
                    <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember"
                               class="rounded border-pac-black-300 text-pac-yellow-500
                                      focus:ring-pac-yellow-400 w-4 h-4">
                        <span class="font-body text-sm text-pac-black-500">로그인 유지</span>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between gap-4">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="font-body text-sm text-pac-black-400 hover:text-pac-yellow-600 transition-colors duration-200">
                        비밀번호를 잊으셨나요?
                    </a>
                @endif
                <x-primary-button class="w-full md:w-auto">
                    로그인
                </x-primary-button>
            </div>

            <div class="mt-6 pt-6 border-t border-pac-black-100 text-center">
                <a href="{{ route('register') }}"
                   class="font-body text-sm text-pac-black-400 hover:text-pac-yellow-600 transition-colors duration-200">
                    계정이 없으신가요? <span class="font-semibold text-pac-black-900">회원가입</span>
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
