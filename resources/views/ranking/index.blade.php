<x-app-layout>
<div class="max-w-3xl mx-auto px-4 py-5 md:px-6 md:py-6 lg:px-8 lg:py-8 space-y-6">

    {{-- 헤더 --}}
    <div>
        <p class="font-display text-[10px] font-bold text-pac-black-400 uppercase tracking-widest mb-1">RANKING</p>
        <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">순위</h1>
    </div>

    {{-- 이번달 개인 순위 --}}
    <section>
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-display text-xs font-bold text-pac-yellow-500 uppercase tracking-widest">
                {{ $year }}년 {{ $month }}월 개인 순위
            </h2>
            <span class="font-body text-xs text-pac-black-400">확정 기록 기준</span>
        </div>

        @if($monthly->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm px-5 py-10 text-center">
                <p class="font-body text-sm text-pac-black-400">이번 달 확정 기록이 없습니다.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                @foreach($monthly as $i => $row)
                    <div class="flex items-center gap-4 px-5 py-3.5 border-b border-pac-black-50 last:border-0
                                {{ auth()->id() == $row->id ? 'bg-pac-yellow-500/8' : '' }}">
                        {{-- 순위 뱃지 --}}
                        <div class="w-8 text-center shrink-0">
                            @if($i === 0)
                                <span class="font-display text-lg font-bold text-yellow-400">1</span>
                            @elseif($i === 1)
                                <span class="font-display text-lg font-bold text-pac-black-400">2</span>
                            @elseif($i === 2)
                                <span class="font-display text-lg font-bold" style="color:#cd7f32">3</span>
                            @else
                                <span class="font-display text-sm font-bold text-pac-black-500">{{ $i + 1 }}</span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="font-body text-sm font-semibold text-pac-black-900 truncate
                                       {{ auth()->id() == $row->id ? 'text-pac-yellow-600' : '' }}">
                                {{ $row->nickname ?? '(이름 없음)' }}
                                @if(auth()->id() == $row->id)
                                    <span class="font-body text-xs text-pac-yellow-500 ml-1">나</span>
                                @endif
                            </p>
                            <p class="font-body text-xs text-pac-black-400">{{ $row->run_count }}회 달림</p>
                        </div>

                        <p class="font-display text-sm font-bold text-pac-black-900 shrink-0">
                            {{ number_format($row->total_km, 1) }}<span class="font-body text-xs font-normal text-pac-black-400 ml-0.5">km</span>
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- 조별 순위 --}}
    @if($groupRanking->isNotEmpty())
    <section>
        <h2 class="font-display text-xs font-bold text-pac-black-400 uppercase tracking-widest mb-3">
            {{ $year }}년 {{ $month }}월 조별 순위
        </h2>
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            @foreach($groupRanking as $i => $row)
                <div class="flex items-center gap-4 px-5 py-3.5 border-b border-pac-black-50 last:border-0">
                    <div class="w-8 text-center shrink-0">
                        <span class="font-display text-sm font-bold text-pac-black-500">{{ $i + 1 }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-body text-sm font-semibold text-pac-black-900 truncate">{{ $row->group_name }}</p>
                        <p class="font-body text-xs text-pac-black-400">{{ $row->member_count }}명 참여</p>
                    </div>
                    <p class="font-display text-sm font-bold text-pac-black-900 shrink-0">
                        {{ number_format($row->total_km, 1) }}<span class="font-body text-xs font-normal text-pac-black-400 ml-0.5">km</span>
                    </p>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- 연간 Top 10 --}}
    <section>
        <h2 class="font-display text-xs font-bold text-pac-black-400 uppercase tracking-widest mb-3">
            {{ $year }}년 연간 Top 10
        </h2>
        @if($yearly->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm px-5 py-10 text-center">
                <p class="font-body text-sm text-pac-black-400">연간 확정 기록이 없습니다.</p>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                @foreach($yearly as $i => $row)
                    <div class="flex items-center gap-4 px-5 py-3.5 border-b border-pac-black-50 last:border-0
                                {{ auth()->id() == $row->id ? 'bg-pac-yellow-500/8' : '' }}">
                        <div class="w-8 text-center shrink-0">
                            <span class="font-display text-sm font-bold text-pac-black-500">{{ $i + 1 }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-body text-sm font-semibold text-pac-black-900 truncate
                                       {{ auth()->id() == $row->id ? 'text-pac-yellow-600' : '' }}">
                                {{ $row->nickname ?? '(이름 없음)' }}
                                @if(auth()->id() == $row->id)
                                    <span class="font-body text-xs text-pac-yellow-500 ml-1">나</span>
                                @endif
                            </p>
                        </div>
                        <p class="font-display text-sm font-bold text-pac-black-900 shrink-0">
                            {{ number_format($row->total_km, 1) }}<span class="font-body text-xs font-normal text-pac-black-400 ml-0.5">km</span>
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</div>
</x-app-layout>
