<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::connection('pgsql')->hasColumn('crew.users_detail', 'skin_select')) {
            Schema::connection('pgsql')->table('crew.users_detail', function (Blueprint $table) {
                $table->string('skin_select', 20)->default('_skin_v1')->after('training_group');
            });
        }
    }

    public function down(): void
    {
        if (Schema::connection('pgsql')->hasColumn('crew.users_detail', 'skin_select')) {
            Schema::connection('pgsql')->table('crew.users_detail', function (Blueprint $table) {
                $table->dropColumn('skin_select');
            });
        }
    }
};
