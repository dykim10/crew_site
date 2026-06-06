<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("GRANT USAGE ON SCHEMA crew TO postgres, anon, authenticated, service_role");

        DB::statement("
            GRANT ALL ON crew.event_fixed_submissions
                TO postgres, anon, authenticated, service_role
        ");

        DB::statement("
            GRANT USAGE, SELECT ON SEQUENCE crew.event_fixed_submissions_id_seq
                TO postgres, anon, authenticated, service_role
        ");
    }

    public function down(): void {}
};
