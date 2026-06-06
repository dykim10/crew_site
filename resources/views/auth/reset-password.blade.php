<x-guest-layout>
    <div class="px-6 py-8">

        <h2 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight mb-2">새 비밀번호 설정</h2>
        <p class="font-body text-sm text-pac-black-500 mb-6">
            새로 사용할 비밀번호를 입력해 주세요.
        </p>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            {{-- 토큰 / email_hash: hidden --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

            <div class="space-y-4">
                <div>
                    <x-input-label for="password" value="새 비밀번호" />
                    <x-text-input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autofocus
                        autocomplete="new-password"
                        placeholder="8자 이상 입력해 주세요" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="새 비밀번호 확인" />
                    <x-text-input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="비밀번호를 다시 입력해 주세요" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center">
                    비밀번호 재설정
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
