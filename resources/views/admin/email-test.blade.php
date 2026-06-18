<x-app-layout>
<div class="max-w-2xl mx-auto py-10 px-4">
    <div class="mb-6">
        <span class="inline-block bg-pac-yellow-500 text-pac-black-900 font-black text-xs tracking-widest px-3 py-1 rounded-full uppercase mb-3">Admin Tool</span>
        <h1 class="font-display text-3xl font-black text-white">이메일 발송 테스트</h1>
        <p class="text-pac-black-400 text-sm mt-1">super_admin 전용 — 실제 메일 발송을 트리거합니다.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-pac-green-500/10 border border-pac-green-500/30 text-pac-green-500 text-sm font-medium">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 rounded-lg bg-pac-pink-500/10 border border-pac-pink-500/30 text-pac-pink-400 text-sm font-medium">
            ❌ {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.email-test.send') }}" class="bg-pac-black-800 border border-white/10 rounded-xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-pac-black-300 mb-1.5">받는 사람 (이메일)</label>
            <input type="email" name="to" value="{{ old('to') }}" required
                   class="w-full bg-pac-black-900 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm outline-none focus:border-pac-yellow-500 transition"
                   placeholder="example@email.com">
            @error('to') <p class="text-pac-pink-400 text-sm mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-pac-black-300 mb-1.5">제목</label>
            <input type="text" name="subject" value="{{ old('subject', '[PAC-RUN] 테스트 메일') }}" required
                   class="w-full bg-pac-black-900 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm outline-none focus:border-pac-yellow-500 transition">
            @error('subject') <p class="text-pac-pink-400 text-sm mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold text-pac-black-300 mb-1.5">본문 (HTML 가능)</label>
            <textarea name="body" rows="6" required
                      class="w-full bg-pac-black-900 border border-white/10 rounded-lg px-3 py-2.5 text-white text-sm outline-none focus:border-pac-yellow-500 transition font-mono"
                      placeholder="<p>테스트 이메일입니다.</p>">{{ old('body', '<p>PAC-RUN 이메일 발송 테스트입니다.</p>') }}</textarea>
            @error('body') <p class="text-pac-pink-400 text-sm mt-1.5">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4 pt-2 border-t border-white/10">
            <button type="submit"
                    class="px-6 py-2.5 bg-pac-yellow-500 text-pac-black-900 font-bold text-sm rounded-lg hover:bg-pac-yellow-400 transition">
                발송
            </button>
            <span class="text-xs text-pac-black-400">
                발신: {{ config('mail.from.address') }} ({{ config('mail.mailer') }})
            </span>
        </div>
    </form>
</div>
</x-app-layout>
