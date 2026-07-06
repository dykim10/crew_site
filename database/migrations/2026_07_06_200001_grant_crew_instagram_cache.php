<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** instagram_cache GRANT + PostgREST reload (테이블 생성 후 1회) */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('GRANT ALL ON crew.instagram_cache TO postgres, anon, authenticated, service_role');
        DB::statement('GRANT USAGE, SELECT ON SEQUENCE crew.instagram_cache_id_seq TO postgres, anon, authenticated, service_role');
        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        // no-op
    }
};
