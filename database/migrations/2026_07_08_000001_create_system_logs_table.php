<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** 통합 시스템 로그 (260708 TASK-system-logs) — public 스키마 공통 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('public.system_logs')) {
            return;
        }

        Schema::create('public.system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 20);
            $table->string('category', 30);
            $table->string('level', 10);
            $table->text('message');
            $table->jsonb('context')->nullable();
            $table->timestampTz('created_at', 6)->useCurrent();
        });

        DB::statement('CREATE INDEX IF NOT EXISTS idx_system_logs_created_at ON public.system_logs (created_at DESC)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_system_logs_source_category ON public.system_logs (source, category)');
        DB::statement('CREATE INDEX IF NOT EXISTS idx_system_logs_level ON public.system_logs (level)');

        DB::statement('GRANT ALL ON public.system_logs TO postgres, anon, authenticated, service_role');
        DB::statement('GRANT USAGE, SELECT ON SEQUENCE public.system_logs_id_seq TO postgres, anon, authenticated, service_role');
        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        Schema::dropIfExists('public.system_logs');
    }
};
