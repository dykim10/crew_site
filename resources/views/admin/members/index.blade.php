<x-admin-layout>

    <div class="space-y-5">

        <div class="flex items-center justify-between">
            <h1 class="font-display text-2xl font-bold text-pac-black-900 uppercase tracking-tight">구성원 목록</h1>
        </div>

        {{-- 검색/필터 --}}
        <form method="GET" action="{{ route('admin.members.index') }}"
              class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="닉네임 검색..."
                   class="flex-1 px-4 py-2.5 rounded-xl border border-pac-black-200 font-body text-sm
                          focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent">
            <select name="role"
                    class="px-4 py-2.5 rounded-xl border border-pac-black-200 font-body text-sm
                           focus:ring-2 focus:ring-pac-yellow-400 focus:border-transparent">
                <option value="">전체 권한</option>
                <option value="super_admin"  {{ $role === 'super_admin'  ? 'selected' : '' }}>슈퍼관리자</option>
                <option value="region_admin" {{ $role === 'region_admin' ? 'selected' : '' }}>지역관리자</option>
                <option value="operator"     {{ $role === 'operator'     ? 'selected' : '' }}>운영자</option>
                <option value="member"       {{ $role === 'member'       ? 'selected' : '' }}>일반멤버</option>
            </select>
            <button type="submit"
                    class="px-5 py-2.5 bg-pac-yellow-500 hover:bg-pac-yellow-600 text-pac-black-900
                           font-body font-bold text-sm rounded-xl transition-colors duration-150">
                검색
            </button>
        </form>

        {{-- 테이블 --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="border-b border-pac-black-100 bg-pac-black-50">
                    <tr>
                        <th class="text-left px-5 py-3 font-body text-xs font-semibold text-pac-black-500 uppercase tracking-wide">닉네임</th>
                        <th class="text-left px-5 py-3 font-body text-xs font-semibold text-pac-black-500 uppercase tracking-wide hidden md:table-cell">권한</th>
                        <th class="text-left px-5 py-3 font-body text-xs font-semibold text-pac-black-500 uppercase tracking-wide hidden lg:table-cell">가입일</th>
                        <th class="text-right px-5 py-3 font-body text-xs font-semibold text-pac-black-500 uppercase tracking-wide">액션</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-pac-black-50">
                    @forelse($members as $member)
                        <tr class="hover:bg-pac-black-50 transition-colors duration-150">
                            <td class="px-5 py-3.5">
                                <p class="font-body text-sm font-semibold text-pac-black-900">
                                    {{ $member->nickname ?? '(닉네임 없음)' }}
                                </p>
                                <p class="font-body text-xs text-pac-black-400 mt-0.5 md:hidden">
                                    {{ match($member->role) {
                                        'super_admin'  => '슈퍼관리자',
                                        'region_admin' => '지역관리자',
                                        'operator'     => '운영자',
                                        default        => '일반멤버',
                                    } }}
                                </p>
                            </td>
                            <td class="px-5 py-3.5 hidden md:table-cell">
                                <span class="inline-block font-display text-[10px] font-bold uppercase tracking-widest
                                             px-2.5 py-1 rounded-full
                                             {{ match($member->role) {
                                                 'super_admin'  => 'bg-pac-pink-100 text-pac-pink-700',
                                                 'region_admin' => 'bg-pac-yellow-100 text-pac-yellow-700',
                                                 'operator'     => 'bg-pac-black-100 text-pac-black-600',
                                                 default        => 'bg-pac-black-50 text-pac-black-400',
                                             } }}">
                                    {{ match($member->role) {
                                        'super_admin'  => 'Super',
                                        'region_admin' => 'Region',
                                        'operator'     => 'Operator',
                                        default        => 'Member',
                                    } }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 hidden lg:table-cell">
                                <p class="font-body text-sm text-pac-black-500">
                                    {{ \Carbon\Carbon::parse($member->created_at)->format('Y.m.d') }}
                                </p>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="#"
                                   class="font-body text-xs font-semibold text-pac-yellow-600 hover:text-pac-yellow-700 transition-colors duration-150">
                                    상세
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center">
                                <p class="font-body text-sm text-pac-black-300">구성원이 없습니다.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($members->hasPages())
                <div class="px-5 py-4 border-t border-pac-black-100">
                    {{ $members->links() }}
                </div>
            @endif
        </div>

    </div>

</x-admin-layout>
