<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>접근 불가 — PAC-RUN CREW</title>
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
    <div class="text-center px-6">
        <div class="mb-6">
            <span class="inline-block bg-pac-yellow text-pac-black font-black text-xs tracking-widest px-3 py-1 rounded-full uppercase">PAC-RUN CREW</span>
        </div>

        <div class="text-8xl font-black text-pac-black mb-4">403</div>

        <h1 class="text-2xl font-bold text-pac-black mb-2">접근 권한이 없습니다</h1>
        <p class="text-gray-500 mb-8">
            관리자 전용 페이지입니다.<br>
            일반 구성원은 이 페이지에 접근할 수 없습니다.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 bg-pac-yellow text-pac-black font-bold px-6 py-3 rounded-lg hover:bg-yellow-400 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                대시보드로 돌아가기
            </a>
            <a href="javascript:history.back()"
               class="inline-flex items-center gap-2 border border-gray-300 text-gray-600 font-medium px-6 py-3 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                이전 페이지
            </a>
        </div>
    </div>
</body>
</html>
