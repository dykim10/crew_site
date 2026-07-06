<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * 개인 훈련노트 스키마 (260707 TASK-CREW-TRAINING-NOTES)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── running_logs 컬럼 추가 ──
        $logCols = [
            'user_note'      => 'TEXT NULL',
            'coach_feedback' => 'JSONB NULL',
            'feedback_at'    => 'TIMESTAMPTZ(6) NULL',
            'vo2max'         => 'NUMERIC(4,1) NULL',
        ];
        foreach ($logCols as $col => $def) {
            if (!Schema::connection('pgsql')->hasColumn('crew.running_logs', $col)) {
                DB::statement("ALTER TABLE crew.running_logs ADD COLUMN {$col} {$def}");
            }
        }

        // ── training_reports ──
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.training_reports (
                id           BIGSERIAL PRIMARY KEY,
                user_id      BIGINT NOT NULL,
                period_start DATE NOT NULL,
                period_end   DATE NOT NULL,
                report       JSONB NOT NULL,
                created_at   TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                UNIQUE(user_id, period_start)
            )
        ");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_training_reports_user ON crew.training_reports(user_id, period_start DESC)");

        // ── personal_records ──
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.personal_records (
                id              BIGSERIAL PRIMARY KEY,
                user_id         BIGINT NOT NULL,
                distance_type   VARCHAR(10) NOT NULL,
                record_seconds  INT NOT NULL,
                achieved_at     DATE NOT NULL,
                source          VARCHAR(20) NOT NULL DEFAULT 'manual',
                created_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_personal_records_user ON crew.personal_records(user_id, distance_type, achieved_at DESC)");

        // ── training_schedules ──
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.training_schedules (
                id             BIGSERIAL PRIMARY KEY,
                user_id        BIGINT NOT NULL,
                week_start     DATE NOT NULL,
                point_workout  JSONB NOT NULL,
                weekly_volume  JSONB NOT NULL,
                rationale      TEXT NULL,
                matched_log_id BIGINT NULL,
                evaluation     JSONB NULL,
                created_at     TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                UNIQUE(user_id, week_start)
            )
        ");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_training_schedules_user ON crew.training_schedules(user_id, week_start DESC)");

        // ── body_records (암호문만 — CORE INSERT) ──
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.body_records (
                id                   BIGSERIAL PRIMARY KEY,
                user_id              BIGINT NOT NULL,
                measured_at          TIMESTAMPTZ(6) NOT NULL,
                weight_enc           TEXT NOT NULL,
                skeletal_muscle_enc  TEXT NULL,
                body_fat_kg_enc      TEXT NULL,
                bmi_enc              TEXT NULL,
                body_fat_percent_enc TEXT NULL,
                inbody_score_enc     TEXT NULL,
                source               VARCHAR(10) NOT NULL,
                image_url            TEXT NULL,
                created_at           TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_body_records_user ON crew.body_records(user_id, measured_at DESC)");

        // ── users_detail: body_data_consent_at ──
        if (!Schema::connection('pgsql')->hasColumn('crew.users_detail', 'body_data_consent_at')) {
            Schema::connection('pgsql')->table('crew.users_detail', function (Blueprint $table) {
                $table->timestampTz('body_data_consent_at', 6)->nullable();
            });
        }

        foreach (['training_reports', 'personal_records', 'training_schedules', 'body_records'] as $table) {
            DB::statement("GRANT ALL ON crew.{$table} TO postgres, anon, authenticated, service_role");
            DB::statement("GRANT USAGE, SELECT ON SEQUENCE crew.{$table}_id_seq TO postgres, anon, authenticated, service_role");
        }
        DB::statement("SELECT pg_notify('pgrst', 'reload schema')");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.body_records CASCADE');
        DB::statement('DROP TABLE IF EXISTS crew.training_schedules CASCADE');
        DB::statement('DROP TABLE IF EXISTS crew.personal_records CASCADE');
        DB::statement('DROP TABLE IF EXISTS crew.training_reports CASCADE');

        foreach (['user_note', 'coach_feedback', 'feedback_at', 'vo2max'] as $col) {
            if (Schema::connection('pgsql')->hasColumn('crew.running_logs', $col)) {
                Schema::connection('pgsql')->table('crew.running_logs', function (Blueprint $table) use ($col) {
                    $table->dropColumn($col);
                });
            }
        }

        if (Schema::connection('pgsql')->hasColumn('crew.users_detail', 'body_data_consent_at')) {
            Schema::connection('pgsql')->table('crew.users_detail', function (Blueprint $table) {
                $table->dropColumn('body_data_consent_at');
            });
        }
    }
};
