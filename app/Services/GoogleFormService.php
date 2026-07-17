<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleFormService
{
    public function credentialsPath(): string
    {
        return config('services.google.service_account_path')
            ?? storage_path('app/google/service-account.json');
    }

    public function getServiceAccountEmail(): ?string
    {
        $path = $this->credentialsPath();
        if (! is_readable($path)) {
            return null;
        }

        $json = json_decode((string) file_get_contents($path), true);

        return is_array($json) ? ($json['client_email'] ?? null) : null;
    }

    /**
     * @return array{headers: string[], rows: array<int, string[]>, source?: string, warning?: string}
     */
    public function getResponses(string $sheetId): array
    {
        $sheetId = self::extractSheetId($sheetId);

        try {
            return $this->getResponsesViaApi($sheetId);
        } catch (\RuntimeException $e) {
            if (! $this->isPermissionError($e)) {
                throw $e;
            }

            try {
                $data = $this->getResponsesViaPublicCsv($sheetId);
                $data['source'] = 'public_csv';
                $data['warning'] = '서비스 계정 권한이 없어 공개 CSV로 불러왔습니다. 보안을 위해 시트를 비공개로 두고 서비스 계정을 뷰어로 공유하는 것을 권장합니다.';

                return $data;
            } catch (\Throwable) {
                throw $this->permissionDeniedException($sheetId);
            }
        }
    }

    /**
     * @return array{headers: string[], rows: array<int, string[]>}
     */
    private function getResponsesViaApi(string $sheetId): array
    {
        $credentialsPath = $this->credentialsPath();

        if (! file_exists($credentialsPath)) {
            throw new \RuntimeException(
                'Google 서비스 계정 키 파일이 없습니다. ' .
                $credentialsPath . ' 에 배치해 주세요.'
            );
        }

        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope(Sheets::SPREADSHEETS_READONLY);
        // 기본 Guzzle 무제한 대기 → PHP max_execution_time(30) FatalError 방지
        $client->setHttpClient(new \GuzzleHttp\Client([
            'timeout'         => 25,
            'connect_timeout' => 5,
        ]));

        $service = new Sheets($client);

        try {
            $response = $service->spreadsheets_values->get($sheetId, 'A:ZZZ');
        } catch (\Google\Service\Exception $e) {
            throw new \RuntimeException($this->formatApiError($e, $sheetId), $e->getCode(), $e);
        }

        return $this->parseSheetValues($response->getValues() ?? []);
    }

    /**
     * @return array{headers: string[], rows: array<int, string[]>}
     */
    private function getResponsesViaPublicCsv(string $sheetId): array
    {
        $url = "https://docs.google.com/spreadsheets/d/{$sheetId}/export?format=csv&gid=0";

        $context = stream_context_create([
            'http' => [
                'timeout' => 15,
                'user_agent' => 'PAC-RUN-CREW/1.0',
            ],
        ]);

        $csv = @file_get_contents($url, false, $context);
        if ($csv === false || trim($csv) === '') {
            throw new \RuntimeException('공개 CSV를 읽을 수 없습니다.');
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($csv)) ?: [];
        $rows = array_values(array_filter(array_map(
            fn (string $line) => str_getcsv($line),
            $lines
        ), fn (array $row) => count(array_filter($row, fn ($cell) => $cell !== null && $cell !== '')) > 0));

        return $this->parseSheetValues($rows);
    }

    /**
     * @param  array<int, array<int, mixed>>  $values
     * @return array{headers: string[], rows: array<int, string[]>}
     */
    private function parseSheetValues(array $values): array
    {
        if (empty($values)) {
            return ['headers' => [], 'rows' => []];
        }

        $headers = array_map(fn ($cell) => (string) $cell, array_shift($values));
        $rows = array_map(
            fn (array $row) => array_map(fn ($cell) => (string) ($cell ?? ''), $row),
            $values
        );

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function isPermissionError(\RuntimeException $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'permission')
            || str_contains($message, 'caller does not have')
            || str_contains($message, 'forbidden');
    }

    private function permissionDeniedException(string $sheetId): \RuntimeException
    {
        $email = $this->getServiceAccountEmail() ?? '(서비스 계정 이메일 확인 불가)';

        $sheetLine = filled($sheetId)
            ? "3. 관리자에 등록한 Sheet ID가 위 시트 URL의 /d/ 뒤 ID와 같은지 확인합니다.\n   현재 ID: {$sheetId}\n\n"
            : "3. 관리자에 등록한 Sheet ID가 스프레드시트 URL의 /d/ 뒤 ID와 같은지 확인합니다.\n\n";

        return new \RuntimeException(
            "Google Sheets 접근 권한이 없습니다.\n\n" .
            "1. 구글 폼 → 응답 → 스프레드시트에서 보기 로 연결된 시트를 엽니다.\n" .
            "2. 시트 우측 상단 [공유] → 아래 이메일을 뷰어로 추가합니다.\n" .
            "   {$email}\n" .
            $sheetLine .
            "※ 폼 URL(viewform)이 아니라 스프레드시트 URL을 등록해야 합니다."
        );
    }

    private function formatApiError(\Google\Service\Exception $e, string $sheetId = ''): string
    {
        $body = json_decode($e->getMessage(), true);
        $msg  = $body['error']['message'] ?? $e->getMessage();

        if ($this->isPermissionError(new \RuntimeException($msg))) {
            return $this->permissionDeniedException($sheetId)->getMessage();
        }

        return 'Google Sheets API 오류: ' . $msg;
    }

    /**
     * Sheet URL에서 Sheet ID를 추출한다.
     * https://docs.google.com/spreadsheets/d/{SHEET_ID}/edit → SHEET_ID
     */
    public static function extractSheetId(string $urlOrId): string
    {
        if (preg_match('#/spreadsheets/d/([a-zA-Z0-9_-]+)#', $urlOrId, $m)) {
            return $m[1];
        }

        return trim($urlOrId);
    }
}
