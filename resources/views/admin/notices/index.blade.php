<x-admin-layout>

    <div class="space-y-5">

        <div class="flex items-center justify-between">
            <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">공지사항 관리</h1>
            <a href="{{ route('admin.notices.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-600
                      text-pac-black-900 font-body font-bold text-sm rounded-xl transition-colors duration-150 min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                공지 작성
            </a>
        </div>

        @if(session('success'))
            <div class="font-body text-sm text-pac-green-600 bg-pac-green-500/10 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="border-b border-pac-black-100 bg-pac-black-50">
                    <tr>
                        <th class="text-left px-5 py-3 font-body text-xs font-semibold text-pac-black-500 uppercase tracking-wide">제목</th>
                        <th class="text-left px-5 py-3 font-body text-xs font-semibold text-pac-black-500 uppercase tracking-wide hidden md:table-cell">대상</th>
                        <th class="text-left px-5 py-3 font-body text-xs font-semibold text-pac-black-500 uppercase tracking-wide hidden lg:table-cell">작성일</th>
                        <th class="text-right px-5 py-3 font-body text-xs font-semibold text-pac-black-500 uppercase tracking-wide">액션</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pac-black-50">
                    @forelse($notices as $notice)
                        <tr class="hover:bg-pac-black-50 transition-colors duration-150">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    @if($notice->is_pinned)
                                        <span class="font-display text-[10px] font-bold uppercase tracking-widest
                                                     bg-pac-pink-100 text-pac-pink-700 px-2 py-0.5 rounded-full shrink-0">
                                            고정
                                        </span>
                                    @endif
                                    <p class="font-body text-sm font-semibold text-pac-black-900 truncate max-w-xs">
                                        {{ $notice->title }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 hidden md:table-cell">
                                <span class="font-body text-xs text-pac-black-500">
                                    {{ $notice->target_type === 'all' ? '전체' : '지역' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 hidden lg:table-cell">
                                <span class="font-body text-sm text-pac-black-500">
                                    {{ $notice->created_at->format('Y.m.d') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <form method="POST" action="{{ route('admin.notices.destroy', $notice) }}"
                                      onsubmit="return confirm('삭제하시겠습니까?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="font-body text-xs font-semibold text-pac-red-500 hover:text-pac-red-600 transition-colors duration-150">
                                        삭제
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center">
                                <p class="font-body text-sm text-pac-black-300">등록된 공지사항이 없습니다.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($notices->hasPages())
                <div class="px-5 py-4 border-t border-pac-black-100">
                    {{ $notices->links() }}
                </div>
            @endif
        </div>

    </div>

</x-admin-layout>
