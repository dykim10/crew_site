<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * 암호화 서비스 (app/Services/CryptoService.php)
 *
 * 개인정보(이메일·이름)의 암호화·복호화를 CORE API 에 위임한다.
 * AES 키를 PHP 측에 보관하지 않고 CORE API(Python)에서 관리하여 키 노출 범위를 최소화한다.
 *
 * [사용 흐름]
 *   회원가입 시
 *     email → hashEmail() → SHA-256 해시 → email_hash 컬럼에 저장 (조회용)
 *     email, name → encrypt() → CORE POST /api/crypto/encrypt → email_enc, name_enc 저장
 *
 *   데이터 조회 시
 *     email_enc → decrypt() → CORE POST /api/crypto/decrypt
 *     → User::getEmailAttribute() 가상 속성이 내부적으로 호출해 투명하게 복호화
 *
 * [CORE API 호출 실패 처리]
 *   timeout 5초. 실패 시 null 반환 + Log::error 기록.
 *   호출부(User 모델)에서 null 을 처리해야 한다.
 */
class CryptoService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.core_api.url');
    }

    public function hashEmail(string $email): string
    {
        return hash('sha256', strtolower(trim($email)));
    }

    public function encrypt(string $value): ?string
    {
        try {
            $response = Http::timeout(5)->post("{$this->baseUrl}/api/crypto/encrypt", [
                'value' => $value,
            ]);
            return $response->json('encrypted');
        } catch (\Exception $e) {
            Log::error('CryptoService encrypt 실패', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function decrypt(string $value): ?string
    {
        try {
            $response = Http::timeout(5)->post("{$this->baseUrl}/api/crypto/decrypt", [
                'value' => $value,
            ]);
            return $response->json('decrypted');
        } catch (\Exception $e) {
            Log::error('CryptoService decrypt 실패', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
