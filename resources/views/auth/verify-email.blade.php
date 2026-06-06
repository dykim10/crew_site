<x-guest-layout>
    <div class="px-6 py-8">

        {{-- 아이콘 --}}
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-pac-yellow-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-pac-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        {{-- 타이틀 --}}
        @if(session('status') === 'registered')
            <h2 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight mb-2 text-center">
                환영합니다!
            </h2>
            <p class="font-body text-sm text-pac-black-500 text-center mb-6">
                PAC-RUN 크루에 오신 걸 환영합니다.<br>
                가입하신 이메일로 인증 링크를 발송했습니다.<br>
                <span class="font-semibold text-pac-black-900">이메일의 링크를 클릭하면 가입이 완료됩니다.</span>
            </p>
        @else
            <h2 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight mb-2 text-center">
                이메일 인증 필요
            </h2>
            <p class="font-body text-sm text-pac-black-500 text-center mb-6">
                서비스 이용을 위해 이메일 인증이 필요합니다.<br>
                발송된 인증 링크를 확인해주세요.
            </p>
        @endif

        {{-- 재발송 성공 메시지 --}}
        @if(session('status') === 'verification-link-sent')
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="font-body text-sm text-green-700 text-center">
                    인증 이메일을 재발송했습니다. 받은편지함을 확인해주세요.
                </p>
            </div>
        @endif

        {{-- 재발송 버튼 --}}
        <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
            @csrf
            <x-primary-button class="w-full justify-center">
                인증 이메일 재발송
            </x-primary-button>
        </form>

        {{-- 도움말 --}}
        <div class="p-3 bg-pac-black-50 rounded-lg mb-6">
            <p class="font-body text-xs text-pac-black-500 leading-relaxed">
                <span class="font-semibold">이메일이 오지 않나요?</span><br>
                스팸 메일함을 확인하거나, 위 버튼을 눌러 재발송하세요.<br>
                인증 링크는 <span class="font-semibold">60분</span> 동안 유효합니다.
            </p>
        </div>

        {{-- 로그아웃 --}}
        <div class="pt-4 border-t border-pac-black-100 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="font-body text-sm text-pac-black-400 hover:text-pac-yellow-600 transition-colors duration-200">
                    다른 계정으로 로그인
                </button>
            </form>
        </div>

    </div>
</x-guest-layout>
