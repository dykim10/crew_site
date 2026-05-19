<x-guest-layout>
    <div class="px-6 py-8">

        <h2 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight mb-6">로그인</h2>

        <x-auth-session-status class="mb-4" :status="session('status')" />

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
