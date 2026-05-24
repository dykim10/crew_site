<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleFormService
{
    /**
     * Google Sheet에서 응답 데이터를 읽어온다.
     *
     * @param  string  $sheetId  Google Sheet URL 중 /d/{SHEET_ID}/ 부분
     * @return array{headers: string[], rows: array<int, string[]>}
     *
     * @throws \RuntimeException  인증 파일 미존재 또는 API 오류 시
     */
    public function getResponses(string $sheetId): array
    {
        $credentialsPath = storage_path('app/google/service-account.json');

        if (! file_exists($credentialsPath)) {
            throw new \RuntimeException(
                'Google 서비스 계정 키 파일이 없습니다. ' .
                'storage/app/google/service-account.json 에 배치해 주세요.'
            );
        }

        $client = new Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope(Sheets::SPREADSHEETS_READONLY);

        $service = new Sheets($client);

        try {
            $response = $service->spreadsheets_values->get($sheetId, 'A:ZZZ');
        } catch (\Google\Service\Exception $e) {
            $body = json_decode($e->getMessage(), true);
            $msg  = $body['error']['message'] ?? $e->getMessage();
            throw new \RuntimeException('Google Sheets API 오류: ' . $msg);
        }

        $values = $response->getValues() ?? [];

        if (empty($values)) {
            return ['headers' => [], 'rows' => []];
        }

        $headers = array_shift($values);

        return ['headers' => $headers, 'rows' => $values];
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

        return $urlOrId;
    }
}
