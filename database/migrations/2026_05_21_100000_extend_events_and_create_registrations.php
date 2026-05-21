<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // crew.events 확장
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS event_type VARCHAR(1) NOT NULL DEFAULT 'B'");
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS parent_event_id BIGINT");
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS target_scope VARCHAR(20) NOT NULL DEFAULT 'all'");
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS generation SMALLINT");
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS form_schema JSONB");
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS score_rules JSONB");
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS max_participants INT");
        DB::statement("ALTER TABLE crew.events ADD COLUMN IF NOT EXISTS is_registration_open BOOLEAN NOT NULL DEFAULT true");

        // crew.event_scores 확장
        DB::statement("ALTER TABLE crew.event_scores ADD COLUMN IF NOT EXISTS group_id BIGINT");
        DB::statement("ALTER TABLE crew.event_scores ADD COLUMN IF NOT EXISTS source VARCHAR(20) NOT NULL DEFAULT 'manual'");
        DB::statement("ALTER TABLE crew.event_scores ADD COLUMN IF NOT EXISTS awarded_by BIGINT");

        // 참가 신청 (B타입 전용)
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.event_registrations (
                id          BIGSERIAL PRIMARY KEY,
                event_id    BIGINT NOT NULL,
                user_id     BIGINT NOT NULL,
                form_data   JSONB NOT NULL DEFAULT '{}',
                created_at  TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at  TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                UNIQUE (event_id, user_id)
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_event_reg_event ON crew.event_registrations(event_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_event_reg_user  ON crew.event_registrations(user_id)");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS crew.event_registrations CASCADE");
    }
};
