<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Generation;
use App\Models\GoogleForm;

class GoogleFormApplicationImportService
{
    public function __construct(
        private GoogleFormService $googleFormService,
        private CryptoService $crypto,
        private ApplicationMatchingService $matchingService,
        private GenerationRecruitmentService $recruitmentService,
    ) {}

    /**
     * @param  array{name: string, email?: ?string, phone?: ?string}  $columnMapping
     * @return array{created: int, skipped: int, failed: int, errors: list<string>, form_missing: bool}
     */
    public function import(GoogleForm $googleForm, array $columnMapping): array
    {
        // Livewire 요청 max_execution_time(30) — 시트+행별 CORE encrypt 누적 대비
        if (function_exists('set_time_limit')) {
            @set_time_limit(180);
        }

        if ($googleForm->purpose !== GoogleForm::PURPOSE_GENERATION_RECRUIT) {
            throw new \InvalidArgumentException('기수 모집 용도 폼만 가져올 수 있습니다.');
        }

        if (! $googleForm->generation_id) {
            throw new \InvalidArgumentException('연결된 기수가 없습니다.');
        }

        $generation = Generation::find($googleForm->generation_id);
        if (! $generation) {
            throw new \InvalidArgumentException('연결된 기수를 찾을 수 없습니다.');
        }

        $applicationForm = $this->recruitmentService->resolveApplicationForm($generation);
        $formId = $applicationForm?->id;

        $sheetId = $googleForm->sheet_id;
        $responseData = $this->googleFormService->getResponses($sheetId);
        $headers = $responseData['headers'];
        $rows = $responseData['rows'];

        if ($headers === []) {
            throw new \RuntimeException('시트에 데이터가 없습니다.');
        }

        $emailHeader = $columnMapping['email'] ?? null;
        if (filled($emailHeader)) {
            $this->assertEmailColumnNotTimestamp((string) $emailHeader);
        }

        $nameIdx = array_search($columnMapping['name'], $headers, true);
        $emailIdx = filled($emailHeader) ? array_search($emailHeader, $headers, true) : false;
        $phoneHeader = $columnMapping['phone'] ?? null;
        $phoneIdx = filled($phoneHeader)
            ? array_search($phoneHeader, $headers, true)
            : false;

        if ($nameIdx === false) {
            throw new \RuntimeException('이름 열 매핑이 시트 헤더와 일치하지 않습니다.');
        }
        if (filled($emailHeader) && $emailIdx === false) {
            throw new \RuntimeException('이메일 열 매핑이 시트 헤더와 일치하지 않습니다.');
        }

        $timestampIdx = $this->detectTimestampColumnIndex($headers);
        $extraFieldMap = $this->buildExtraFieldMap($applicationForm?->form_fields ?? [], $headers);

        $pending = [];
        $created = 0;
        $skipped = 0;
        $failed = 0;
        $errors = [];
        $emailInvalid = 0;

        foreach ($rows as $rowIndex => $row) {
            $rowNum = $rowIndex + 2;
            $name = trim((string) ($row[$nameIdx] ?? ''));
            $email = ($emailIdx !== false) ? trim((string) ($row[$emailIdx] ?? '')) : '';
            $phone = ($phoneIdx !== false) ? trim((string) ($row[$phoneIdx] ?? '')) : '';

            if ($name === '') {
                $failed++;
                $errors[] = "행 {$rowNum}: 이름 없음";
                continue;
            }

            if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $failed++;
                $emailInvalid++;
                $errors[] = "행 {$rowNum}: 이메일 형식 아님 («{$emailHeader}» 열 매핑을 확인하세요)";
                continue;
            }

            $timestamp = $timestampIdx !== false
                ? trim((string) ($row[$timestampIdx] ?? ''))
                : (string) $rowNum;

            // 이메일 없으면 이름+연락처+타임스탬프로 중복키 (동일 행 재가져오기 skip)
            $identity = $email !== ''
                ? strtolower($email)
                : mb_strtolower($name).'|'.preg_replace('/\D+/', '', $phone);
            $importKey = hash('sha256', $sheetId.$timestamp.$identity);

            if (Application::where('import_key', $importKey)->exists()) {
                $skipped++;
                continue;
            }

            $fieldValues = [];
            foreach ($extraFieldMap as $key => $colIdx) {
                $fieldValues[$key] = trim((string) ($row[$colIdx] ?? ''));
            }

            $pending[] = [
                'rowNum'       => $rowNum,
                'name'         => $name,
                'email'        => $email,
                'phone'        => $phone,
                'importKey'    => $importKey,
                'field_values' => $fieldValues,
            ];
        }

