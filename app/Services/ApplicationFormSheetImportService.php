<?php

namespace App\Services;

use App\Models\GoogleForm;
use Illuminate\Support\Str;

/**
 * 신청서 폼 ← 구글 응답 시트 헤더 (Forms API 금지, Sheets만).
 * merge only · 기본항목(타임스탬프·이름·이메일·연락처) 제외.
 */
class ApplicationFormSheetImportService
{
    /** @var list<string> */
    public const EXCLUDED_HEADERS = [
        '타임스탬프', 'timestamp', 'submitted at', '제출 시간', '시간',
        '이름', '성명', 'name', '실명',
        '이메일', 'email', 'e-mail', '메일', '메일주소', 'email address',
        '연락처', '전화번호', '휴대폰', '휴대전화', 'phone', 'mobile', 'tel',
    ];

    public function __construct(private GoogleFormService $googleFormService) {}

    /**
     * @param  list<array<string, mixed>>  $existingFields  Filament Builder / DB form_fields
     * @return array{fields: list<array>, added: list<string>, skipped: list<string>, mapping: array{name:?string,email:?string,phone:?string}}
     */
    public function mergeFromGoogleForm(GoogleForm $googleForm, array $existingFields): array
    {
        $data = $this->googleFormService->getResponses($googleForm->sheet_id);
        $headers = $data['headers'] ?? [];

        return $this->mergeFromHeaders($headers, $existingFields);
    }

    /**
     * @param  list<string>  $headers
     * @param  list<array<string, mixed>>  $existingFields
     * @return array{fields: list<array>, added: list<string>, skipped: list<string>, mapping: array{name:?string,email:?string,phone:?string}}
     */
    public function mergeFromHeaders(array $headers, array $existingFields): array
    {
        $existingFields = array_values($existingFields);
        $existingLabels = [];
        foreach ($existingFields as $field) {
            $label = (string) ($field['data']['label'] ?? $field['label'] ?? '');
            if ($label !== '') {
                $existingLabels[$this->normalize($label)] = true;
            }
        }

        $added = [];
        $skipped = [];
        $fields = $existingFields;

        foreach ($headers as $header) {
            $header = trim((string) $header);
            if ($header === '') {
                continue;
            }

            if ($this->isExcluded($header)) {
                $skipped[] = $header.' (기본항목)';
                continue;
            }

            $norm = $this->normalize($header);
            if (isset($existingLabels[$norm])) {
                $skipped[] = $header.' (이미 있음)';
                continue;
            }

            $fields[] = $this->makeTextBlock($header, count($fields));
            $existingLabels[$norm] = true;
            $added[] = $header;
        }

        $fields = $this->ensureFieldKeys($fields);

        return [
            'fields'  => $fields,
            'added'   => $added,
            'skipped' => $skipped,
            'mapping' => $this->suggestColumnMapping($headers),
        ];
    }

    /**
     * @param  list<string>  $headers
     * @return array{name:?string,email:?string,phone:?string}
     */
    public function suggestColumnMapping(array $headers): array
    {
        return [
            'name'  => $this->guessHeader($headers, ['이름', '성명', 'name', '실명']),
            'email' => $this->guessHeader($headers, [
                '이메일', 'email', 'e-mail', '메일', '메일주소', 'email address',
            ]),
            'phone' => $this->guessHeader($headers, [
                '연락처', '전화번호', '휴대폰', '휴대전화', 'phone', 'mobile', 'tel',
            ]),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $fields
     * @return list<array<string, mixed>>
     */
    public function ensureFieldKeys(array $fields): array
    {
        $used = [];

        foreach ($fields as $i => &$field) {
            $label = (string) ($field['data']['label'] ?? $field['label'] ?? 'field');
            $key = (string) ($field['key'] ?? $field['data']['key'] ?? '');

            if ($key === '') {
                $key = Str::snake(Str::ascii($label)) ?: ('field_'.$i);
                $key = preg_replace('/[^a-z0-9_]/', '', strtolower($key)) ?: ('field_'.$i);
            }

            $base = $key;
            $n = 2;
            while (isset($used[$key])) {
                $key = $base.'_'.$n;
                $n++;
            }
            $used[$key] = true;

            $field['key'] = $key;
            if (! isset($field['data']) || ! is_array($field['data'])) {
                $field['data'] = [];
            }
            $field['data']['key'] = $key;
            $field['type'] = $field['type'] ?? 'text';
        }
        unset($field);

        return array_values($fields);
    }

    /** @param  list<string>  $headers @param  list<string>  $candidates */
    private function guessHeader(array $headers, array $candidates): ?string
    {
        $want = array_map(fn (string $c) => $this->normalize($c), $candidates);
        foreach ($headers as $header) {
            if (in_array($this->normalize((string) $header), $want, true)) {
                return (string) $header;
            }
        }

        return null;
    }

    private function isExcluded(string $header): bool
    {
        $norm = $this->normalize($header);
        foreach (self::EXCLUDED_HEADERS as $ex) {
            if ($norm === $this->normalize($ex)) {
                return true;
            }
        }

        return false;
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(preg_replace('/\s+/u', '', $value) ?? $value);
    }

    /** @return array{type: string, key: string, data: array} */
    private function makeTextBlock(string $label, int $index): array
    {
        $key = Str::snake(Str::ascii($label)) ?: ('field_'.$index);
        $key = preg_replace('/[^a-z0-9_]/', '', strtolower($key)) ?: ('field_'.$index);

        return [
            'type' => 'text',
            'key'  => $key,
            'data' => [
                'key'      => $key,
                'label'    => $label,
                'required' => false,
            ],
        ];
    }
}
