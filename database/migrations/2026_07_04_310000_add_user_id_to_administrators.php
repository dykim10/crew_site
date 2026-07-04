<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE crew.administrators ADD COLUMN IF NOT EXISTS user_id BIGINT NULL');
        DB::statement('
            CREATE UNIQUE INDEX IF NOT EXISTS administrators_user_id_unique
            ON crew.administrators (user_id)
            WHERE user_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS crew.administrators_user_id_unique');
        DB::statement('ALTER TABLE crew.administrators DROP COLUMN IF EXISTS user_id');
    }
};
