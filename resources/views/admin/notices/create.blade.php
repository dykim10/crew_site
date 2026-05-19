<x-admin-layout>

    <div class="max-w-2xl space-y-5">

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.notices.index') }}"
               class="font-body text-sm text-pac-black-400 hover:text-pac-black-700 transition-colors duration-150">
                ← 목록
            </a>
            <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">공지 작성</h1>
        </div>

        <form method="POST" action="{{ route('admin.notices.store') }}"
              class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
            @csrf

            <div>
                <label class="font-body text-xs font-medium text-pac-black-500 uppercase tracking-wide mb-1.5 block">
                    제목 <span class="text-pac-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-pac-black-200 font-body text-base
                              focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent
                              @error('title') border-pac-red-500 @enderror">
                @error('title')
                    <p class="font-body text-sm text-pac-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="font-body text-xs font-medium text-pac-black-500 uppercase tracking-wide mb-1.5 block">
                    내용 <span class="text-pac-red-500">*</span>
                </label>
                <textarea name="content" rows="8" required
                          class="w-full px-4 py-3 rounded-xl border border-pac-black-200 font-body text-base
                                 focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent resize-none
                                 @error('content') border-pac-red-500 @enderror">{{ old('content') }}</textarea>
                @error('content')
                    <p class="font-body text-sm text-pac-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <label class="font-body text-xs font-medium text-pac-black-500 uppercase tracking-wide mb-1.5 block">
                        공개 대상
                    </label>
                    <select name="target_type"
                            class="w-full px-4 py-3 rounded-xl border border-pac-black-200 font-body text-base
                                   focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent">
                        <option value="all">전체 공개</option>
                        <option value="region">지역 공지</option>
                    </select>
                </div>
                <div class="flex items-end gap-3 pb-0.5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_pinned" value="1"
                               {{ old('is_pinned') ? 'checked' : '' }}
                               class="w-4 h-4 text-pac-yellow-500 rounded focus:ring-pac-yellow-400">
                        <span class="font-body text-sm text-pac-black-700">상단 고정</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 sm:flex-none px-6 py-3 bg-pac-yellow-500 hover:bg-pac-yellow-600
                               text-pac-black-900 font-body font-bold text-sm rounded-xl
                               transition-colors duration-150 min-h-[44px]">
                    공지 등록
                </button>
                <a href="{{ route('admin.notices.index') }}"
                   class="flex-1 sm:flex-none px-6 py-3 bg-pac-black-100 hover:bg-pac-black-200
                          text-pac-black-700 font-body font-bold text-sm rounded-xl text-center
                          transition-colors duration-150 min-h-[44px] flex items-center justify-center">
                    취소
                </a>
            </div>
        </form>

    </div>

</x-admin-layout>
