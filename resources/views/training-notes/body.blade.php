<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-6 md:px-6 space-y-5"
     x-data="bodyManager(@js([
         'hasConsent' => $hasConsent,
         'parseUrl' => route('training-notes.body.parse'),
         'storeUrl' => route('training-notes.body.store'),
         'csrf' => csrf_token(),
     ]))">

  <div>
    <a href="{{ route('training-notes.index') }}" class="font-body text-sm text-white/40 hover:text-pac-yellow-400">&larr; 훈련노트</a>
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mt-4 mb-2">BODY COMPOSITION</p>
    <h1 class="font-display text-3xl text-white uppercase tracking-wide">체성분</h1>
    <div class="w-12 h-0.5 bg-pac-yellow-500 mt-3"></div>
  </div>

  @if(session('success'))
    <div class="px-4 py-3 bg-pac-green-500/10 border border-pac-green-500/30 text-pac-green-500 text-sm">{{ session('success') }}</div>
  @endif

  @unless($hasConsent)
    {{-- 동의 모달 --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80">
      <div class="w-full max-w-md bg-pac-black-900 border border-white/10 p-6 space-y-4">
        <h2 class="font-display text-lg text-white uppercase">민감정보 수집 동의</h2>
        <p class="font-body text-sm text-white/70">
          체성분(체중·인바디) 데이터는 AI 코칭 목적으로 수집·이용됩니다.
          데이터는 암호화되어 저장되며, 코칭 품질 향상 외 용도로 사용되지 않습니다.
        </p>
        <form method="POST" action="{{ route('training-notes.body.consent') }}">
          @csrf
          <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="consent" value="1" required class="mt-1">
            <span class="font-body text-sm text-white/80">위 내용에 동의합니다.</span>
          </label>
          <button type="submit"
                  class="mt-4 w-full py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900 font-display text-sm font-black uppercase">
            동의하고 계속
          </button>
        </form>
      </div>
    </div>
  @endunless

  @if($hasConsent)
    {{-- 입력 탭 --}}
    <div class="flex gap-2">
      <button type="button" @click="mode = 'manual'"
              :class="mode === 'manual' ? 'bg-pac-yellow-500 text-pac-black-900' : 'bg-white/5 text-white/60'"
              class="px-4 py-2 font-display text-xs uppercase tracking-wider">직접 입력</button>
      <button type="button" @click="mode = 'image'"
              :class="mode === 'image' ? 'bg-pac-yellow-500 text-pac-black-900' : 'bg-white/5 text-white/60'"
              class="px-4 py-2 font-display text-xs uppercase tracking-wider">인바디 사진</button>
    </div>

    {{-- 직접 입력 --}}
    <form x-show="mode === 'manual'" @submit.prevent="saveManual()"
          class="bg-pac-black-900 border border-white/[0.05] p-5 space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="font-display text-[10px] text-white/50 uppercase">측정일시</label>
          <input type="datetime-local" x-model="form.measured_at" required
                 class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        </div>
        <div>
          <label class="font-display text-[10px] text-white/50 uppercase">체중 (kg) *</label>
          <input type="number" step="0.1" x-model="form.weight_kg" required min="30" max="150"
                 class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        </div>
        <div>
          <label class="font-display text-[10px] text-white/50 uppercase">골격근량 (kg)</label>
          <input type="number" step="0.1" x-model="form.skeletal_muscle_kg"
                 class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        </div>
        <div>
          <label class="font-display text-[10px] text-white/50 uppercase">체지방률 (%)</label>
          <input type="number" step="0.1" x-model="form.body_fat_percent"
                 class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        </div>
      </div>
      <button type="submit" :disabled="saving"
              class="px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400 disabled:opacity-50 text-pac-black-900 font-display text-sm font-black uppercase">
        저장
      </button>
    </form>

    {{-- 사진 업로드 --}}
    <div x-show="mode === 'image'" class="bg-pac-black-900 border border-white/[0.05] p-5 space-y-4">
      <input type="file" accept="image/*" @change="uploadImage($event)" class="text-white/60 text-sm">
      <template x-if="preview">
        <div class="space-y-3 border border-white/10 p-4">
          <p class="font-display text-xs text-pac-yellow-400 uppercase">파싱 결과 — 확인 후 수정</p>
          <div class="grid grid-cols-2 gap-3 text-sm">
            <label class="text-white/50">체중<input type="number" step="0.1" x-model="preview.weight_kg" class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white px-2 py-1"></label>
            <label class="text-white/50">골격근<input type="number" step="0.1" x-model="preview.skeletal_muscle_kg" class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white px-2 py-1"></label>
            <label class="text-white/50">체지방률<input type="number" step="0.1" x-model="preview.body_fat_percent" class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white px-2 py-1"></label>
            <label class="text-white/50">BMI<input type="number" step="0.1" x-model="preview.bmi" class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white px-2 py-1"></label>
          </div>
          <button type="button" @click="savePreview()" :disabled="saving"
                  class="px-5 py-2 bg-pac-yellow-500 text-pac-black-900 font-display text-xs font-black uppercase">
            확인 저장
          </button>
        </div>
      </template>
    </div>

    <p x-show="message" x-text="message" class="text-sm" :class="messageOk ? 'text-pac-green-500' : 'text-red-400'"></p>

    {{-- 이력 --}}
    @if(count($records) > 0)
      <div class="bg-pac-black-900 border border-white/[0.05] overflow-hidden">
        <div class="px-5 py-3 border-b border-white/[0.06] font-display text-xs text-white uppercase tracking-widest">최근 이력</div>
        @foreach($records as $rec)
          <div class="flex items-center justify-between px-5 py-3 border-b border-white/[0.04] last:border-0 text-sm">
            <span class="text-white/50">{{ \Carbon\Carbon::parse($rec['measured_at'])->format('Y.m.d H:i') }}</span>
            <span class="text-white">{{ $rec['weight_kg'] ?? '—' }}kg</span>
            <span class="text-white/60">체지방 {{ $rec['body_fat_percent'] ?? '—' }}%</span>
          </div>
        @endforeach
      </div>
    @endif
  @endif
</div>

<script>
function bodyManager(config) {
  const now = new Date();
  const pad = n => String(n).padStart(2, '0');
  const defaultDt = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;

  return {
    ...config,
    mode: 'manual',
    saving: false,
    preview: null,
    message: '',
    messageOk: false,
    form: {
      measured_at: defaultDt,
      weight_kg: '',
      skeletal_muscle_kg: '',
      body_fat_percent: '',
      source: 'manual',
    },

    async saveManual() {
      await this._store({
        measured_at: this._toIso(this.form.measured_at),
        weight_kg: parseFloat(this.form.weight_kg),
        skeletal_muscle_kg: this.form.skeletal_muscle_kg ? parseFloat(this.form.skeletal_muscle_kg) : null,
        body_fat_percent: this.form.body_fat_percent ? parseFloat(this.form.body_fat_percent) : null,
        source: 'manual',
      });
    },

    async uploadImage(e) {
      const file = e.target.files[0];
      if (!file) return;
      const fd = new FormData();
      fd.append('image', file);
      try {
        const res = await fetch(this.parseUrl, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
          body: fd,
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message);
        this.preview = data.data;
      } catch (err) {
        this.message = err.message || '파싱 실패';
        this.messageOk = false;
      }
    },

    async savePreview() {
      await this._store({
        measured_at: this.preview.measured_at,
        weight_kg: parseFloat(this.preview.weight_kg),
        skeletal_muscle_kg: this.preview.skeletal_muscle_kg,
        body_fat_kg: this.preview.body_fat_kg,
        bmi: this.preview.bmi,
        body_fat_percent: this.preview.body_fat_percent,
        inbody_score: this.preview.inbody_score,
        source: 'image',
      });
      this.preview = null;
    },

    async _store(payload) {
      this.saving = true;
      this.message = '';
      try {
        const res = await fetch(this.storeUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
          body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message);
        this.message = '저장되었습니다. 페이지를 새로고침하면 이력에 반영됩니다.';
        this.messageOk = true;
      } catch (err) {
        this.message = err.message || '저장 실패';
        this.messageOk = false;
      } finally {
        this.saving = false;
      }
    },

    _toIso(local) {
      if (!local) return new Date().toISOString();
      return new Date(local).toISOString();
    },
  };
}
</script>
</x-app-layout>
