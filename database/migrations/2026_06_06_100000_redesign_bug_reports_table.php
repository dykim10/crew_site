<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 재설계: 기존 테이블 DROP 후 신규 스키마로 재생성
        DB::statement('DROP TABLE IF EXISTS crew.bug_reports CASCADE');

        DB::statement("
            CREATE TABLE crew.bug_reports (
                id             BIGSERIAL PRIMARY KEY,
                crew_id        BIGINT         NOT NULL DEFAULT 1,
                user_id        BIGINT         NOT NULL,
                title          VARCHAR(200)   NOT NULL,
                path           TEXT           NOT NULL,
                description    TEXT           NOT NULL,
                screenshot_url TEXT,
                severity       VARCHAR(20)    NOT NULL DEFAULT 'medium',
                status         VARCHAR(20)    NOT NULL DEFAULT 'open',
                admin_note     TEXT,
                resolved_by    BIGINT,
                resolved_at    TIMESTAMPTZ(6),
                created_at     TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at     TIMESTAMPTZ(6) NOT NULL DEFAULT now(),

                CONSTRAINT chk_bug_severity CHECK (severity IN ('low', 'medium', 'high')),
                CONSTRAINT chk_bug_status   CHECK (status   IN ('open', 'in_progress', 'resolved'))
            )
        ");

        DB::statement('CREATE INDEX idx_bug_reports_user   ON crew.bug_reports(user_id)');
        DB::statement('CREATE INDEX idx_bug_reports_status ON crew.bug_reports(status)');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.bug_reports CASCADE');
    }
};
