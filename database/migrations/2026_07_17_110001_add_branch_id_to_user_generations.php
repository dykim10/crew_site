<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 기수별 활동 지부 스냅샷 — 같은 기수 내 이동 없음.
 * users.branch_id 는 현재 캐시, 이력은 user_generations.branch_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('crew.user_generations', 'branch_id')) {
            DB::statement('ALTER TABLE crew.user_generations ADD COLUMN branch_id BIGINT NULL');
        }

        // 백필: 기존 행은 users.branch_id 로 채움
        DB::statement('
            UPDATE crew.user_generations ug
            SET branch_id = u.branch_id
            FROM users u
            WHERE ug.user_id = u.id
              AND ug.branch_id IS NULL
              AND u.branch_id IS NOT NULL
        ');

        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_user_gen_branch_generation
            ON crew.user_generations (branch_id, generation_id)
        ');

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS crew.idx_user_gen_branch_generation');

        if (Schema::hasColumn('crew.user_generations', 'branch_id')) {
            DB::statement('ALTER TABLE crew.user_generations DROP COLUMN branch_id');
        }

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }
};
