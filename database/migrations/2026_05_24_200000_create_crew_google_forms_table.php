<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.google_forms (
                id          BIGSERIAL PRIMARY KEY,
                title       VARCHAR(255)    NOT NULL,
                sheet_id    VARCHAR(255)    NOT NULL,
                description TEXT,
                is_active   BOOLEAN         NOT NULL DEFAULT true,
                created_at  TIMESTAMPTZ(6)  DEFAULT now(),
                updated_at  TIMESTAMPTZ(6)  DEFAULT now()
            )
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.google_forms');
    }
};
