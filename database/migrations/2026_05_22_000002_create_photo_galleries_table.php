<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE TABLE IF NOT EXISTS crew.photo_galleries (
            id              BIGSERIAL PRIMARY KEY,
            user_id         BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            session_number  INTEGER NOT NULL,
            title           VARCHAR(200) NOT NULL,
            description     TEXT,
            taken_at        DATE NOT NULL,
            cover_image_url TEXT,
            album_url       TEXT,
            photo_urls      JSONB NOT NULL DEFAULT \'[]\',
            created_at      TIMESTAMPTZ(6),
            updated_at      TIMESTAMPTZ(6)
        )');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.photo_galleries');
    }
};
