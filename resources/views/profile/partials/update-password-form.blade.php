<section>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password"
                   class="block font-display text-xs font-bold uppercase tracking-[0.12em] text-white mb-2">현재 비밀번호</label>
            <input id="update_password_current_password" name="current_password" type="password"
                   autocomplete="current-password"
                   class="w-full bg-pac-black-800 border border-pac-black-100 text-white
                          placeholder-pac-black-500 focus:outline-none focus:border-pac-yellow-400
                          px-4 py-2.5 font-body text-sm transition-colors duration-150">
            @if($errors->updatePassword->has('current_password'))
                <p class="mt-1.5 font-body text-xs text-red-400">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password"
                   class="block font-display text-xs font-bold uppercase tracking-[0.12em] text-white mb-2">새 비밀번호</label>
            <input id="update_password_password" name="password" type="password"
                   autocomplete="new-password"
                   class="w-full bg-pac-black-800 border border-pac-black-100 text-white
                          placeholder-pac-black-500 focus:outline-none focus:border-pac-yellow-400
                          px-4 py-2.5 font-body text-sm transition-colors duration-150">
            @if($errors->updatePassword->has('password'))
                <p class="mt-1.5 font-body text-xs text-red-400">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation"
                   class="block font-display text-xs font-bold uppercase tracking-[0.12em] text-white mb-2">비밀번호 확인</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   autocomplete="new-password"
                   class="w-full bg-pac-black-800 border border-pac-black-100 text-white
                          placeholder-pac-black-500 focus:outline-none focus:border-pac-yellow-400
                          px-4 py-2.5 font-body text-sm transition-colors duration-150">
            @if($errors->updatePassword->has('password_confirmation'))
                <p class="mt-1.5 font-body text-xs text-red-400">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="pt-1 flex items-center gap-4">
            <button type="submit"
                    class="px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900
                           font-display font-bold text-sm uppercase tracking-wide transition-colors duration-150">
                변경
            </button>

            @if(session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="font-body text-xs text-pac-yellow-400">비밀번호가 변경되었습니다.</p>
            @endif
        </div>
    </form>
</section>
