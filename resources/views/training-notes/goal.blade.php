<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-6 md:px-6 space-y-5"
     x-data="goalForm(@js([
         'editions' => $raceEditions,
         'goal' => $trainingGoal,
         'old' => [
             'race_edition_id' => old('race_edition_id'),
             'race_name' => old('race_name'),
             'race_date' => old('race_date'),
         ],
     ]))">

  <div>
    <a href="{{ route('training-notes.index') }}" class="font-body text-sm text-white/40 hover:text-pac-yellow-400">&larr; 훈련노트</a>
    <p class="font-display text-[11px] tracking-[5px] uppercase text-pac-yellow-500 mt-4 mb-2">RACE TARGET</p>
    <h1 class="font-display text-3xl text-white uppercase tracking-wide">목표 대회</h1>
    <div class="w-12 h-0.5 bg-pac-yellow-500 mt-3"></div>
    <p class="font-body text-sm text-white/50 mt-4 leading-relaxed">
      출전할 대회와 코스(5K·10K·하프·풀)를 정하면, AI가 그 완주를 목표로 주간 스케줄을 짭니다.
    </p>
  </div>

  @if(session('success'))
    <div class="px-4 py-3 bg-pac-green-500/10 border border-pac-green-500/30 text-pac-green-500 text-sm">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('training-notes.goal.store') }}"
        class="bg-pac-black-900 border border-white/[0.05] p-5 space-y-5">
    @csrf

    <div>
      <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">대회 선택</label>
      <select name="race_edition_id" x-model="editionId" @change="pickEdition()"
              class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        <option value="">직접 입력</option>
        @foreach($raceEditions as $ed)
          <option value="{{ $ed['id'] }}"
                  @selected(old('race_edition_id', $trainingGoal['race_edition_id'] ?? '') == $ed['id'])>
            {{ $ed['race_name'] }}
            @if($ed['race_date'])
              ({{ \Carbon\Carbon::parse($ed['race_date'])->format('Y.m.d') }})
            @elseif($ed['year'])
              ({{ $ed['year'] }})
            @endif
          </option>
        @endforeach
      </select>
      @if(count($raceEditions) === 0)
        <p class="font-body text-xs text-white/35 mt-2">대회 목록을 불러오지 못했습니다. 아래에 직접 입력해 주세요.</p>
      @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div class="sm:col-span-2">
        <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">대회명 *</label>
        <input type="text" name="race_name" required maxlength="120"
               x-model="raceName"
               placeholder="예: 서울마라톤"
               class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        @error('race_name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">코스 *</label>
        <select name="course_type" required
                class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
          @foreach($courseTypes as $val => $label)
            <option value="{{ $val }}" @selected(old('course_type', $trainingGoal['course_type'] ?? '') === $val)>{{ $label }}</option>
          @endforeach
        </select>
        @error('course_type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div>
        <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">대회일 *</label>
        <input type="date" name="race_date" required x-model="raceDate"
               class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        @error('race_date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
      <div class="sm:col-span-2">
        <label class="font-display text-[10px] text-white/50 uppercase tracking-widest">목표 기록 (선택)</label>
        <input type="text" name="goal_time"
               value="{{ old('goal_time', $trainingGoal['goal_time'] ?? '') }}"
               placeholder="풀: 3:30:00 · 10K: 50:00"
               class="mt-1 w-full bg-pac-black-800 border border-white/10 text-white text-sm px-3 py-2">
        <p class="font-body text-xs text-white/35 mt-1">MM:SS 또는 H:MM:SS. 비워두면 PB·현재 체력 기준으로 코치가 제안합니다.</p>
        @error('goal_time')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    <button type="submit"
            class="px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-400 text-pac-black-900 font-display text-sm font-black uppercase tracking-wider">
      목표 저장
    </button>
  </form>
</div>

<script>
function goalForm(config) {
  const editions = config.editions || [];
  const goal = config.goal || {};
  const old = config.old || {};
  const pick = (key, fromGoal) => old[key] ?? fromGoal ?? '';
  return {
    editions,
    editionId: pick('race_edition_id', goal.race_edition_id ? String(goal.race_edition_id) : ''),
    raceName: pick('race_name', goal.race_name || ''),
    raceDate: pick('race_date', goal.race_date || ''),
    pickEdition() {
      if (!this.editionId) return;
      const ed = this.editions.find(e => String(e.id) === String(this.editionId));
      if (!ed) return;
      this.raceName = ed.race_name;
      if (ed.race_date) this.raceDate = ed.race_date;
    },
  };
}
</script>
</x-app-layout>
