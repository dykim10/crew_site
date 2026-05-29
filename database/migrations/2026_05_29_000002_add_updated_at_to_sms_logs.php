<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew.sms_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('crew.sms_logs', 'updated_at')) {
                $table->timestampTz('updated_at', 6)->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('crew.sms_logs', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
