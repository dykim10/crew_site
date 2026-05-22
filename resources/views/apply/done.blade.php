<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>신청 완료 — PAC-RUN CREW</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800;900&family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-pac-black-900 min-h-screen flex flex-col items-center justify-center px-4">

    <div class="text-center max-w-md">

        {{-- 체크 아이콘 --}}
        <div class="w-20 h-20 rounded-full bg-pac-yellow-500/15 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-pac-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <p class="font-display text-[10px] font-bold text-pac-yellow-500 uppercase tracking-widest mb-2">SUBMITTED</p>
        <h1 class="font-display text-3xl font-bold text-white uppercase tracking-tight mb-4">신청 완료!</h1>
        <p class="font-body text-sm text-pac-black-400 leading-relaxed mb-8">
            기수 신청서가 정상적으로 접수되었습니다.<br>
            검토 후 입력하신 이메일로 결과를 안내드릴게요.
        </p>

        <a href="{{ route('login') }}"
           class="inline-block px-6 py-3 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900
                  font-display font-bold text-sm uppercase tracking-wide rounded-xl
                  transition-colors duration-200">
            로그인 페이지로
        </a>
    </div>

</body>
</html>
