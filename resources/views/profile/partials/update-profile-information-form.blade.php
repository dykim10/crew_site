<section>
    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        {{-- 닉네임 --}}
        <div>
            <label for="nickname" class="block font-display text-xs font-bold uppercase tracking-[0.12em] text-white mb-2">닉네임</label>
            <input id="nickname" name="nickname" type="text"
                   value="{{ old('nickname', $user->nickname) }}"
                   maxlength="30" required autocomplete="nickname"
                   class="w-full bg-pac-black-800 border border-pac-black-100 text-white
                          placeholder-pac-black-500 focus:outline-none focus:border-pac-yellow-400
                          px-4 py-2.5 font-body text-sm transition-colors duration-150">
            @error('nickname')
                <p class="mt-1.5 font-body text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- 이메일 (읽기 전용 — 암호화 저장) --}}
        <div>
            <label class="block font-display text-xs font-bold uppercase tracking-[0.12em] text-white mb-2">이메일</label>
            <div class="w-full bg-pac-black-900 border border-pac-black-100/50 text-pac-black-500
                        px-4 py-2.5 font-body text-sm flex items-center justify-between">
                <span>{{ $user->email ?? '(이메일 복호화 실패)' }}</span>
                <span class="text-[10px] font-display uppercase tracking-wider text-pac-black-300 bg-pac-black-800 px-2 py-0.5">변경 불가</span>
            </div>
            @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-2 flex items-center gap-3">
                    <p class="font-body text-xs text-amber-400">이메일 인증이 필요합니다.</p>
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit"
                                class="font-display text-[10px] uppercase tracking-wider underline text-pac-yellow-400 hover:text-pac-yellow-300 transition-colors">
                            인증 메일 재전송
                        </button>
                    </form>
                </div>
                @if(session('status') === 'verification-link-sent')
                    <p class="mt-1 font-body text-xs text-green-400">인증 메일이 발송되었습니다.</p>
                @endif
            @endif
        </div>

        {{-- 역할 --}}
        <div>
            <label class="block font-display text-xs font-bold uppercase tracking-[0.12em] text-white mb-2">역할</label>
            <div class="w-full bg-pac-black-900 border border-pac-black-100/50 px-4 py-2.5 font-body text-sm text-pac-black-500 flex items-center gap-2">
                @php
                  $roleLabel = match($user->role) {
                    'super_admin'  => '슈퍼관리자',
                    'region_admin' => '지부 관리자',
                    'operator'     => '운영자',
                    default        => '일반멤버',
                  };
                  $roleColor = in_array($user->role, ['super_admin','region_admin']) ? 'text-pac-yellow-400' : 'text-white';
                @endphp
                <span class="font-display text-xs uppercase tracking-wider {{ $roleColor }}">{{ $roleLabel }}</span>
            </div>
        </div>

        <div class="pt-1 flex items-center gap-4">
            <button type="submit"
                    class="px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900
                           font-display font-bold text-sm uppercase tracking-wide transition-colors duration-150">
                저장
            </button>

            @if(session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="font-body text-xs text-pac-yellow-400">저장되었습니다.</p>
            @endif
        </div>
    </form>
</section>
