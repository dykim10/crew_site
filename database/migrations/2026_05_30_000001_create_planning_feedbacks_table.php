<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crew.planning_feedbacks')) {
            DB::statement('
                CREATE TABLE crew.planning_feedbacks (
                    id            BIGSERIAL PRIMARY KEY,
                    document_key  VARCHAR(100)  NOT NULL DEFAULT \'event_a_type\',
                    reviewer_name VARCHAR(100),
                    good_points   TEXT,
                    bad_points    TEXT,
                    questions     TEXT,
                    created_at    TIMESTAMPTZ(6) DEFAULT now()
                )
            ');
        }
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.planning_feedbacks');
    }
};
