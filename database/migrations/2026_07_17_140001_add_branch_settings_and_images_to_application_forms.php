<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew.application_forms', function (Blueprint $table) {
            if (! Schema::hasColumn('crew.application_forms', 'branch_settings')) {
                // [{"branch_id":1,"is_active":true,"max_applicants":60}, ...]
                $table->jsonb('branch_settings')->nullable();
            }
            if (! Schema::hasColumn('crew.application_forms', 'images')) {
                // S3 path 또는 URL 배열 (최대 10)
                $table->jsonb('images')->nullable();
            }
        });

        Schema::table('crew.applications', function (Blueprint $table) {
            if (! Schema::hasColumn('crew.applications', 'preferred_branch_id')) {
                $table->unsignedBigInteger('preferred_branch_id')->nullable()->index();
            }
        });

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        Schema::table('crew.applications', function (Blueprint $table) {
            if (Schema::hasColumn('crew.applications', 'preferred_branch_id')) {
                $table->dropColumn('preferred_branch_id');
            }
        });

        Schema::table('crew.application_forms', function (Blueprint $table) {
            if (Schema::hasColumn('crew.application_forms', 'images')) {
                $table->dropColumn('images');
            }
            if (Schema::hasColumn('crew.application_forms', 'branch_settings')) {
                $table->dropColumn('branch_settings');
            }
        });

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }
};
