<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE TABLE IF NOT EXISTS crew.meeting_minutes (
            id            BIGSERIAL PRIMARY KEY,
            user_id       BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
            title         VARCHAR(200) NOT NULL,
            content       TEXT,
            meeting_date  DATE NOT NULL,
            views         INTEGER NOT NULL DEFAULT 0,
            is_pinned     BOOLEAN NOT NULL DEFAULT FALSE,
            created_at    TIMESTAMPTZ(6),
            updated_at    TIMESTAMPTZ(6)
        )');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.meeting_minutes');
    }
};
