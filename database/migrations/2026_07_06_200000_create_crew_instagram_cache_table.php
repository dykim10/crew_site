<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * @pac-run 공식 Instagram 캐시 (CORE Apify → crew.instagram_cache)
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            CREATE TABLE IF NOT EXISTS crew.instagram_cache (
                id              BIGSERIAL PRIMARY KEY,
                post_id         VARCHAR(100) NOT NULL,
                media_type      VARCHAR(20) NOT NULL DEFAULT 'IMAGE',
                media_url       TEXT,
                thumbnail_url   TEXT,
                caption         TEXT,
                like_count      INTEGER NOT NULL DEFAULT 0,
                comments_count  INTEGER NOT NULL DEFAULT 0,
                permalink       TEXT,
                posted_at       TIMESTAMPTZ(6),
                fetched_at      TIMESTAMPTZ(6),
                created_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                UNIQUE (post_id)
            )
        SQL);

        DB::statement('CREATE INDEX IF NOT EXISTS idx_crew_instagram_cache_posted ON crew.instagram_cache(posted_at DESC)');

        DB::statement('GRANT ALL ON crew.instagram_cache TO postgres, anon, authenticated, service_role');
        DB::statement('GRANT USAGE, SELECT ON SEQUENCE crew.instagram_cache_id_seq TO postgres, anon, authenticated, service_role');
        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.instagram_cache CASCADE');
    }
};
