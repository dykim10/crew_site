<section>
    <p class="font-body text-sm text-pac-black-500 mb-5 leading-relaxed">
        비밀번호 변경은 보안을 위해 등록된 이메일로 재설정 링크를 보내드립니다.<br>
        메일의 링크를 클릭한 뒤 새 비밀번호를 설정해 주세요.
    </p>

    @if($errors->has('password_reset'))
        <p class="mb-4 font-body text-xs text-red-400">{{ $errors->first('password_reset') }}</p>
    @endif

    <form method="post" action="{{ route('profile.password-reset') }}" class="space-y-4">
        @csrf

        <p class="font-body text-xs text-pac-black-400">
            발송 대상: <span class="text-white">{{ auth()->user()->email }}</span>
        </p>

        <div class="pt-1 flex items-center gap-4">
            <button type="submit"
                    class="px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900
                           font-display font-bold text-sm uppercase tracking-wide transition-colors duration-150">
                재설정 링크 받기
            </button>

            @if(session('status') === 'password-reset-link-sent')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 5000)"
                   class="font-body text-xs text-pac-yellow-400">
                    이메일을 발송했습니다. 받은편지함을 확인해 주세요.
                </p>
            @endif
        </div>
    </form>
</section>
