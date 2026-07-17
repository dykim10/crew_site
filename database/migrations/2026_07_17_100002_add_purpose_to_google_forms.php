<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew.google_forms', function (Blueprint $table) {
            if (! Schema::hasColumn('crew.google_forms', 'purpose')) {
                $table->string('purpose', 30)->default('general')->index();
            }
            if (! Schema::hasColumn('crew.google_forms', 'generation_id')) {
                $table->unsignedBigInteger('generation_id')->nullable();
            }
            if (! Schema::hasColumn('crew.google_forms', 'event_id')) {
                $table->unsignedBigInteger('event_id')->nullable();
            }
            if (! Schema::hasColumn('crew.google_forms', 'column_mapping')) {
                $table->jsonb('column_mapping')->nullable();
            }
            if (! Schema::hasColumn('crew.google_forms', 'form_url')) {
                $table->text('form_url')->nullable();
            }
        });

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        Schema::table('crew.google_forms', function (Blueprint $table) {
            foreach (['form_url', 'column_mapping', 'event_id', 'generation_id', 'purpose'] as $col) {
                if (Schema::hasColumn('crew.google_forms', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        DB::statement("NOTIFY pgrst, 'reload schema'");
    }
};
