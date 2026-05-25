<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE crew.events
                ADD COLUMN IF NOT EXISTS score_type  VARCHAR(20),
                ADD COLUMN IF NOT EXISTS score_config JSONB
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE crew.events DROP COLUMN IF EXISTS score_type");
        DB::statement("ALTER TABLE crew.events DROP COLUMN IF EXISTS score_config");
    }
};
