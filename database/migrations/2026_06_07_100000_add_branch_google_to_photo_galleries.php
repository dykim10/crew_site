<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE crew.photo_galleries
                ADD COLUMN IF NOT EXISTS branch_id        BIGINT DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS google_photos_url TEXT   DEFAULT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE crew.photo_galleries
                DROP COLUMN IF EXISTS branch_id,
                DROP COLUMN IF EXISTS google_photos_url
        ");
    }
};
