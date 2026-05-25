<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crew.sms_logs')) {
            Schema::create('crew.sms_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->string('target_type', 20)->default('all'); // all/generation
                $table->unsignedBigInteger('target_id')->nullable();  // generation_id
                $table->integer('recipient_count')->default(0);
                $table->text('message');
                $table->jsonb('result')->nullable();
                $table->timestampsTz(6);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crew.sms_logs');
    }
};
