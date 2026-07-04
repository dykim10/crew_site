<x-app-layout>
@php
  $user   = auth()->user();
  $detail = $user->detail;
  $nick   = $user->nickname ?? $user->name ?? '?';
  $initial = mb_strtoupper(mb_substr($nick, 0, 1));
  $avatar  = $detail?->avatar_url;
@endphp

<div class="max-w-3xl mx-auto px-4 py-8 md:px-6 lg:px-8 space-y-6">

  {{-- 페이지 헤더 --}}
  <div>
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mb-3">Profile</p>
    <h1 class="font-display text-[clamp(36px,5vw,64px)] leading-none tracking-wide text-white uppercase">내 정보</h1>
    <div class="w-12 h-0.5 bg-pac-yellow-500 mt-3 mb-1"></div>
  </div>

  {{-- 아바타 섹션 --}}
  <div class="bg-pac-black-900 border border-white/[0.06] overflow-hidden">
    <div class="px-5 py-3.5 border-b border-white/[0.05] bg-pac-black-800">
      <h3 class="font-display text-xs font-black text-white uppercase tracking-[0.15em]">프로필 이미지</h3>
    </div>

    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data"
          class="p-6" x-data="avatarUpload()">
      @csrf

      <div class="flex items-start gap-6">

        {{-- 현재 아바타 미리보기 --}}
        <div class="shrink-0">
          <div class="w-24 h-24 rounded-full overflow-hidden ring-2 ring-white/10 bg-pac-black-800 flex items-center justify-center">
            <template x-if="preview">
              <img :src="preview" class="w-full h-full object-cover" alt="미리보기">
            </template>
            <template x-if="!preview">
              @if($avatar)
                <img src="{{ $avatar }}" class="w-full h-full object-cover" alt="{{ $nick }}">
              @else
                <span class="font-display text-4xl font-black text-pac-yellow-400 leading-none">{{ $initial }}</span>
              @endif
            </template>
          </div>
        </div>

        {{-- 업로드 영역 --}}
        <div class="flex-1 min-w-0">
          <div class="relative border-2 border-dashed border-pac-black-100 hover:border-pac-yellow-400 transition-colors duration-150 rounded-xl cursor-pointer"
               :class="fileName ? 'border-pac-yellow-400' : ''">
            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"
                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                   @change="handleFile($event)">
            <div class="px-5 py-6 text-center" x-show="!fileName">
              <p class="font-body text-sm text-white font-medium mb-1">클릭하거나 드래그해서 이미지 선택</p>
              <p class="font-body text-xs text-pac-black-500">JPG, PNG, WEBP · 최대 5MB · 권장: 정사각형</p>
            </div>
            <div class="px-5 py-4 flex items-center gap-3" x-show="fileName" style="display:none">
              <svg class="w-5 h-5 text-pac-yellow-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <span class="font-body text-sm text-white flex-1 truncate" x-text="fileName"></span>
              <button type="button" @click.stop="clearFile()"
                      class="font-body text-xs text-pac-black-500 hover:text-pac-pink-400 transition-colors">제거</button>
            </div>
          </div>

          @error('avatar')
            <p class="mt-2 font-body text-xs text-red-400">{{ $message }}</p>
          @enderror

          @if(session('status') === 'avatar-updated')
            <p class="mt-2 font-body text-xs text-pac-green-500">프로필 이미지가 업데이트되었습니다.</p>
          @endif

          <div class="mt-3">
            <button type="submit"
                    :disabled="!fileName"
                    class="inline-flex items-center gap-2 px-5 py-2.5
                           bg-pac-yellow-500 hover:bg-pac-yellow-400 disabled:opacity-40 disabled:cursor-not-allowed
                           text-pac-black-900 font-display font-bold text-sm uppercase tracking-wide
                           transition-colors duration-150">
              이미지 저장
            </button>
          </div>
        </div>
      </div>

    </form>
  </div>

  {{-- 프로필 정보 수정 --}}
  <div class="bg-pac-black-900 border border-white/[0.06] overflow-hidden">
    <div class="px-5 py-3.5 border-b border-white/[0.05] bg-pac-black-800">
      <h3 class="font-display text-xs font-black text-white uppercase tracking-[0.15em]">기본 정보</h3>
    </div>

    <div class="p-6">
      @include('profile.partials.update-profile-information-form')
    </div>
  </div>

  {{-- 비밀번호 변경 --}}
  <div class="bg-pac-black-900 border border-white/[0.06] overflow-hidden">
    <div class="px-5 py-3.5 border-b border-white/[0.05] bg-pac-black-800">
      <h3 class="font-display text-xs font-black text-white uppercase tracking-[0.15em]">비밀번호 변경</h3>
    </div>

    <div class="p-6">
      @include('profile.partials.request-password-reset-form')
    </div>
  </div>

  {{-- 계정 삭제 --}}
  <div class="bg-pac-black-900 border border-pac-pink-500/20 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-pac-pink-500/20 bg-pac-black-800">
      <h3 class="font-display text-xs font-black text-pac-pink-400 uppercase tracking-[0.15em]">계정 삭제</h3>
    </div>

    <div class="p-6">
      @include('profile.partials.delete-user-form')
    </div>
  </div>

</div>

<script>
function avatarUpload() {
    return {
        fileName: '',
        preview: null,
        handleFile(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (ev) => { this.preview = ev.target.result; };
            reader.readAsDataURL(file);
        },
        clearFile() {
            this.fileName = '';
            this.preview  = null;
            const input = document.querySelector('input[name="avatar"]');
            if (input) input.value = '';
        },
    }
}
</script>
</x-app-layout>
