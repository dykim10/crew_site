<x-filament-panels::page>
    <div class="adm-stack">
        {{ $this->form }}

        <x-filament::section heading="등록된 발신번호 현황">
            @if ($senders === [])
                <p class="text-sm text-gray-500">등록된 발신번호가 없습니다.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="fi-ta-table w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left text-gray-600">
                                <th class="px-3 py-2 font-medium">번호</th>
                                <th class="px-3 py-2 font-medium">상태</th>
                                <th class="px-3 py-2 font-medium text-right">작업</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($senders as $sender)
                                @php
                                    $status = $sender['status'] ?? '-';
                                    $isActive = $status === 'ACTIVE';
                                @endphp
                                <tr class="border-b border-gray-100" wire:key="sender-{{ $sender['phoneNumber'] }}">
                                    <td class="px-3 py-3 font-mono">{{ $sender['phoneNumber'] }}</td>
                                    <td class="px-3 py-3">
                                        <span @class([
                                            'inline-flex rounded-full px-2 py-0.5 text-xs font-medium',
                                            'bg-emerald-100 text-emerald-800' => $isActive,
                                            'bg-amber-100 text-amber-800' => !$isActive,
                                        ])>
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-3 text-right">
                                        <button
                                            type="button"
                                            class="fi-btn fi-btn-size-sm fi-color-danger inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium"
                                            wire:click="deleteSender('{{ $sender['phoneNumber'] }}')"
                                            wire:confirm="이 번호로 더 이상 문자를 발송할 수 없습니다. 삭제하시겠습니까?"
                                        >
                                            삭제
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
