<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew.applications', function (Blueprint $table) {
            if (! Schema::hasColumn('crew.applications', 'matched_user_id')) {
                $table->unsignedBigInteger('matched_user_id')->nullable()->index();
            }
            if (! Schema::hasColumn('crew.applications', 'matched_at')) {
                $table->timestampTz('matched_at', 6)->nullable();
            }
            if (! Schema::hasColumn('crew.applications', 'matched_by')) {
                $table->string('matched_by', 20)->nullable();
            }
        });

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        Schema::table('crew.applications', function (Blueprint $table) {
            foreach (['matched_by', 'matched_at', 'matched_user_id'] as $col) {
                if (Schema::hasColumn('crew.applications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        DB::statement("NOTIFY pgrst, 'reload schema'");
    }
};
