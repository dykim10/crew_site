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
            if (! Schema::hasColumn('crew.applications', 'import_source')) {
                $table->string('import_source', 20)->nullable();
            }
            if (! Schema::hasColumn('crew.applications', 'import_key')) {
                $table->string('import_key', 100)->nullable()->unique();
            }
        });

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        Schema::table('crew.applications', function (Blueprint $table) {
            if (Schema::hasColumn('crew.applications', 'import_key')) {
                $table->dropColumn('import_key');
            }
            if (Schema::hasColumn('crew.applications', 'import_source')) {
                $table->dropColumn('import_source');
            }
        });
        DB::statement("NOTIFY pgrst, 'reload schema'");
    }
};
