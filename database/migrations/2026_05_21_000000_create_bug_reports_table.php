<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.bug_reports (
                id              BIGSERIAL PRIMARY KEY,
                user_id         BIGINT NOT NULL,
                title           VARCHAR(200) NOT NULL,
                description     TEXT NOT NULL,
                status          VARCHAR(20) NOT NULL DEFAULT 'pending',
                priority        VARCHAR(20) NOT NULL DEFAULT 'medium',
                attachment_url  TEXT,
                attachment_name VARCHAR(200),
                admin_note      TEXT,
                resolved_by     BIGINT,
                resolved_at     TIMESTAMPTZ(6),
                created_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_bug_reports_user ON crew.bug_reports(user_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_bug_reports_status ON crew.bug_reports(status)");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS crew.bug_reports CASCADE");
    }
};
