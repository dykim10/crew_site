<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 기수 정보 테이블 (public 스키마)
        DB::statement("
            CREATE TABLE IF NOT EXISTS public.generations (
                id               BIGSERIAL PRIMARY KEY,
                number           SMALLINT NOT NULL,
                alias            VARCHAR(100),
                start_date       DATE,
                end_date         DATE,
                main_race_id     BIGINT,
                main_race_name   VARCHAR(200),
                notes            TEXT,
                active_branch_ids JSONB NOT NULL DEFAULT '[]',
                status           VARCHAR(20) NOT NULL DEFAULT 'upcoming',
                created_at       TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at       TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        DB::statement("CREATE UNIQUE INDEX IF NOT EXISTS idx_generations_number ON public.generations(number)");

        // branches 테이블에 관리자/운영자/상태/수정일 추가
        DB::statement("ALTER TABLE public.branches ADD COLUMN IF NOT EXISTS admin_id    BIGINT");
        DB::statement("ALTER TABLE public.branches ADD COLUMN IF NOT EXISTS operator_id BIGINT");
        DB::statement("ALTER TABLE public.branches ADD COLUMN IF NOT EXISTS status      VARCHAR(20) NOT NULL DEFAULT 'active'");
        DB::statement("ALTER TABLE public.branches ADD COLUMN IF NOT EXISTS updated_at  TIMESTAMPTZ(6) NOT NULL DEFAULT now()");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS public.generations CASCADE");
        DB::statement("ALTER TABLE public.branches DROP COLUMN IF EXISTS admin_id");
        DB::statement("ALTER TABLE public.branches DROP COLUMN IF EXISTS operator_id");
        DB::statement("ALTER TABLE public.branches DROP COLUMN IF EXISTS status");
        DB::statement("ALTER TABLE public.branches DROP COLUMN IF EXISTS updated_at");
    }
};
