<div class="adm-detail">

    @if($error)
        <div class="adm-error-box">
            <svg class="shrink-0" style="width:1.25rem;height:1.25rem;color:#f87171;margin-top:0.1rem;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
            </svg>
            <div>
                <strong>Google Sheets 연결 오류</strong>
                <p style="white-space:pre-wrap;margin-top:0.5rem;line-height:1.6;">{{ $error }}</p>
            </div>
        </div>

    @elseif(empty($headers))
        <div class="adm-empty">응답 데이터가 없습니다.</div>

    @else
        @if(!empty($warning))
            <div style="margin-bottom:0.75rem;padding:0.75rem 1rem;border-radius:0.5rem;background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;font-size:0.84rem;line-height:1.5;white-space:pre-wrap;">
                {{ $warning }}
            </div>
        @endif

        <p style="font-size:0.84rem;color:var(--adm-muted);">
            총 <span style="font-weight:600;color:var(--adm-ink);">{{ $count }}건</span>
        </p>

        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        @foreach($headers as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            @foreach($headers as $i => $header)
                                <td>{{ $row[$i] ?? '' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
