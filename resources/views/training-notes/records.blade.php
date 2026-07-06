<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-6 md:px-6 space-y-5"
     x-data="pbManager(@js([
         'parseUrl' => route('training-notes.records.parse'),
         'csrf' => csrf_token(),
         'defaults' => [
             'distance_type' => old('distance_type', '5K'),
             'record_time' => old('record_time', ''),
             'achieved_at' => old('achieved_at', now()->toDateString()),
             'source' => old('source', 'manual'),
         ],
     ]))">

  <div>
    <a href="{{ route('training-notes.index') }}" class="font-body text-sm text-white/40 hover:text-pac-yellow-400">&larr; 훈련노트</a>
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mt-4 mb-2">PERSONAL BEST</p>
    <h1 class="font-display text-3xl text-white uppercase tracking-wide">PB 관리</h1>
    <div class="w-12 h-0.5 bg-pac-yellow-500 mt-3"></div>
  </div>

  @if(session('success'))
    <div class="px-4 py-3 bg-pac-green-500/10 border border-pac-green-500/30 text-pac-green-500 text-sm">{{ session('success') }}</div>
  @endif

  {{-- 최신 PB 요약 --}}
  @if($latestByDistance->isNotEmpty())
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
      @foreach($latestByDistance as $rec)
        <div class="bg-pac-black-900 border-t-2 border-t-pac-yellow-500 border border-white/[0.05] px-4 py-3">
          <p class="font-display text-[9px] text-pac-black-500 uppercase tracking-widest">{{ $rec->distance_type }}</p>
          <p class="font-display text-xl text-white mt-1">{{ $rec->record_formatted }}</p>
          <p class="font-body text-xs text-white/40 mt-1">{{ $rec->achieved_at->format('Y.m.d') }}</p>
        </div>
      @endforeach
    </div>
  @endif

  {{-- 입력 방식 탭 --}}
  <div class="flex gap-2">
    <button type="button" @click="mode = 'manual'"
            :class="mode === 'manual' ? 'bg-pac-yellow-500 text-pac-black-900' : 'bg-white/5 text-white/60'"
            class="px-4 py-2 font-display text-xs uppercase tracking-wider">직접 입력</button>
    <button type="button" @click="mode = 'image'"
            :class="mode === 'image' ? 'bg-pac-yellow-500 text-pac-black-900' : 'bg-white/5 text-white/60'"
            class="px-4 py-2 font-display text-xs uppercase tracking-wider">기록 사진</button>
  </div>

  {{-- 사진 업로드 (파싱만, 저장 없음) --}}
  <div x-show="mode === 'image'" x-cloak
       class="bg-pac-black-900 border border-white/[0.05] p-5 space-y-4">
    <p class="font-body text-sm text-white/50 leading-relaxed">
      러닝 앱 기록·대회 결과 캡처를 업로드하면 거리·기록·달성일을 자동으로 채워 드립니다.
      <span class="text-pac-yellow-400/90">업로드한 이미지는 파싱 후 서버에 저장되지 않습니다.</span>
      추출된 값을 확인한 뒤 아래 폼에서 PB 등록을 눌러 주세요.
    </p>
    <input type="file" accept="image/*" @change="uploadImage($event)"
           :disabled="parsing"
           class="text-white/60 text-sm file:mr-3 file:px-3 file:py-1.5 file:border-0 file:bg-white/10 file:text-white file:text-xs file:uppercase file:tracking-wider">
    <p x-show="parsing" class="font-body text-sm text-white/40">이미지 분석 중…</p>
  </div>

  <p x-show="message" x-text="message" class="text-sm" :class="messageOk ? 'text-pac-green-500' : 'text-red-400'"></p>

  {{-- 등록 폼 --}}
  <form method="POST" action="{{ route('training-notes.records.store') }}"
        class="bg-pac-black-900 border border-white/[0.05] p-5 space-y-4">
    @csrf
    <input type="hidden" name="source" x-model="form.source">
    <template x-if="form.source === 'image'">
      <p class="font-body text-xs text-pac-yellow-400/80 border border-pac-yellow-500/20 bg-pac-yellow-500/5 px-3 py-2">
        사진에서 추출한 값입니다. 확인·수정 후 등록해 주세요. (원본 이미지는 저장되지 않았습니다)
      </p>
    </template>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div>
        <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">거리</label>
        <select name="distance_type" required x-model="form.distance_type"
                class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
          @foreach(['1K','5K','10K','HALF','FULL'] as $d)
            <option value="{{ $d }}">{{ $d }}</option>
          @endforeach
        </select>
        @error('distance_type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">기록 (MM:SS 또는 H:MM:SS)</label>
        <input type="text" name="record_time" required placeholder="22:30" x-model="form.record_time"
               class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        @error('record_time')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">달성일</label>
        <input type="date" name="achieved_at" required max="{{ now()->toDateString() }}" x-model="form.achieved_at"
               class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        @error('achieved_at')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
    </div>
    <button type="submit"
            class="px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900 font-display text-sm font-black uppercase tracking-wider">
      PB 등록
    </button>
  </form>

  {{-- 이력 --}}
  <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
    <div class="px-5 py-3 border-b border-white/[0.06] font-display text-xs text-white uppercase tracking-widest">전체 이력</div>
    @forelse($records as $rec)
      <div class="flex items-center justify-between px-5 py-3 border-b border-white/[0.04] last:border-0">
        <div>
          <span class="font-display text-sm text-pac-yellow-400">{{ $rec->distance_type }}</span>
          <span class="font-body text-white ml-3">{{ $rec->record_formatted }}</span>
          <span class="font-body text-white/40 text-sm ml-2">{{ $rec->achieved_at->format('Y.m.d') }}</span>
        </div>
        <form method="POST" action="{{ route('training-notes.records.destroy', $rec) }}"
              onsubmit="return confirm('삭제하시겠습니까?')">
          @csrf @method('DELETE')
          <button type="submit" class="text-white/30 hover:text-red-400 text-xs">삭제</button>
        </form>
      </div>
    @empty
      <p class="px-5 py-8 text-center text-white/30 font-body text-sm">등록된 PB가 없습니다.</p>
    @endforelse
  </div>
</div>

<script>
function pbManager(config) {
  return {
    ...config,
    mode: 'manual',
    parsing: false,
    message: '',
    messageOk: false,
    form: { ...config.defaults },

    async uploadImage(e) {
      const file = e.target.files[0];
      if (!file) return;
      this.parsing = true;
      this.message = '';
      const fd = new FormData();
      fd.append('image', file);
      try {
        const res = await fetch(this.parseUrl, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
          body: fd,
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || '파싱에 실패했습니다.');
        const p = data.data;
        if (p.distance_type) this.form.distance_type = p.distance_type;
        if (p.record_time) this.form.record_time = p.record_time;
        if (p.achieved_at) this.form.achieved_at = p.achieved_at;
        this.form.source = 'image';
        this.mode = 'manual';
        this.message = '사진에서 값을 불러왔습니다. 확인 후 PB 등록을 눌러 주세요.';
        this.messageOk = true;
      } catch (err) {
        this.message = err.message || '파싱 실패';
        this.messageOk = false;
      } finally {
        this.parsing = false;
        e.target.value = '';
      }
    },
  };
}
</script>
</x-app-layout>
