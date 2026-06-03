{{-- 고정점수 이벤트 제출물 섹션 (resources/views/events/partials/fixed-submission.blade.php)
     대시보드에서 include 로 사용됨
     @param Event $event (score_type='fixed')
     @param Collection $mySubmissions (사용자의 제출 내역)
--}}

<div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden"
     x-data="fixedSubmissionForm()"
     @submit.prevent="handleSubmit">

  {{-- 헤더 --}}
  <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/[0.06]">
    <span class="font-display text-xs font-black text-white uppercase tracking-[0.15em]">미션 제출</span>
    <span class="font-display text-[10px] font-bold uppercase tracking-widest text-pac-yellow-500">
      {{ $event->name }}
    </span>
  </div>

  {{-- 미션 설명 --}}
  @if($event->score_rules && isset($event->score_rules['fixed_description']))
    <div class="px-5 py-4 border-b border-white/[0.06] bg-pac-black-800">
      <p class="font-display text-[9px] font-bold uppercase tracking-widest text-pac-black-400 mb-2">미션 설명</p>
      <p class="font-body text-sm text-pac-black-300 leading-relaxed">
        {{ $event->score_rules['fixed_description'] }}
      </p>
    </div>
  @endif

  {{-- 폼 (업로드 영역) --}}
  <form action="{{ route('events.submit-fixed', $event) }}" method="POST" enctype="multipart/form-data"
        class="px-5 py-5 border-b border-white/[0.06]">
    @csrf

    <div class="mb-4">
      <label for="image" class="block cursor-pointer group">
        {{-- 드래그 영역 --}}
        <div class="relative border-2 border-dashed border-white/[0.2] rounded-lg p-6 py-8
                    group-hover:border-pac-yellow-500/50 group-hover:bg-pac-black-800
                    transition-all duration-150"
             @dragover.prevent="isDragging = true"
             @dragleave.prevent="isDragging = false"
             @drop.prevent="handleDrop($event)"
             :class="{'border-pac-yellow-500 bg-pac-black-800': isDragging}">

          <input type="file" id="image" name="image" accept="image/*" class="hidden"
                 @change="handleFileSelect($event)">

          {{-- 아이콘 + 텍스트 --}}
          <div class="text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-pac-yellow-500/40 group-hover:text-pac-yellow-500/60 transition-colors"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <p class="font-body text-sm text-pac-black-400 mb-1">
              이미지를 드래그하거나 클릭하여 선택
            </p>
            <p class="font-body text-xs text-pac-black-600">
              JPG, PNG, WebP (최대 10MB)
            </p>
          </div>
        </div>
      </label>

      @error('image')
        <div class="mt-2 text-sm text-pac-pink-500">
          {{ $message }}
        </div>
      @enderror
    </div>

    {{-- 선택된 파일 미리보기 --}}
    <template x-if="preview">
      <div class="mb-4">
        <div class="relative rounded-lg overflow-hidden border border-white/[0.1]">
          <img :src="preview" alt="Preview" class="w-full h-auto max-h-64 object-cover">
          <button type="button"
                  @click="clearPreview()"
                  class="absolute top-2 right-2 bg-pac-pink-500 hover:bg-pac-pink-600 text-white p-2 rounded transition-colors">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
          </button>
        </div>
      </div>
    </template>

    {{-- 제출 버튼 --}}
    <button type="submit"
            :disabled="isSubmitting || !preview"
            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3
                   bg-pac-yellow-500 hover:bg-pac-yellow-400
                   disabled:bg-pac-black-600 disabled:text-pac-black-500 disabled:cursor-not-allowed
                   text-pac-black-900 font-display font-black text-sm uppercase tracking-wider
                   transition-colors duration-150">
      <template x-if="!isSubmitting">
        <span>제출하기</span>
      </template>
      <template x-if="isSubmitting">
        <span class="inline-flex items-center gap-2">
          <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          처리 중...
        </span>
      </template>
    </button>
  </form>

  {{-- 제출 내역 목록 --}}
  @if($mySubmissions && count($mySubmissions) > 0)
    <div class="px-5 py-4">
      <p class="font-display text-[9px] font-bold uppercase tracking-widest text-pac-black-400 mb-3">
        제출 내역 ({{ count($mySubmissions) }})
      </p>

      <div class="space-y-3">
        @forelse($mySubmissions as $submission)
          <div class="border border-white/[0.06] rounded-lg overflow-hidden hover:border-white/[0.1] transition-colors">
            {{-- 이미지 + 상태 --}}
            <div class="flex gap-3 p-3">
              {{-- 썸네일 --}}
              <a href="{{ $submission->image_url }}" target="_blank" rel="noopener"
                 class="shrink-0 w-16 h-16 rounded overflow-hidden border border-white/[0.05] group">
                <img src="{{ $submission->image_url }}" alt="Submission"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
              </a>

              {{-- 메타 정보 + 상태 --}}
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2 mb-2">
                  <div>
                    <p class="font-display text-[10px] text-pac-black-600 uppercase tracking-wider">
                      {{ $submission->created_at->format('Y.m.d H:i') }}
                    </p>
                  </div>

                  {{-- 상태 배지 --}}
                  <span class="inline-flex items-center px-2 py-1 rounded text-[9px] font-bold uppercase tracking-widest shrink-0
                             @if($submission->status === 'pending')
                               bg-pac-yellow-500/20 text-pac-yellow-500 border border-pac-yellow-500/30
                             @elseif($submission->status === 'approved')
                               bg-pac-green-500/20 text-pac-green-500 border border-pac-green-500/30
                             @else
                               bg-pac-pink-500/20 text-pac-pink-500 border border-pac-pink-500/30
                             @endif">
                    @switch($submission->status)
                      @case('pending')
                        <span class="w-1 h-1 rounded-full bg-pac-yellow-500 mr-1.5"></span>검수 대기
                        @break
                      @case('approved')
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>승인 완료
                        @break
                      @case('rejected')
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>반려됨
                        @break
                    @endswitch
                  </span>
                </div>

                {{-- 관리자 노트 (반려 시) --}}
                @if($submission->status === 'rejected' && $submission->admin_note)
                  <p class="font-body text-xs text-pac-pink-400 leading-snug">
                    <span class="font-bold">관리자 피드백:</span> {{ $submission->admin_note }}
                  </p>
                @endif

                {{-- 승인 완료 시 --}}
                @if($submission->status === 'approved')
                  <p class="font-body text-xs text-pac-green-400">
                    ✓ {{ $submission->confirmed_at->format('Y.m.d') }}에 승인됨
                  </p>
                @endif
              </div>
            </div>
          </div>
        @empty
        @endforelse
      </div>
    </div>
  @endif

</div>

<script>
function fixedSubmissionForm() {
  return {
    preview: null,
    isDragging: false,
    isSubmitting: false,

    handleFileSelect(event) {
      const file = event.target.files?.[0];
      if (file) {
        this.loadPreview(file);
      }
    },

    handleDrop(event) {
      this.isDragging = false;
      const file = event.dataTransfer?.files?.[0];
      if (file && file.type.startsWith('image/')) {
        document.querySelector('#image').files = event.dataTransfer.files;
        this.loadPreview(file);
      }
    },

    loadPreview(file) {
      const reader = new FileReader();
      reader.onload = (e) => {
        this.preview = e.target.result;
      };
      reader.readAsDataURL(file);
    },

    clearPreview() {
      this.preview = null;
      document.querySelector('#image').value = '';
    },

    handleSubmit() {
      this.isSubmitting = true;
      // 폼은 자동으로 제출됨
    }
  };
}
</script>
