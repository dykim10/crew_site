<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 재설계: 앨범형 → 단일 이미지형으로 전환
        DB::statement('DROP TABLE IF EXISTS crew.photo_galleries CASCADE');

        DB::statement("
            CREATE TABLE crew.photo_galleries (
                id            BIGSERIAL PRIMARY KEY,
                crew_id       BIGINT         NOT NULL DEFAULT 1,
                admin_id      BIGINT         NOT NULL,
                title         VARCHAR(200)   NOT NULL,
                description   TEXT,
                image_url     TEXT           NOT NULL,
                thumbnail_url TEXT,
                taken_at      DATE,
                view_count    INTEGER        NOT NULL DEFAULT 0,
                sort_order    INTEGER        NOT NULL DEFAULT 0,
                created_at    TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at    TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        DB::statement('CREATE INDEX idx_photo_sort ON crew.photo_galleries(sort_order DESC, created_at DESC)');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.photo_galleries CASCADE');
    }
};
