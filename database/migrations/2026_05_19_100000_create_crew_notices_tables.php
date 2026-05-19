<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.notices (
                id          BIGSERIAL PRIMARY KEY,
                title       TEXT NOT NULL,
                content     TEXT NOT NULL,
                author_id   BIGINT NOT NULL,
                is_pinned   BOOLEAN NOT NULL DEFAULT FALSE,
                target_type TEXT NOT NULL DEFAULT 'all',
                target_ids  JSONB NOT NULL DEFAULT '[]',
                created_at  TIMESTAMPTZ(6) NOT NULL DEFAULT NOW(),
                updated_at  TIMESTAMPTZ(6) NOT NULL DEFAULT NOW()
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_notices_created ON crew.notices(created_at DESC)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_notices_pinned  ON crew.notices(is_pinned)");

        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.notice_reads (
                id        BIGSERIAL PRIMARY KEY,
                notice_id BIGINT NOT NULL,
                user_id   BIGINT NOT NULL,
                read_at   TIMESTAMPTZ(6) NOT NULL DEFAULT NOW(),
                UNIQUE(notice_id, user_id)
            )
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS crew.notice_reads CASCADE");
        DB::statement("DROP TABLE IF EXISTS crew.notices CASCADE");
    }
};
