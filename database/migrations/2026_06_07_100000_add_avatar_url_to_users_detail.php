<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE crew.users_detail ADD COLUMN IF NOT EXISTS avatar_url TEXT NULL");

        DB::statement("GRANT ALL ON crew.users_detail TO postgres, anon, authenticated, service_role");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE crew.users_detail DROP COLUMN IF EXISTS avatar_url");
    }
};
