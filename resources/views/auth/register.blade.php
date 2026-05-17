<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="이름" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                :value="old('name')" required autofocus autocomplete="name" placeholder="홍길동" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="nickname" value="닉네임" />
            <x-text-input id="nickname" class="block mt-1 w-full" type="text" name="nickname"
                :value="old('nickname')" required placeholder="달리는 길동" />
            <x-input-error :messages="$errors->get('nickname')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="이메일" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="비밀번호" />
            <x-text-input id="password" class="block mt-1 w-full" type="password"
                name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="비밀번호 확인" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="invite_code" value="초대 코드" />
            <x-text-input id="invite_code" class="block mt-1 w-full" type="text" name="invite_code"
                :value="old('invite_code')" required placeholder="초대 코드를 입력하세요" />
            <x-input-error :messages="$errors->get('invite_code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                이미 계정이 있으신가요?
            </a>
            <x-primary-button class="ms-4">
                가입하기
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
