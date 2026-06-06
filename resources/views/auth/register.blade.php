<x-guest-layout>
    <div class="px-6 py-8">

        <h2 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight mb-6">회원가입</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="이름" />
                    <x-text-input id="name" type="text" name="name"
                        :value="old('name')" required autofocus autocomplete="name" placeholder="홍길동" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="nickname" value="닉네임" />
                    <x-text-input id="nickname" type="text" name="nickname"
                        :value="old('nickname')" required placeholder="달리는 길동" />
                    <x-input-error :messages="$errors->get('nickname')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="이메일" />
                    <x-text-input id="email" type="email" name="email"
                        :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" value="비밀번호" />
                    <x-text-input id="password" type="password"
                        name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password_confirmation" value="비밀번호 확인" />
                    <x-text-input id="password_confirmation" type="password"
                        name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <div class="mt-6">
                <x-primary-button class="w-full justify-center">
                    가입하기
                </x-primary-button>
            </div>

            <div class="mt-6 pt-6 border-t border-pac-black-100 text-center">
                <a href="{{ route('login') }}"
                   class="font-body text-sm text-pac-black-400 hover:text-pac-yellow-600 transition-colors duration-200">
                    이미 계정이 있으신가요? <span class="font-semibold text-pac-black-900">로그인</span>
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
