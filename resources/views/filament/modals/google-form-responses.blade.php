<div class="space-y-4 py-2">

    @if($error)
        {{-- 오류 --}}
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-700">Google Sheets 연결 오류</p>
                <p class="mt-0.5 text-sm text-red-600">{{ $error }}</p>
            </div>
        </div>

    @elseif(empty($headers))
        {{-- 데이터 없음 --}}
        <div class="py-12 text-center text-sm text-gray-400">
            응답 데이터가 없습니다.
        </div>

    @else
        {{-- 총 건수 --}}
        <p class="text-sm text-gray-500">총 <span class="font-semibold text-gray-800">{{ $count }}건</span></p>

        {{-- 응답 테이블 --}}
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-900 text-white">
                    <tr>
                        @foreach($headers as $header)
                            <th class="whitespace-nowrap px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wider">
                                {{ $header }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($rows as $row)
                        <tr class="hover:bg-gray-50">
                            @foreach($headers as $i => $header)
                                <td class="whitespace-nowrap px-4 py-2.5 text-gray-700">
                                    {{ $row[$i] ?? '' }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
