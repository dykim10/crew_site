<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ─── crew.events 컬럼 추가 ───────────────────────────────────────
        // 썸네일 이미지 (S3 URL, webp)
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS thumbnail_url TEXT");

        // 이벤트 장소
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS location VARCHAR(255)");

        // 모집기간 (start_date/end_date 는 이벤트 기간 유지)
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS recruit_start_at TIMESTAMPTZ(6)");
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS recruit_end_at   TIMESTAMPTZ(6)");

        // ─── crew.event_registrations 컬럼 추가 (B안: 별도 컬럼) ────────
        // 휴대폰번호 암호화 저장 (복호화 가능)
        DB::statement("ALTER TABLE crew.event_registrations ADD COLUMN IF NOT EXISTS phone_enc TEXT");

        // 이메일 평문 저장 (@로 식별)
        DB::statement("ALTER TABLE crew.event_registrations ADD COLUMN IF NOT EXISTS email VARCHAR(255)");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE crew.events DROP COLUMN IF EXISTS thumbnail_url");
        DB::statement("ALTER TABLE crew.events DROP COLUMN IF EXISTS location");
        DB::statement("ALTER TABLE crew.events DROP COLUMN IF EXISTS recruit_start_at");
        DB::statement("ALTER TABLE crew.events DROP COLUMN IF EXISTS recruit_end_at");

        DB::statement("ALTER TABLE crew.event_registrations DROP COLUMN IF EXISTS phone_enc");
        DB::statement("ALTER TABLE crew.event_registrations DROP COLUMN IF EXISTS email");
    }
};
