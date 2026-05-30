<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 인증 — PAC-RUN CREW</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pac-yellow': '#E5AD16',
                        'pac-black':  '#1A1212',
                        'pac-pink':   '#E80043',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md p-8">

        <div class="text-center mb-8">
            <span class="inline-block bg-pac-yellow text-pac-black font-black text-xs tracking-widest px-3 py-1 rounded-full uppercase mb-4">PAC-RUN CREW</span>
            <h1 class="text-2xl font-black text-pac-black">관리자 확인</h1>
            <p class="text-gray-500 text-sm mt-2">
                보안 구역입니다. 계속하려면<br>비밀번호를 다시 입력해 주세요.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg px-4 py-3 mb-6">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.password.confirm.store') }}">
            @csrf

            <div class="mb-4">
                <label for="password" class="block text-sm font-semibold text-pac-black mb-1">비밀번호</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autofocus
                    autocomplete="current-password"
                    placeholder="현재 비밀번호를 입력하세요"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-pac-yellow focus:border-pac-yellow"
                />
            </div>

            <button type="submit"
                    class="w-full bg-pac-yellow text-pac-black font-black py-3 rounded-lg hover:bg-yellow-400 transition text-sm tracking-wide">
                확인 후 관리자 패널 이동
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-400 hover:text-gray-600 transition">
                대시보드로 돌아가기
            </a>
        </div>
    </div>
</body>
</html>
