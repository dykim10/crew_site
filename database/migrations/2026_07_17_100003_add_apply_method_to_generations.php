<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('generations', 'apply_method')) {
            Schema::table('generations', function (Blueprint $table) {
                $table->string('apply_method', 20)->default('internal');
            });
        }

        if (! Schema::hasColumn('generations', 'google_form_id')) {
            Schema::table('generations', function (Blueprint $table) {
                $table->unsignedBigInteger('google_form_id')->nullable();
            });
        }

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        Schema::table('generations', function (Blueprint $table) {
            if (Schema::hasColumn('generations', 'google_form_id')) {
                $table->dropColumn('google_form_id');
            }
            if (Schema::hasColumn('generations', 'apply_method')) {
                $table->dropColumn('apply_method');
            }
        });
        DB::statement("NOTIFY pgrst, 'reload schema'");
    }
};
