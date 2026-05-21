<x-app-layout>
<div class="max-w-2xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-4">

    {{-- 헤더 --}}
    <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">EVENT</p>
        <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight leading-tight">
            {{ $event->name }}
        </h1>
        <div class="flex items-center gap-3 mt-2">
            <span class="font-display text-xs font-bold uppercase tracking-widest px-2.5 py-1 rounded
                         {{ $event->isActive() ? 'bg-pac-pink-500 text-white' : 'bg-pac-black-100 text-pac-black-500' }}">
                {{ $event->isActive() ? 'LIVE' : ($event->status === 'upcoming' ? '예정' : '종료') }}
            </span>
            <span class="font-body text-sm text-pac-black-400">
                {{ $event->start_date->format('Y.m.d') }} ~ {{ $event->end_date->format('Y.m.d') }}
            </span>
            @if($event->max_participants)
                <span class="font-body text-sm text-pac-black-400">
                    · 최대 {{ number_format($event->max_participants) }}명
                </span>
            @endif
        </div>
    </div>

    {{-- 이벤트 설명 --}}
    @if($event->description)
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="prose prose-sm max-w-none text-pac-black-800">
                {!! $event->description !!}
            </div>
        </div>
    @endif

    {{-- 플래시 메시지 --}}
    @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- 신청 완료 상태 --}}
    @if($alreadyRegistered)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-pac-black-900">
                <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">참가 신청</h3>
            </div>
            <div class="p-6 text-center py-10">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p class="font-body text-sm font-semibold text-pac-black-800">참가 신청이 완료되었습니다.</p>
                <p class="font-body text-xs text-pac-black-400 mt-1">신청 내용은 관리자가 확인합니다.</p>
            </div>
        </div>

    {{-- 신청 마감 --}}
    @elseif(!$event->is_registration_open || !$event->isActive() && $event->status !== 'upcoming')
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center py-10">
            <p class="font-display text-lg font-bold text-pac-black-400 uppercase tracking-widest">신청 마감</p>
            <p class="font-body text-sm text-pac-black-400 mt-1">참가 신청이 종료되었습니다.</p>
        </div>

    {{-- 신청 폼 --}}
    @else
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 bg-pac-black-900">
                <h3 class="font-display text-sm font-bold text-white uppercase tracking-widest">참가 신청</h3>
            </div>

            <form method="POST" action="{{ route('events.register', $event) }}"
                  enctype="multipart/form-data"
                  class="p-6 space-y-5">
                @csrf

                @foreach($event->form_schema ?? [] as $field)
                    @php
                        $key      = $field['key'];
                        $type     = $field['type'];
                        $label    = $field['data']['label'] ?? '항목';
                        $required = $field['data']['required'] ?? false;
                        $options  = $field['data']['options'] ?? [];
                        $inputName = "field_{$key}";
                    @endphp

                    <div>
                        <label class="block font-body text-sm font-semibold text-pac-black-700 mb-1.5">
                            {{ $label }}
                            @if($required)
                                <span class="text-red-500">*</span>
                            @else
                                <span class="font-normal text-pac-black-400">(선택)</span>
                            @endif
                        </label>

                        @if($type === 'text')
                            <input type="text" name="{{ $inputName }}" value="{{ old($inputName) }}"
                                   {{ $required ? 'required' : '' }}
                                   class="w-full px-4 py-2.5 border border-pac-black-200 rounded-xl font-body text-sm
                                          focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-pac-yellow-400
                                          @error($inputName) border-red-400 bg-red-50 @enderror">

                        @elseif($type === 'textarea')
                            <textarea name="{{ $inputName }}" rows="4"
                                      {{ $required ? 'required' : '' }}
                                      class="w-full px-4 py-2.5 border border-pac-black-200 rounded-xl font-body text-sm resize-none
                                             focus:outline-none focus:ring-2 focus:ring-pac-yellow-400 focus:border-pac-yellow-400
                                             @error($inputName) border-red-400 bg-red-50 @enderror">{{ old($inputName) }}</textarea>

                        @elseif($type === 'radio')
                            <div class="space-y-2">
                                @foreach($options as $option)
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="radio" name="{{ $inputName }}" value="{{ $option['value'] }}"
                                               {{ old($inputName) === $option['value'] ? 'checked' : '' }}
                                               {{ $required ? 'required' : '' }}
                                               class="w-4 h-4 accent-pac-yellow-500">
                                        <span class="font-body text-sm text-pac-black-800">{{ $option['value'] }}</span>
                                    </label>
                                @endforeach
                            </div>

                        @elseif($type === 'checkbox')
                            <div class="space-y-2">
                                @foreach($options as $option)
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="{{ $inputName }}[]" value="{{ $option['value'] }}"
                                               {{ in_array($option['value'], (array) old($inputName, [])) ? 'checked' : '' }}
                                               class="w-4 h-4 accent-pac-yellow-500">
                                        <span class="font-body text-sm text-pac-black-800">{{ $option['value'] }}</span>
                                    </label>
                                @endforeach
                            </div>

                        @elseif($type === 'image')
                            <div class="border-2 border-dashed border-pac-black-200 rounded-xl px-5 py-6 text-center
                                        hover:border-pac-yellow-300 transition-colors duration-150">
                                <input type="file" name="{{ $inputName }}" accept=".jpg,.jpeg,.png,.gif,.webp"
                                       {{ $required ? 'required' : '' }}
                                       class="block mx-auto text-sm text-pac-black-500 font-body
                                              file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                              file:font-display file:text-xs file:font-bold file:uppercase file:tracking-wide
                                              file:bg-pac-yellow-500 file:text-pac-black-900 hover:file:bg-pac-yellow-400">
                                <p class="font-body text-xs text-pac-black-400 mt-2">JPG, PNG, GIF, WEBP · 최대 5MB</p>
                            </div>
                        @endif

                        @error($inputName)
                            <p class="mt-1 font-body text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <div class="pt-2 flex justify-end gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400
                                   text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                                   rounded-xl transition-colors duration-150">
                        신청 완료
                    </button>
                </div>
            </form>
        </div>
    @endif

</div>
</x-app-layout>
