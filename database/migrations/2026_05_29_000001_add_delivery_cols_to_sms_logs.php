<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew.sms_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('crew.sms_logs', 'group_id')) {
                $table->string('group_id', 50)->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('crew.sms_logs', 'delivered_cnt')) {
                $table->integer('delivered_cnt')->default(0)->after('recipient_cnt');
            }
            if (!Schema::hasColumn('crew.sms_logs', 'failed_cnt')) {
                $table->integer('failed_cnt')->default(0)->after('delivered_cnt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('crew.sms_logs', function (Blueprint $table) {
            $table->dropColumn(['group_id', 'delivered_cnt', 'failed_cnt']);
        });
    }
};
