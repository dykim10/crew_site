@extends('layouts.admin-gate')
@section('title', '관리자 인증 — PAC-RUN CREW')

@section('content')
<div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-8">

    <div class="text-center mb-8">
        <span class="inline-block bg-pac-yellow-500 text-pac-black-900 font-black text-xs tracking-widest px-3 py-1 rounded-full uppercase mb-4">PAC-RUN CREW</span>
        <h1 class="font-display text-2xl font-black text-pac-black-900">관리자 확인</h1>
        <p class="text-pac-black-400 text-sm mt-2">
            보안 구역입니다. 계속하려면<br>비밀번호를 다시 입력해 주세요.
        </p>
    </div>

    @if ($errors->any())
        <div class="bg-pac-pink-50 border border-pac-pink-200 text-pac-pink-700 text-sm rounded-lg px-4 py-3 mb-6">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.password.confirm.store') }}">
        @csrf

        <div class="mb-4">
            <label for="password" class="block text-sm font-semibold text-pac-black-900 mb-1">비밀번호</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="current-password"
                placeholder="현재 비밀번호를 입력하세요"
                class="w-full border border-pac-black-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-pac-yellow-400"
            />
        </div>

        <button type="submit"
                class="w-full bg-pac-yellow-500 text-pac-black-900 font-black py-3 rounded-lg hover:bg-pac-yellow-400 transition text-sm tracking-wide">
            확인 후 관리자 패널 이동
        </button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('dashboard') }}" class="text-sm text-pac-black-300 hover:text-pac-black-500 transition">
            대시보드로 돌아가기
        </a>
    </div>
</div>
@endsection
