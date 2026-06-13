@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <h1 class="text-2xl font-bold mb-6 text-white">이메일 발송 테스트</h1>

    @if(session('success'))
        <div class="mb-4 p-4 rounded bg-green-800 text-green-100">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 rounded bg-red-800 text-red-100">
            ❌ {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.email-test.send') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm text-gray-300 mb-1">받는 사람 (이메일)</label>
            <input type="email" name="to" value="{{ old('to') }}" required
                   class="w-full bg-gray-800 border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-yellow-400"
                   placeholder="example@email.com">
            @error('to') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-300 mb-1">제목</label>
            <input type="text" name="subject" value="{{ old('subject', '[PAC-RUN] 테스트 메일') }}" required
                   class="w-full bg-gray-800 border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-yellow-400">
            @error('subject') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-300 mb-1">본문 (HTML 가능)</label>
            <textarea name="body" rows="6" required
                      class="w-full bg-gray-800 border border-gray-600 rounded px-3 py-2 text-white focus:outline-none focus:border-yellow-400 font-mono text-sm"
                      placeholder="<p>테스트 이메일입니다.</p>">{{ old('body', '<p>PAC-RUN 이메일 발송 테스트입니다.</p>') }}</textarea>
            @error('body') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="px-6 py-2 bg-yellow-400 text-black font-bold rounded hover:bg-yellow-300 transition">
                발송
            </button>
            <span class="ml-3 text-xs text-gray-400">
                발신: {{ config('mail.from.address') }} ({{ config('mail.mailer') }})
            </span>
        </div>
    </form>
</div>
@endsection