        $sampled = min(count($rows), 20);
        if (
            filled($emailHeader)
            && $sampled >= 5
            && $emailInvalid >= (int) ceil($sampled / 2)
            && $pending === []
        ) {
            throw new \RuntimeException(
                '이메일 열 매핑이 잘못된 것 같습니다. 현재 «'
                .$emailHeader
                .'» 열을 이메일로 지정했습니다. 이메일이 없으면 이메일 열을 비워 두세요.'
            );
        }

        foreach (array_chunk($pending, 20) as $chunk) {
            $plain = [];
            foreach ($chunk as $i => $item) {
                $plain["{$i}:name"] = $item['name'];
                if ($item['email'] !== '') {
                    $plain["{$i}:email"] = $item['email'];
                }
                if ($item['phone'] !== '') {
                    $plain["{$i}:phone"] = $item['phone'];
                }
            }

            $encrypted = $this->crypto->encryptMany($plain);

            foreach ($chunk as $i => $item) {
                $nameEnc = $encrypted["{$i}:name"] ?? null;
                if (! $nameEnc) {
                    $failed++;
                    $errors[] = "행 {$item['rowNum']}: 이름 암호화 실패";
                    continue;
                }

                $emailEnc = null;
                $emailHash = null;
                if ($item['email'] !== '') {
                    $emailEnc = $encrypted["{$i}:email"] ?? null;
                    if (! $emailEnc) {
                        $failed++;
                        $errors[] = "행 {$item['rowNum']}: 이메일 암호화 실패";
                        continue;
                    }
                    $emailHash = $this->crypto->hashEmail($item['email']);
                }

                $phoneEnc = null;
                if ($item['phone'] !== '') {
                    $phoneEnc = $encrypted["{$i}:phone"] ?? null;
                    if (! $phoneEnc) {
                        $failed++;
                        $errors[] = "행 {$item['rowNum']}: 연락처 암호화 실패";
                        continue;
                    }
                }

                $app = Application::create([
                    'form_id'       => $formId,
                    'name_enc'      => $nameEnc,
                    'email_hash'    => $emailHash,
                    'email_enc'     => $emailEnc,
                    'phone_enc'     => $phoneEnc,
                    'field_values'  => $item['field_values'] ?? [],
                    'status'        => 'pending',
                    'agree_privacy' => true,
                    'import_source' => 'google_form',
                    'import_key'    => $item['importKey'],
                ]);

                $this->matchingService->matchApplication($app);
                $created++;
            }
        }

        return [
            'created'      => $created,
            'skipped'      => $skipped,
            'failed'       => $failed,
            'errors'       => $errors,
            'form_missing' => $applicationForm === null,
        ];
    }

    /**
     * 신청서 form_fields 라벨 ↔ 시트 헤더 매칭 → field_values 키.
     *
     * @param  list<array<string, mixed>>  $formFields
     * @param  list<string>  $headers
     * @return array<string, int> key => column index
     */
    private function buildExtraFieldMap(array $formFields, array $headers): array
    {
        $headerIndex = [];
        foreach ($headers as $i => $header) {
            $headerIndex[$this->normalizeHeader((string) $header)] = $i;
        }

        $map = [];
        foreach ($formFields as $field) {
            $key = (string) ($field['key'] ?? $field['data']['key'] ?? '');
            $label = (string) ($field['data']['label'] ?? $field['label'] ?? '');
            if ($key === '' || $label === '') {
                continue;
            }
            $idx = $headerIndex[$this->normalizeHeader($label)] ?? null;
            if ($idx !== null) {
                $map[$key] = $idx;
            }
        }

        return $map;
    }

    private function normalizeHeader(string $value): string
    {
        return mb_strtolower(preg_replace('/\s+/u', '', $value) ?? $value);
    }

    private function assertEmailColumnNotTimestamp(string $header): void
    {
        $normalized = mb_strtolower(trim($header));
        if (in_array($normalized, ['타임스탬프', 'timestamp', 'submitted at', '제출 시간', '시간'], true)) {
            throw new \InvalidArgumentException(
                '이메일 열에 «'.$header.'»가 선택되어 있습니다. 이메일이 없으면 이메일 열을 비워 두세요.'
            );
        }
    }

    /** @param  list<string>  $headers */
    private function detectTimestampColumnIndex(array $headers): int|false
    {
        foreach ($headers as $i => $header) {
            $normalized = mb_strtolower(trim($header));
            if (in_array($normalized, ['타임스탬프', 'timestamp', 'submitted at', '제출 시간', '시간'], true)) {
                return $i;
            }
        }

        return false;
    }
}
