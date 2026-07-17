<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('generations', 'main_races')) {
            Schema::table('generations', function (Blueprint $table) {
                $table->jsonb('main_races')->nullable();
            });
        }

        // 단건 → 배열 1건 이관 (멱등)
        DB::statement("
            UPDATE generations
            SET main_races = jsonb_build_array(
                jsonb_build_object(
                    'race_id', main_race_id,
                    'name', COALESCE(NULLIF(TRIM(main_race_name), ''), '')
                )
            )
            WHERE main_races IS NULL
              AND (main_race_id IS NOT NULL OR (main_race_name IS NOT NULL AND TRIM(main_race_name) <> ''))
        ");

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        if (Schema::hasColumn('generations', 'main_races')) {
            Schema::table('generations', function (Blueprint $table) {
                $table->dropColumn('main_races');
            });
            DB::statement("NOTIFY pgrst, 'reload schema'");
        }
    }
};
