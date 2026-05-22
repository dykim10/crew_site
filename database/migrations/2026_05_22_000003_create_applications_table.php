<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 기존 테이블 정리 (스키마 변경)
        Schema::dropIfExists('crew.applications');
        Schema::dropIfExists('crew.application_forms');

        // 기수 신청 폼 (관리자가 필드를 자유 구성)
        Schema::create('crew.application_forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('cohort', 20)->nullable();
            $table->text('subtitle')->nullable();
            $table->boolean('is_active')->default(false);
            $table->date('open_from')->nullable();
            $table->date('open_until')->nullable();
            $table->jsonb('form_fields')->default('[]');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 기수 신청서 제출 내역
        Schema::create('crew.applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id')->nullable();
            $table->string('name_enc');
            $table->string('email_hash')->index();
            $table->string('email_enc');
            $table->string('phone_enc')->nullable();
            $table->jsonb('field_values')->default('{}');
            $table->string('status', 20)->default('pending');
            $table->text('admin_memo')->nullable();
            $table->boolean('agree_privacy')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crew.applications');
        Schema::dropIfExists('crew.application_forms');
    }
};
