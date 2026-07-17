<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 구글 폼 설문에 이메일이 없는 경우 가져오기 허용.
 * 자동 매칭은 email_hash 있을 때만 — 없으면 관리자 수동 연결.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE crew.applications ALTER COLUMN email_hash DROP NOT NULL');
        DB::statement('ALTER TABLE crew.applications ALTER COLUMN email_enc DROP NOT NULL');
        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE crew.applications ALTER COLUMN email_hash SET NOT NULL');
        DB::statement('ALTER TABLE crew.applications ALTER COLUMN email_enc SET NOT NULL');
        DB::statement("NOTIFY pgrst, 'reload schema'");
    }
};
