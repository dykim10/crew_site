<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 신청 내역 자체가 기수·지부 편성 원장.
 * 회원가입은 선택 — 회원 연결 없이도 이관 가능.
 * 연결 회원이 있으면 user_generations 에도 동기화.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crew.applications', function (Blueprint $table) {
            if (! Schema::hasColumn('crew.applications', 'generation_id')) {
                $table->unsignedBigInteger('generation_id')->nullable()->index();
            }
            if (! Schema::hasColumn('crew.applications', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->index();
            }
            if (! Schema::hasColumn('crew.applications', 'enrolled_at')) {
                $table->timestampTz('enrolled_at', 6)->nullable();
            }
        });

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        Schema::table('crew.applications', function (Blueprint $table) {
            foreach (['enrolled_at', 'branch_id', 'generation_id'] as $col) {
                if (Schema::hasColumn('crew.applications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
        DB::statement("NOTIFY pgrst, 'reload schema'");
    }
};
