<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE SCHEMA IF NOT EXISTS crew');
        DB::statement("GRANT USAGE ON SCHEMA crew TO postgres, anon, authenticated, service_role");

        if (! Schema::hasTable('crew.admin_logs')) {
            Schema::create('crew.admin_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admin_id')->index();
                $table->string('action', 50);
                $table->string('target_type', 50);
                $table->unsignedBigInteger('target_id')->nullable();
                $table->text('description');
                $table->string('ip_address', 45)->nullable();
                $table->timestampsTz(6);

                $table->index(['admin_id', 'created_at']);
                $table->index(['target_type', 'target_id']);
                $table->index('action');
            });

            DB::statement("
                ALTER TABLE crew.admin_logs
                ADD CONSTRAINT fk_admin_logs_admin
                FOREIGN KEY (admin_id) REFERENCES public.users(id) ON DELETE SET NULL
                NOT VALID
            ");

            DB::statement("GRANT ALL ON crew.admin_logs TO postgres, anon, authenticated, service_role");
            DB::statement("GRANT USAGE, SELECT ON SEQUENCE crew.admin_logs_id_seq TO postgres, anon, authenticated, service_role");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crew.admin_logs');
    }
};
