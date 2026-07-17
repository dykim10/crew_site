<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-12 md:px-6 lg:px-8">

  {{-- 페이지 헤더 --}}
  <div class="mb-12">
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-4">Application</p>
    @if($form && $form->isOpen())
      <h1 class="font-display text-[clamp(36px,6vw,56px)] leading-none tracking-wide text-pac-black-900 uppercase">
        {{ $form->title }}
        @if($form->cohort)
          <span class="text-pac-yellow-500"> · {{ $form->cohort }}</span>
        @endif
      </h1>
      @if($form->open_from || $form->open_until)
        <p class="font-body text-sm text-pac-black-500 mt-4">
          모집 기간 {{ $form->open_from?->format('Y.m.d') ?? '~' }} ~ {{ $form->open_until?->format('Y.m.d') ?? '' }}
        </p>
      @endif
      @if($form->subtitle)
        <p class="font-body text-base text-pac-black-600 mt-4 leading-relaxed max-w-xl">{{ $form->subtitle }}</p>
      @endif

      @if(!empty($formImages))
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
          @foreach($formImages as $imageUrl)
            <div class="overflow-hidden border border-pac-black-100 bg-pac-black-50">
              <img src="{{ $imageUrl }}" alt="안내 이미지" class="w-full h-auto object-cover" loading="lazy">
            </div>
          @endforeach
        </div>
      @endif
    @else
      @if(!empty($generation))
        <h1 class="font-display text-[clamp(36px,6vw,56px)] leading-none tracking-wide text-pac-black-900 uppercase">
          {{ $generation->number }}기 <span class="text-pac-black-400">접수 마감</span>
        </h1>
        @php $periodForm = $closedForm ?? null; @endphp
        @if($periodForm && ($periodForm->open_from || $periodForm->open_until))
          <p class="font-body text-sm text-pac-black-500 mt-4">
            모집 기간 {{ $periodForm->open_from?->format('Y.m.d') ?? '~' }}
            ~
            {{ $periodForm->open_until?->format('Y.m.d') ?? '' }}
          </p>
        @endif
      @else
        <h1 class="font-display text-[clamp(36px,6vw,56px)] leading-none tracking-wide text-pac-black-900 uppercase">
          모집 <span class="text-pac-black-400">준비 중</span>
        </h1>
      @endif
    @endif
    <div class="w-20 h-0.5 bg-pac-yellow-500 mt-6"></div>
  </div>

  @if(!$form || !$form->isOpen())
    <div class="border border-pac-black-100 bg-pac-black-900 py-20 text-center">
      <p class="font-display text-[10px] tracking-[4px] uppercase text-pac-black-500 mb-4">Not Available</p>
      @if(!empty($generation))
        <p class="font-body text-sm text-pac-black-600 leading-relaxed">
          {{ $generation->display_name }} 모집 기간이 아닙니다.<br>
          지금은 접수를 받지 않습니다.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
          <a href="{{ route('generation.show') }}" class="pac-btn">기수 소개 보기</a>
          <a href="{{ route('home') }}" class="pac-btn-ghost">메인으로</a>
        </div>
      @else
        <p class="font-body text-sm text-pac-black-600 leading-relaxed">
          현재 모집 중인 기수 신청이 없습니다.<br>추후 공지를 확인해주세요.
        </p>
        <a href="{{ route('home') }}" class="inline-block mt-8 pac-btn-ghost">메인으로</a>
      @endif
    </div>
  @else

    @if ($errors->any())
      <div class="border border-red-500/40 bg-red-900/20 px-5 py-4 mb-6">
        <p class="font-body text-sm font-semibold text-red-400 mb-2">입력 내용을 확인해주세요.</p>
        <ul class="space-y-1">
          @foreach ($errors->all() as $error)
            <li class="font-body text-sm text-red-300">· {{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(session('error'))
      <div class="border border-red-500/40 bg-red-900/20 px-5 py-4 mb-6">
        <p class="font-body text-sm text-red-300">{{ session('error') }}</p>
      </div>
    @endif

    <form method="POST" action="{{ route('apply.store') }}">
      @csrf
      @if(!empty($generation))
        <input type="hidden" name="generation" value="{{ $generation->id }}">
      @endif
      <div class="space-y-px bg-pac-black-100 border border-pac-black-100">

        {{-- 기본 정보 --}}
        <div class="bg-pac-black-900 p-6 md:p-8 space-y-5">
          <h2 class="font-display text-[10px] tracking-[4px] uppercase text-pac-yellow-500">기본 정보</h2>

          <div>
            <label for="name" class="block font-body text-sm font-medium text-pac-black-800 mb-2">
              이름 <span class="text-pac-yellow-500">*</span>
            </label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="실명을 입력해주세요"
                   class="w-full border {{ $errors->has('name') ? 'border-red-500' : 'border-pac-black-100' }} px-4 py-3 font-body text-sm">
            @error('name')<p class="font-body text-xs text-red-400 mt-1.5">{{ $message }}</p>@enderror
          </div>

          <div>
            <label for="email" class="block font-body text-sm font-medium text-pac-black-800 mb-2">
              이메일 <span class="text-pac-yellow-500">*</span>
            </label>
            <input type="email" id="email" name="email" value="{{ old('email') }}"
                   placeholder="name@example.com"
                   required autocomplete="email" inputmode="email"
                   pattern="[^\s@]+@[^\s@]+\.[^\s@]{2,}"
                   title="올바른 이메일 형식으로 입력해주세요"
                   class="w-full border {{ $errors->has('email') ? 'border-red-500' : 'border-pac-black-100' }} px-4 py-3 font-body text-sm">
            @error('email')<p class="font-body text-xs text-red-400 mt-1.5">{{ $message }}</p>@enderror
          </div>

          <div>
            <label for="phone" class="block font-body text-sm font-medium text-pac-black-800 mb-2">
              연락처 <span class="text-pac-yellow-500">*</span>
            </label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="010-0000-0000" required
                   class="w-full border {{ $errors->has('phone') ? 'border-red-500' : 'border-pac-black-100' }} px-4 py-3 font-body text-sm">
            @error('phone')<p class="font-body text-xs text-red-400 mt-1.5">{{ $message }}</p>@enderror
          </div>
        </div>

        {{-- 희망지부 (내장) --}}
        @if(($branchOptions ?? collect())->isNotEmpty())
        <div class="bg-pac-black-900 p-6 md:p-8 space-y-4">
          <h2 class="font-display text-[10px] tracking-[4px] uppercase text-pac-yellow-500">희망지부</h2>
          <p class="font-body text-sm text-pac-black-600">활동할 지부를 하나만 선택해주세요. <span class="text-pac-yellow-500">*</span></p>
          <div class="space-y-2">
            @foreach($branchOptions as $opt)
              @php
                $bid = $opt->branch->id;
                $label = $opt->branch->name;
                if ($opt->max !== null) {
                  $label .= ' ('.$opt->count.'/'.$opt->max.')';
                }
                if ($opt->is_full) {
                  $label .= ' · 마감';
                }
              @endphp
              <label class="flex items-center gap-3 {{ $opt->is_selectable ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed' }}">
                <input type="radio"
                       name="preferred_branch_id"
                       value="{{ $bid }}"
                       {{ (string) old('preferred_branch_id') === (string) $bid ? 'checked' : '' }}
                       {{ $opt->is_selectable ? 'required' : 'disabled' }}
                       class="accent-pac-yellow-500">
                <span class="font-body text-sm text-pac-black-800">{{ $label }}</span>
              </label>
            @endforeach
          </div>
          @error('preferred_branch_id')<p class="font-body text-xs text-red-400 mt-1.5">{{ $message }}</p>@enderror
        </div>
        @endif

        {{-- 동적 필드 --}}
        @if(count($form->form_fields ?? []) > 0)
        <div class="bg-pac-black-900 p-6 md:p-8 space-y-5">
          <h2 class="font-display text-[10px] tracking-[4px] uppercase text-pac-yellow-500">추가 정보</h2>

          @foreach($form->form_fields as $field)
            @php
              $key       = $field['key'] ?? null;
              $type      = $field['type'] ?? 'text';
              $label     = $field['data']['label'] ?? '항목';
              $required  = $field['data']['required'] ?? false;
              $options   = $field['data']['options'] ?? [];
              $inputName = "field_{$key}";
              $inputClass = 'w-full border ' . ($errors->has($inputName) ? 'border-red-500' : 'border-pac-black-100') . ' px-4 py-3 font-body text-sm';
            @endphp
            @if($key)
            <div>
              <label class="block font-body text-sm font-medium text-pac-black-800 mb-2">
                {{ $label }}
                @if($required)<span class="text-pac-yellow-500">*</span>@else<span class="font-normal text-pac-black-500">(선택)</span>@endif
              </label>

              @if($type === 'text')
                <input type="text" name="{{ $inputName }}" value="{{ old($inputName) }}" {{ $required ? 'required' : '' }} class="{{ $inputClass }}">
              @elseif($type === 'textarea')
                <textarea name="{{ $inputName }}" rows="4" {{ $required ? 'required' : '' }} class="{{ $inputClass }} resize-none">{{ old($inputName) }}</textarea>
              @elseif($type === 'radio')
                <div class="space-y-2">
                  @foreach($options as $option)
                    <label class="flex items-center gap-3 cursor-pointer">
                      <input type="radio" name="{{ $inputName }}" value="{{ $option['value'] }}" {{ old($inputName) === $option['value'] ? 'checked' : '' }} {{ $required ? 'required' : '' }} class="accent-pac-yellow-500">
                      <span class="font-body text-sm text-pac-black-800">{{ $option['value'] }}</span>
                    </label>
                  @endforeach
                </div>
              @elseif($type === 'checkbox')
                <div class="space-y-2">
                  @foreach($options as $option)
                    <label class="flex items-center gap-3 cursor-pointer">
                      <input type="checkbox" name="{{ $inputName }}[]" value="{{ $option['value'] }}" {{ in_array($option['value'], (array) old($inputName, [])) ? 'checked' : '' }} class="accent-pac-yellow-500">
                      <span class="font-body text-sm text-pac-black-800">{{ $option['value'] }}</span>
                    </label>
                  @endforeach
                </div>
              @elseif($type === 'select')
                <select name="{{ $inputName }}" {{ $required ? 'required' : '' }} class="{{ $inputClass }}">
                  <option value="">선택해주세요</option>
                  @foreach($options as $option)
                    <option value="{{ $option['value'] }}" {{ old($inputName) === $option['value'] ? 'selected' : '' }}>{{ $option['value'] }}</option>
                  @endforeach
                </select>
              @endif
              @error($inputName)<p class="font-body text-xs text-red-400 mt-1.5">{{ $message }}</p>@enderror
            </div>
            @endif
          @endforeach
        </div>
        @endif

        {{-- 개인정보 동의 --}}
        <div class="bg-pac-black-900 p-6 md:p-8">
          <div class="font-body text-sm text-pac-black-600 mb-4 leading-relaxed">
            <p class="font-medium text-pac-black-900 mb-2">개인정보 수집 및 이용 동의</p>
            <p>수집 항목: 이름, 이메일, 연락처 · 목적: 기수 신청 검토 및 합격 안내 · 보유: 처리 완료 후 1년</p>
          </div>
          <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="agree_privacy" value="1" {{ old('agree_privacy') ? 'checked' : '' }} class="mt-0.5 accent-pac-yellow-500">
            <span class="font-body text-sm text-pac-black-800">개인정보 수집 및 이용에 동의합니다. <span class="text-pac-yellow-500">*</span></span>
          </label>
          @error('agree_privacy')<p class="font-body text-xs text-red-400 mt-2">{{ $message }}</p>@enderror
        </div>

      </div>

      <div class="flex flex-col sm:flex-row items-start gap-4 mt-10">
        <button type="submit" class="pac-btn">신청서 제출 →</button>
        <a href="{{ route('login') }}" class="pac-btn-ghost">로그인</a>
      </div>
    </form>
  @endif

</div>
</x-app-layout>
