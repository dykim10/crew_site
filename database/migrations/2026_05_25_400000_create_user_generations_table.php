<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.user_generations (
                id             BIGSERIAL PRIMARY KEY,
                user_id        BIGINT NOT NULL,
                generation_id  BIGINT NOT NULL,
                joined_at      DATE,
                is_current     BOOLEAN NOT NULL DEFAULT false,
                created_at     TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at     TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                UNIQUE(user_id, generation_id)
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_user_gen_user       ON crew.user_generations(user_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_user_gen_generation ON crew.user_generations(generation_id)");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_user_gen_current    ON crew.user_generations(user_id, is_current)");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS crew.user_generations CASCADE");
    }
};
