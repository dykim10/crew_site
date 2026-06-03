<x-app-layout>
<div class="max-w-6xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-5"
     x-data="eventBoard()">

  {{-- ══════════════════════════════════════════════════
       헤더 영역
       ══════════════════════════════════════════════════ --}}
  <div class="flex items-start justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <span class="font-display text-[10px] font-bold text-pac-pink-500 uppercase tracking-widest">A타입 · 기수 경쟁</span>
        <span class="font-display text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded
                     {{ $event->isActive() ? 'bg-pac-pink-500 text-white' : 'bg-pac-black-100 text-pac-black-500' }}">
          {{ $event->isActive() ? 'LIVE' : ($event->status === 'upcoming' ? '예정' : '종료') }}
        </span>
      </div>
      <h1 class="font-display text-3xl lg:text-4xl font-black text-pac-black-900 uppercase tracking-tight leading-tight mb-1">
        {{ $event->name }} 현황
      </h1>
      <p class="font-body text-sm text-pac-black-400">
        {{ $event->start_date->format('Y.m.d') }} ~ {{ $event->end_date->format('Y.m.d') }}
      </p>
    </div>
    <a href="{{ route('events.show', $event) }}"
       class="shrink-0 font-display text-sm font-bold text-pac-yellow-500 hover:text-pac-yellow-400 transition-colors uppercase tracking-wider">
      ← 이벤트 소개
    </a>
  </div>

  {{-- ══════════════════════════════════════════════════
       내 현황 카드 (조건부)
       ══════════════════════════════════════════════════ --}}
  @if($myGroup)
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border-l-4 border-l-pac-yellow-500">
      <div class="px-6 py-5 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-display text-xs font-bold text-pac-black-500 uppercase tracking-widest mb-1">내 조</p>
            <h3 class="font-display text-2xl font-black text-pac-black-900 uppercase tracking-tight">
              {{ $myGroup->group_name }}
            </h3>
          </div>
          <div class="text-right">
            <p class="font-display text-xs font-bold text-pac-black-500 uppercase tracking-widest mb-1">조 순위</p>
            <p class="font-display text-3xl font-black text-pac-pink-500 leading-none">
              {{ $myGroup->rank ?? '—' }}위
            </p>
          </div>
        </div>

        {{-- 3개 수치 카드 --}}
        <div class="grid grid-cols-3 gap-3">
          <div class="bg-pac-black-50 rounded-lg px-4 py-3 text-center">
            <p class="font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-wider mb-1">내 인증km</p>
            <p class="font-display text-2xl font-black text-pac-black-900 leading-none">
              {{ number_format($myGroup->my_confirmed_km ?? 0, 1) }}
              <span class="font-body text-xs font-normal text-pac-black-500">km</span>
            </p>
          </div>
          <div class="bg-pac-black-50 rounded-lg px-4 py-3 text-center">
            <p class="font-display text-[9px] font-bold text-pac-black-500 uppercase tracking-wider mb-1">조 인증km</p>
            <p class="font-display text-2xl font-black text-pac-black-900 leading-none">
              {{ number_format($myGroup->total_km ?? 0, 1) }}
              <span class="font-body text-xs font-normal text-pac-black-500">km</span>
            </p>
          </div>
          <div class="bg-pac-yellow-50 border border-pac-yellow-100 rounded-lg px-4 py-3 text-center">
            <p class="font-display text-[9px] font-bold text-pac-yellow-700 uppercase tracking-wider mb-1">달성률</p>
            <p class="font-display text-2xl font-black text-pac-yellow-600 leading-none">
              {{ $myGroup->progress_percent ?? 0 }}%
            </p>
          </div>
        </div>

        {{-- 프로그레스 바 --}}
        <div>
          <div class="flex justify-between items-baseline mb-1.5">
            <span class="font-body text-xs text-pac-black-500">이벤트 기간 달성률</span>
            <span class="font-display text-xs font-bold text-pac-black-700">
              {{ number_format($myGroup->total_km ?? 0, 1) }} / {{ number_format($myGroup->target_km ?? 0, 0) }} km
            </span>
          </div>
          <div class="h-3 bg-pac-black-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500"
                 style="width: {{ $myGroup->progress_percent ?? 0 }}%;
                        background: linear-gradient(90deg, #E5AD16, #E80043);">
            </div>
          </div>
        </div>

        <p class="font-body text-xs text-pac-black-400">
          · 확정된 러닝 기록만 집계됩니다 (관리자 검수 완료 기준)<br>
          · 이벤트 기간: {{ $event->start_date->format('Y.m.d') }} ~ {{ $event->end_date->format('Y.m.d') }}
        </p>
      </div>
    </div>
  @else
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden p-6 text-center py-10">
      <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-pac-black-100 mb-3">
        <svg class="w-6 h-6 text-pac-black-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
      </div>
      <p class="font-display text-base font-bold text-pac-black-700 uppercase tracking-widest">조에 미배정</p>
      <p class="font-body text-sm text-pac-black-400 mt-1">관리자에게 조 배정을 요청해 주세요.</p>
    </div>
  @endif

  {{-- ══════════════════════════════════════════════════
       지부 탭
       ══════════════════════════════════════════════════ --}}
  @if($branches && count($branches) > 0)
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="border-b border-pac-black-100">
        <div class="flex items-center gap-1 overflow-x-auto">
          @foreach($branches as $branch)
            <button type="button"
                    @click="selectBranch({{ $branch->id }})"
                    :class="currentBranchId === {{ $branch->id }}
                      ? 'border-b-2 border-b-pac-yellow-500 text-pac-yellow-600 bg-pac-black-50'
                      : 'border-b-2 border-b-transparent text-pac-black-600 hover:text-pac-black-900 hover:bg-white/50'"
                    class="flex-1 px-4 py-3.5 font-display font-bold text-xs uppercase tracking-wider transition-colors whitespace-nowrap">
              {{ $branch->name }}
            </button>
          @endforeach
        </div>
      </div>

      {{-- 로딩 상태 --}}
      <template x-if="loading">
        <div class="px-6 py-8 text-center">
          <div class="inline-flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-pac-yellow-500 animate-pulse"></div>
            <span class="font-body text-sm text-pac-black-400">로딩 중...</span>
          </div>
        </div>
      </template>

      {{-- 조별 순위 테이블 --}}
      <template x-if="!loading && branchGroups[currentBranchId]">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-pac-black-900 border-b border-pac-black-800">
              <tr>
                <th class="text-left px-4 py-3 font-display text-xs font-bold text-pac-black-400 uppercase tracking-wider">순위</th>
                <th class="text-left px-4 py-3 font-display text-xs font-bold text-pac-black-400 uppercase tracking-wider">조명</th>
                <th class="text-center px-4 py-3 font-display text-xs font-bold text-pac-black-400 uppercase tracking-wider">구성원</th>
                <th class="text-right px-4 py-3 font-display text-xs font-bold text-pac-black-400 uppercase tracking-wider">인증km</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-pac-black-50">
              <template x-for="(group, idx) in branchGroups[currentBranchId]" :key="idx">
                <tr :class="currentGroupId === group.group_id
                  ? 'bg-pac-yellow-500/8 font-bold'
                  : 'hover:bg-pac-black-50 transition-colors'"
                    @click="selectGroup(group.group_id)"
                    class="cursor-pointer">
                  <td class="px-4 py-4 font-display text-sm font-bold text-pac-pink-500">
                    <span x-text="group.rank"></span>위
                  </td>
                  <td class="px-4 py-4 font-body text-sm"
                      :class="currentGroupId === group.group_id
                        ? 'text-pac-yellow-600 font-semibold'
                        : 'text-pac-black-800'">
                    <span x-text="group.group_name"></span>
                    <template x-if="currentGroupId === group.group_id">
                      <span class="font-display text-[9px] font-bold bg-pac-yellow-500 text-pac-black-900 px-1.5 py-0.5 rounded uppercase tracking-wide ml-1">내 조</span>
                    </template>
                  </td>
                  <td class="px-4 py-4 font-body text-sm text-center text-pac-black-600">
                    <span x-text="group.member_count"></span>명
                  </td>
                  <td class="px-4 py-4 font-display text-sm font-bold text-right"
                      :class="currentGroupId === group.group_id
                        ? 'text-pac-yellow-600'
                        : 'text-pac-black-800'">
                    <span x-text="group.total_km.toFixed(1)"></span> km
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </template>

      {{-- 데이터 없음 --}}
      <template x-if="!loading && (!branchGroups[currentBranchId] || branchGroups[currentBranchId].length === 0)">
        <div class="px-6 py-10 text-center">
          <p class="font-display text-xl font-black text-pac-black-700 uppercase tracking-wider">조 정보 없음</p>
          <p class="font-body text-sm text-pac-black-400 mt-1">이 지부에 등록된 조가 없습니다.</p>
        </div>
      </template>
    </div>
  @endif

  {{-- ══════════════════════════════════════════════════
       조원 상세 기록 (선택된 조)
       ══════════════════════════════════════════════════ --}}
  @if($myGroup)
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="px-5 py-3.5 bg-pac-black-50 border-b border-pac-black-100 flex items-center justify-between">
        <h3 class="font-display text-xs font-bold text-pac-black-600 uppercase tracking-widest">
          조원 기록 (<span x-text="selectedGroupMemberCount">—</span>명)
        </h3>
      </div>

      {{-- 로딩 --}}
      <template x-if="loading">
        <div class="px-6 py-8 text-center">
          <div class="inline-flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-pac-yellow-500 animate-pulse"></div>
            <span class="font-body text-sm text-pac-black-400">로딩 중...</span>
          </div>
        </div>
      </template>

      {{-- 테이블 --}}
      <template x-if="!loading && memberLogs.length > 0">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-pac-black-50 border-b border-pac-black-100">
              <tr>
                <th class="text-left px-4 py-3 font-display text-xs font-bold text-pac-black-600 uppercase tracking-wider">이름</th>
                <th class="text-right px-4 py-3 font-display text-xs font-bold text-pac-black-600 uppercase tracking-wider">인증km</th>
                <th class="text-right px-4 py-3 font-display text-xs font-bold text-pac-black-600 uppercase tracking-wider">대기km</th>
                <th class="text-center px-4 py-3 font-display text-xs font-bold text-pac-black-600 uppercase tracking-wider">기록 수</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-pac-black-100">
              <template x-for="(member, idx) in memberLogs" :key="idx">
                <tr class="hover:bg-pac-black-50 transition-colors">
                  <td class="px-4 py-3.5 font-body text-sm font-medium text-pac-black-800">
                    <span x-text="member.name"></span>
                  </td>
                  <td class="px-4 py-3.5 font-display text-sm font-bold text-right text-pac-yellow-600">
                    <span x-text="member.confirmed_km.toFixed(1)"></span>
                  </td>
                  <td class="px-4 py-3.5 font-display text-sm font-bold text-right text-pac-pink-500">
                    <span x-text="member.pending_km.toFixed(1)"></span>
                  </td>
                  <td class="px-4 py-3.5 font-body text-sm text-center text-pac-black-600">
                    <span x-text="member.record_count"></span>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </template>

      {{-- 데이터 없음 --}}
      <template x-if="!loading && memberLogs.length === 0">
        <div class="px-6 py-10 text-center">
          <p class="font-display text-lg font-black text-pac-black-700 uppercase tracking-wider">조원 기록 없음</p>
          <p class="font-body text-sm text-pac-black-400 mt-1">아직 인증된 기록이 없습니다.</p>
        </div>
      </template>
    </div>
  @endif

  {{-- ══════════════════════════════════════════════════
       일별 달성 추이 (순수 CSS 바 차트, 내 조)
       ══════════════════════════════════════════════════ --}}
  @if($myGroup && $dailyStats && count($dailyStats) > 0)
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="px-5 py-3.5 bg-pac-black-50 border-b border-pac-black-100">
        <h3 class="font-display text-xs font-bold text-pac-black-600 uppercase tracking-widest">
          일별 인증 추이 ({{ $myGroup->group_name }})
        </h3>
      </div>

      <div class="p-6">
        {{-- 최대값 계산 (Blade에서) --}}
        @php
          $maxDailyKm = max(array_column($dailyStats, 'km'));
          $maxDailyKm = max($maxDailyKm, 5); // 최소 5km 스케일
        @endphp

        <div class="grid grid-cols-7 gap-2 md:gap-3">
          @foreach($dailyStats as $stat)
            @php
              $percentage = ($stat['km'] / $maxDailyKm) * 100;
            @endphp
            <div class="flex flex-col items-center gap-2">
              {{-- 바 --}}
              <div class="w-full flex items-end justify-center h-32 bg-pac-black-50 rounded-t-sm">
                <div class="w-2/3 rounded-t-sm transition-all duration-300"
                     style="height: {{ $percentage }}%;
                            background: linear-gradient(180deg, #E5AD16, #E80043);"
                     title="{{ $stat['km'] }} km">
                </div>
              </div>
              {{-- 레이블 --}}
              <div class="text-center w-full">
                <p class="font-display text-[11px] font-bold text-pac-black-900">
                  {{ number_format($stat['km'], 1) }}
                </p>
                <p class="font-body text-[9px] text-pac-black-400">
                  {{ \Carbon\Carbon::parse($stat['date'])->format('m/d') }}
                </p>
              </div>
            </div>
          @endforeach
        </div>

        {{-- 주석 --}}
        <p class="font-body text-xs text-pac-black-400 mt-4">
          · 각 막대는 해당 날짜의 조 인증km 합계입니다<br>
          · 확정된 기록만 포함됩니다
        </p>
      </div>
    </div>
  @endif

  {{-- ══════════════════════════════════════════════════
       뒤로
       ══════════════════════════════════════════════════ --}}
  <div class="pt-2">
    <a href="{{ route('events.index') }}"
       class="font-display text-xs font-bold text-pac-black-400 uppercase tracking-widest hover:text-pac-black-700 transition-colors">
      ← 이벤트 목록
    </a>
  </div>

</div>

<script>
function eventBoard() {
  return {
    currentBranchId: {{ $myBranchId ?? ($branches->first()->id ?? 0) }},
    currentGroupId: {{ $myGroup?->id ?? 0 }},
    loading: false,
    branchGroups: @json($branchGroups),
    memberLogs: [],
    selectedGroupMemberCount: 0,

    async selectBranch(branchId) {
      this.currentBranchId = branchId;
      // 초기 데이터는 Blade에서 전달됨
      // 필요시 AJAX로 동적 로드 가능
    },

    async selectGroup(groupId) {
      if (this.currentGroupId === groupId && this.memberLogs.length > 0) {
        return; // 이미 로드됨
      }

      this.currentGroupId = groupId;
      this.loading = true;

      try {
        const response = await fetch(
          `/events/{{ $event->id }}/board/member-logs?group_id=${groupId}`,
          {
            headers: { 'Accept': 'application/json' }
          }
        );

        if (!response.ok) {
          throw new Error('Failed to load member logs');
        }

        const data = await response.json();
        this.memberLogs = data.members || [];
        this.selectedGroupMemberCount = this.memberLogs.length;
      } catch (error) {
        console.error('Error loading member logs:', error);
        this.memberLogs = [];
      } finally {
        this.loading = false;
      }
    },

    init() {
      // 초기 로드: 내 조 기록 또는 첫 번째 그룹
      if (this.currentGroupId > 0) {
        this.selectGroup(this.currentGroupId);
      } else if (this.branchGroups[this.currentBranchId]?.length > 0) {
        this.selectGroup(this.branchGroups[this.currentBranchId][0].group_id);
      }
    }
  };
}
</script>

</x-app-layout>
