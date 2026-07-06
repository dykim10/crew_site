<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** 훈련노트 — 목표 대회·코스 (users_detail.training_goal JSONB) */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::connection('pgsql')->hasColumn('crew.users_detail', 'training_goal')) {
            DB::statement('ALTER TABLE crew.users_detail ADD COLUMN training_goal JSONB NULL');
        }
    }

    public function down(): void
    {
        if (Schema::connection('pgsql')->hasColumn('crew.users_detail', 'training_goal')) {
            DB::statement('ALTER TABLE crew.users_detail DROP COLUMN training_goal');
        }
    }
};
