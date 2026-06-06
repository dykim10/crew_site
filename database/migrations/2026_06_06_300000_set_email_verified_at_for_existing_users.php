<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 기존 가입 회원은 이미 운영 중이므로 인증 완료 처리
        // 이 마이그레이션 이후 신규 가입자만 이메일 인증이 필요함
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => now()]);
    }

    public function down(): void
    {
        // 롤백 시 복원 불가 — 어느 유저가 원래 null이었는지 알 수 없음
    }
};
