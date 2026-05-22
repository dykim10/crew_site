<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>기수 신청{{ $form ? ' — ' . $form->title : '' }} — PAC-RUN CREW</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800;900&family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-pac-black-900 min-h-screen">

    {{-- 헤더 --}}
    <header class="bg-pac-black-900 border-b border-white/10">
        <div class="max-w-2xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-1">
                <span class="font-display text-2xl font-bold text-pac-yellow-500 uppercase tracking-tight">PAC</span>
                <span class="font-display text-2xl font-bold text-white uppercase tracking-tight">RUN</span>
                <span class="font-display text-xs text-pac-black-400 ml-2 uppercase tracking-widest hidden sm:block">CREW</span>
            </div>
            <a href="{{ route('login') }}"
               class="font-body text-sm text-pac-black-400 hover:text-pac-yellow-400 transition-colors duration-200">
                로그인
            </a>
        </div>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-10">

        @if(!$form || !$form->isOpen())
            {{-- 모집 없음 --}}
            <div class="text-center py-20">
                <p class="font-display text-[10px] font-bold text-pac-black-500 uppercase tracking-widest mb-3">NOT AVAILABLE</p>
                <h1 class="font-display text-3xl font-bold text-white uppercase tracking-tight mb-4">모집 준비 중</h1>
                <p class="font-body text-sm text-pac-black-400">현재 모집 중인 기수 신청이 없습니다.<br>추후 공지를 확인해주세요.</p>
            </div>
        @else

            {{-- 타이틀 --}}
            <div class="mb-8">
                <p class="font-display text-[10px] font-bold text-pac-yellow-500 uppercase tracking-widest mb-1">APPLICATION</p>
                <h1 class="font-display text-3xl font-bold text-white uppercase tracking-tight leading-tight">
                    {{ $form->title }}
                    @if($form->cohort)
                        <span class="text-pac-yellow-500"> · {{ $form->cohort }}</span>
                    @endif
                </h1>
                @if($form->open_from || $form->open_until)
                    <p class="font-body text-xs text-pac-black-500 mt-1">
                        모집 기간:
                        {{ $form->open_from?->format('Y.m.d') ?? '~' }}
                        ~
                        {{ $form->open_until?->format('Y.m.d') ?? '' }}
                    </p>
                @endif
                @if($form->subtitle)
                    <p class="font-body text-sm text-pac-black-400 mt-2 leading-relaxed">{{ $form->subtitle }}</p>
                @endif
            </div>

            {{-- 에러 메시지 --}}
            @if ($errors->any())
                <div class="bg-red-900/30 border border-red-500/40 rounded-xl px-5 py-4 mb-6">
                    <p class="font-body text-sm font-semibold text-red-400 mb-2">입력 내용을 확인해주세요.</p>
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="font-body text-sm text-red-300 flex items-start gap-2">
                                <span class="mt-0.5 shrink-0">•</span>{{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-900/30 border border-red-500/40 rounded-xl px-5 py-4 mb-6">
                    <p class="font-body text-sm text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('apply.store') }}">
                @csrf

                <div class="space-y-6">

                    {{-- 기본 정보 (항상 표시) --}}
                    <div class="bg-white/5 rounded-2xl p-6 space-y-5">
                        <h2 class="font-display text-xs font-bold text-pac-yellow-500 uppercase tracking-widest">기본 정보</h2>

                        {{-- 이름 --}}
                        <div>
                            <label for="name" class="block font-body text-sm font-semibold text-white mb-1.5">
                                이름 <span class="text-pac-yellow-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   placeholder="실명을 입력해주세요"
                                   class="w-full bg-pac-black-800 border {{ $errors->has('name') ? 'border-red-500' : 'border-white/10' }}
                                          text-white placeholder-pac-black-500 rounded-xl px-4 py-3
                                          font-body text-sm focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent">
                            @error('name')
                                <p class="font-body text-xs text-red-400 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 이메일 --}}
                        <div>
                            <label for="email" class="block font-body text-sm font-semibold text-white mb-1.5">
                                이메일 <span class="text-pac-yellow-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   placeholder="연락 가능한 이메일"
                                   class="w-full bg-pac-black-800 border {{ $errors->has('email') ? 'border-red-500' : 'border-white/10' }}
                                          text-white placeholder-pac-black-500 rounded-xl px-4 py-3
                                          font-body text-sm focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent">
                            @error('email')
                                <p class="font-body text-xs text-red-400 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 연락처 --}}
                        <div>
                            <label for="phone" class="block font-body text-sm font-semibold text-white mb-1.5">
                                연락처 <span class="font-normal text-pac-black-500">(선택)</span>
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                   placeholder="010-0000-0000"
                                   class="w-full bg-pac-black-800 border border-white/10
                                          text-white placeholder-pac-black-500 rounded-xl px-4 py-3
                                          font-body text-sm focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent">
                        </div>
                    </div>

                    {{-- 동적 필드 (관리자가 구성) --}}
                    @if(count($form->form_fields ?? []) > 0)
                        <div class="bg-white/5 rounded-2xl p-6 space-y-5">
                            <h2 class="font-display text-xs font-bold text-pac-yellow-500 uppercase tracking-widest">추가 정보</h2>

                            @foreach($form->form_fields as $field)
                                @php
                                    $key      = $field['key'] ?? null;
                                    $type     = $field['type'] ?? 'text';
                                    $label    = $field['data']['label'] ?? '항목';
                                    $required = $field['data']['required'] ?? false;
                                    $options  = $field['data']['options'] ?? [];
                                    $inputName = "field_{$key}";
                                @endphp

                                @if($key)
                                <div>
                                    <label class="block font-body text-sm font-semibold text-white mb-1.5">
                                        {{ $label }}
                                        @if($required)
                                            <span class="text-pac-yellow-500">*</span>
                                        @else
                                            <span class="font-normal text-pac-black-500">(선택)</span>
                                        @endif
                                    </label>

                                    @if($type === 'text')
                                        <input type="text" name="{{ $inputName }}" value="{{ old($inputName) }}"
                                               {{ $required ? 'required' : '' }}
                                               class="w-full bg-pac-black-800 border {{ $errors->has($inputName) ? 'border-red-500' : 'border-white/10' }}
                                                      text-white placeholder-pac-black-500 rounded-xl px-4 py-3
                                                      font-body text-sm focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent">

                                    @elseif($type === 'textarea')
                                        <textarea name="{{ $inputName }}" rows="4"
                                                  {{ $required ? 'required' : '' }}
                                                  class="w-full bg-pac-black-800 border {{ $errors->has($inputName) ? 'border-red-500' : 'border-white/10' }}
                                                         text-white placeholder-pac-black-500 rounded-xl px-4 py-3
                                                         font-body text-sm focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent resize-none">{{ old($inputName) }}</textarea>

                                    @elseif($type === 'radio')
                                        <div class="space-y-2">
                                            @foreach($options as $option)
                                                <label class="flex items-center gap-3 cursor-pointer">
                                                    <input type="radio" name="{{ $inputName }}" value="{{ $option['value'] }}"
                                                           {{ old($inputName) === $option['value'] ? 'checked' : '' }}
                                                           {{ $required ? 'required' : '' }}
                                                           class="w-4 h-4 accent-pac-yellow-500">
                                                    <span class="font-body text-sm text-white">{{ $option['value'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                    @elseif($type === 'checkbox')
                                        <div class="space-y-2">
                                            @foreach($options as $option)
                                                <label class="flex items-center gap-3 cursor-pointer">
                                                    <input type="checkbox" name="{{ $inputName }}[]" value="{{ $option['value'] }}"
                                                           {{ in_array($option['value'], (array) old($inputName, [])) ? 'checked' : '' }}
                                                           class="rounded w-4 h-4 accent-pac-yellow-500">
                                                    <span class="font-body text-sm text-white">{{ $option['value'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>

                                    @elseif($type === 'select')
                                        <select name="{{ $inputName }}"
                                                {{ $required ? 'required' : '' }}
                                                class="w-full bg-pac-black-800 border {{ $errors->has($inputName) ? 'border-red-500' : 'border-white/10' }}
                                                       text-white rounded-xl px-4 py-3
                                                       font-body text-sm focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent">
                                            <option value="" class="bg-pac-black-800">선택해주세요</option>
                                            @foreach($options as $option)
                                                <option value="{{ $option['value'] }}"
                                                        {{ old($inputName) === $option['value'] ? 'selected' : '' }}
                                                        class="bg-pac-black-800">{{ $option['value'] }}</option>
                                            @endforeach
                                        </select>
                                    @endif

                                    @error($inputName)
                                        <p class="font-body text-xs text-red-400 mt-1.5">{{ $message }}</p>
                                    @enderror
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    {{-- 개인정보 동의 --}}
                    <div class="bg-white/5 rounded-2xl p-5">
                        <div class="font-body text-sm text-pac-black-400 mb-4 leading-relaxed">
                            <p class="font-semibold text-white mb-2">개인정보 수집 및 이용 동의</p>
                            <p>수집 항목: 이름, 이메일, 연락처</p>
                            <p>수집 목적: 기수 신청 검토 및 합격 안내</p>
                            <p>보유 기간: 신청 처리 완료 후 1년</p>
                        </div>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="agree_privacy" value="1"
                                   {{ old('agree_privacy') ? 'checked' : '' }}
                                   class="mt-0.5 rounded border-white/20 bg-pac-black-800 text-pac-yellow-500
                                          focus:ring-pac-yellow-400 w-4 h-4 shrink-0">
                            <span class="font-body text-sm text-white">
                                개인정보 수집 및 이용에 동의합니다. <span class="text-pac-yellow-500">*</span>
                            </span>
                        </label>
                        @error('agree_privacy')
                            <p class="font-body text-xs text-red-400 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 제출 --}}
                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-8 py-3.5 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900
                                       font-display font-bold text-sm uppercase tracking-wide rounded-xl
                                       transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:ring-offset-2 focus:ring-offset-pac-black-900">
                            신청서 제출
                        </button>
                    </div>

                </div>
            </form>
        @endif
    </main>

    <footer class="max-w-2xl mx-auto px-4 py-8 border-t border-white/5 mt-4">
        <p class="font-body text-xs text-pac-black-500 text-center">© 2026 PAC-RUN CREW. All rights reserved.</p>
    </footer>

</body>
</html>
