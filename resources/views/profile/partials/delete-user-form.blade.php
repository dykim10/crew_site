<section x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">
    <p class="font-body text-sm text-pac-black-400 mb-5">
        계정을 삭제하면 모든 기록과 데이터가 영구적으로 제거됩니다.<br>삭제 전 필요한 데이터를 먼저 백업해주세요.
    </p>

    <button type="button" @click="open = true"
            class="px-5 py-2.5 bg-pac-pink-500 hover:bg-pac-pink-400 text-white
                   font-display font-bold text-sm uppercase tracking-wide transition-colors duration-150">
        계정 삭제
    </button>

    {{-- 삭제 확인 모달 --}}
    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="display:none">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="open = false"></div>

        <div class="relative z-10 w-full max-w-md bg-pac-black-900 border border-pac-pink-500/30 shadow-2xl">
            <div class="px-5 py-3.5 border-b border-pac-pink-500/20 bg-pac-black-800 flex items-center justify-between">
                <h3 class="font-display text-xs font-black text-pac-pink-400 uppercase tracking-[0.15em]">계정 삭제 확인</h3>
                <button type="button" @click="open = false"
                        class="text-pac-black-400 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-5">
                @csrf
                @method('delete')

                <p class="font-body text-sm text-white leading-relaxed">
                    계정이 <strong class="text-pac-pink-400">영구적으로 삭제</strong>됩니다.<br>
                    확인을 위해 현재 비밀번호를 입력해주세요.
                </p>

                <div>
                    <label for="del_password" class="sr-only">비밀번호</label>
                    <input id="del_password" name="password" type="password" placeholder="현재 비밀번호"
                           class="w-full bg-pac-black-800 border border-pac-black-100 text-white
                                  placeholder-pac-black-500 focus:outline-none focus:border-pac-pink-400
                                  px-4 py-2.5 font-body text-sm transition-colors duration-150">
                    @if($errors->userDeletion->has('password'))
                        <p class="mt-1.5 font-body text-xs text-red-400">{{ $errors->userDeletion->first('password') }}</p>
                    @endif
                </div>

                <div class="flex justify-end gap-3 pt-1">
                    <button type="button" @click="open = false"
                            class="px-4 py-2 border border-pac-black-100 text-pac-black-400
                                   hover:border-white hover:text-white font-display text-xs uppercase tracking-wide
                                   transition-colors duration-150">
                        취소
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-pac-pink-500 hover:bg-pac-pink-400 text-white
                                   font-display font-bold text-xs uppercase tracking-wide transition-colors duration-150">
                        삭제 확인
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

