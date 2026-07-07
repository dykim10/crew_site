<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** 예약 문자 테이블 GRANT + PostgREST reload (CORE APScheduler용) */
return new class extends Migration
{
    private array $tables = [
        'scheduled_sms',
        'scheduled_sms_recipients',
        'sms_test_recipients',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            DB::statement("GRANT ALL ON crew.{$table} TO postgres, anon, authenticated, service_role");
            DB::statement("GRANT USAGE, SELECT ON SEQUENCE crew.{$table}_id_seq TO postgres, anon, authenticated, service_role");
        }

        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        // no-op
    }
};
