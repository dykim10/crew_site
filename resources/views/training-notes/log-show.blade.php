<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-6 md:px-6 space-y-5"
     x-data="logFeedback(@js([
         'hasFeedback' => (bool) $log->feedback_at,
         'feedback' => $log->coach_feedback,
         'feedbackUrl' => route('training-notes.logs.feedback', $log),
         'csrf' => csrf_token(),
     ]))">

  <div>
    <a href="{{ route('training-notes.index') }}" class="font-body text-sm text-white/40 hover:text-pac-yellow-400">&larr; 훈련노트</a>
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mt-4 mb-2">RUN LOG</p>
    <h1 class="font-display text-3xl text-white uppercase tracking-wide">{{ $log->run_date->format('Y.m.d') }}</h1>
    <div class="w-12 h-0.5 bg-pac-yellow-500 mt-3"></div>
  </div>

  @if(session('success'))
    <div class="px-4 py-3 bg-pac-green-500/10 border border-pac-green-500/30 text-pac-green-500 text-sm">{{ session('success') }}</div>
  @endif

  {{-- 기록 요약 --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <div class="bg-pac-black-900 border border-white/[0.05] px-4 py-3">
      <p class="font-display text-[9px] text-pac-black-500 uppercase tracking-widest mb-1">거리</p>
      <p class="font-display text-2xl text-pac-yellow-400">{{ number_format($log->distance_km, 1) }}<span class="text-sm text-pac-black-500">km</span></p>
    </div>
    <div class="bg-pac-black-900 border border-white/[0.05] px-4 py-3">
      <p class="font-display text-[9px] text-pac-black-500 uppercase tracking-widest mb-1">시간</p>
      <p class="font-display text-2xl text-white">{{ $log->duration_formatted ?? '—' }}</p>
    </div>
    <div class="bg-pac-black-900 border border-white/[0.05] px-4 py-3">
      <p class="font-display text-[9px] text-pac-black-500 uppercase tracking-widest mb-1">페이스</p>
      <p class="font-display text-2xl text-white">{{ $log->avg_pace_formatted ?? '—' }}</p>
    </div>
    <div class="bg-pac-black-900 border border-white/[0.05] px-4 py-3">
      <p class="font-display text-[9px] text-pac-black-500 uppercase tracking-widest mb-1">VO2max</p>
      <p class="font-display text-2xl text-white">{{ $log->vo2max ?? '—' }}</p>
    </div>
  </div>

  {{-- 메모 --}}
  <form method="POST" action="{{ route('training-notes.logs.note', $log) }}" class="space-y-3">
    @csrf
    <label class="font-display text-xs text-white/50 uppercase tracking-widest">훈련 메모</label>
    <textarea name="user_note" rows="4"
              class="w-full bg-pac-black-900 border border-white/10 text-white font-body text-sm px-4 py-3 focus:border-pac-yellow-500/50 focus:outline-none"
              placeholder="컨디션, 날씨, 느낌 등">{{ old('user_note', $log->user_note) }}</textarea>
    <button type="submit"
            class="px-5 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-display text-xs uppercase tracking-wider">
      메모 저장
    </button>
  </form>

  {{-- AI 피드백 --}}
  <div class="bg-pac-black-900 border border-white/[0.05] p-5 space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="font-display text-sm text-white uppercase tracking-widest">AI 코치 피드백</h2>
      <template x-if="!hasFeedback">
        <button type="button" @click="requestFeedback()" :disabled="loading"
                class="px-4 py-2 bg-pac-yellow-500 hover:bg-pac-yellow-400 disabled:opacity-50
                       text-pac-black-900 font-display text-xs font-black uppercase tracking-wider">
          <span x-show="!loading">피드백 받기</span>
          <span x-show="loading">생성 중...</span>
        </button>
      </template>
    </div>

    <template x-if="feedback">
      <div class="space-y-3 font-body text-sm text-white/80">
        <p><strong class="text-pac-yellow-400">총평</strong><br><span x-text="feedback.summary"></span></p>
        <p><strong class="text-pac-yellow-400">강도</strong><br><span x-text="feedback.intensity_check"></span></p>
        <template x-if="feedback.advice && feedback.advice.length">
          <div>
            <strong class="text-pac-yellow-400">조언</strong>
            <ul class="list-disc list-inside mt-1 space-y-1">
              <template x-for="(a, i) in feedback.advice" :key="i">
                <li x-text="a"></li>
              </template>
            </ul>
          </div>
        </template>
        <p><strong class="text-pac-yellow-400">다음 러닝</strong><br><span x-text="feedback.next_run"></span></p>
      </div>
    </template>

    <p x-show="error" x-text="error" class="text-red-400 text-sm"></p>
  </div>
</div>

<script>
function logFeedback(config) {
  return {
    ...config,
    loading: false,
    error: '',

    async requestFeedback() {
      this.loading = true;
      this.error = '';
      try {
        const res = await fetch(this.feedbackUrl, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.message || '실패');
        this.feedback = data.feedback;
        this.hasFeedback = true;
      } catch (e) {
        this.error = e.message || '피드백 생성에 실패했습니다.';
      } finally {
        this.loading = false;
      }
    },
  };
}
</script>
</x-app-layout>
