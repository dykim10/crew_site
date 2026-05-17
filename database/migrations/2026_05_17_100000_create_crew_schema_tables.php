<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("CREATE SCHEMA IF NOT EXISTS crew");

        // 이전 부분 실행으로 잘못된 구조의 테이블이 있을 수 있으므로 먼저 제거
        DB::statement("DROP TABLE IF EXISTS crew.event_scores CASCADE");
        DB::statement("DROP TABLE IF EXISTS crew.events CASCADE");
        DB::statement("DROP TABLE IF EXISTS crew.user_goals CASCADE");
        DB::statement("DROP TABLE IF EXISTS crew.running_logs CASCADE");

        // running_logs: 구성원 러닝 기록 (CORE API 파싱 결과 저장)
        DB::statement("
            CREATE TABLE crew.running_logs (
                id                BIGSERIAL PRIMARY KEY,
                user_id           BIGINT NOT NULL,
                group_id          BIGINT,
                run_date          DATE NOT NULL,
                distance_km       NUMERIC(6,2) NOT NULL,
                duration_seconds  INT NOT NULL,
                avg_pace_seconds  INT,
                best_pace_seconds INT,
                is_indoor         BOOLEAN NOT NULL DEFAULT false,
                calories          INT,
                avg_heart_rate    INT,
                elevation_m       NUMERIC(6,1),
                weather_desc      VARCHAR(50),
                image_url         TEXT,
                parsed_data       JSONB,
                memo              TEXT,
                created_at        TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at        TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // events: 이벤트 / 챌린지
        DB::statement("
            CREATE TABLE crew.events (
                id          BIGSERIAL PRIMARY KEY,
                crew_id     BIGINT,
                name        VARCHAR(100) NOT NULL,
                description TEXT,
                start_date  DATE NOT NULL,
                end_date    DATE NOT NULL,
                target_km   NUMERIC(7,2),
                status      VARCHAR(20) NOT NULL DEFAULT 'active',
                created_at  TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at  TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // event_scores: 이벤트별 참가자 점수
        DB::statement("
            CREATE TABLE crew.event_scores (
                id         BIGSERIAL PRIMARY KEY,
                event_id   BIGINT NOT NULL,
                user_id    BIGINT NOT NULL,
                score      NUMERIC(8,2) NOT NULL DEFAULT 0,
                rank       INT,
                memo       TEXT,
                created_at TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // user_goals: 개인 마일리지 목표 (월별 / 연간)
        DB::statement("
            CREATE TABLE crew.user_goals (
                id          BIGSERIAL PRIMARY KEY,
                user_id     BIGINT NOT NULL,
                year        SMALLINT NOT NULL,
                month       SMALLINT,
                target_km   NUMERIC(7,2) NOT NULL,
                achieved_km NUMERIC(7,2) NOT NULL DEFAULT 0,
                is_achieved BOOLEAN NOT NULL DEFAULT false,
                created_at  TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at  TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // 인덱스
        DB::statement("CREATE INDEX idx_running_logs_user_date ON crew.running_logs(user_id, run_date DESC)");
        DB::statement("CREATE INDEX idx_running_logs_group ON crew.running_logs(group_id)");
        DB::statement("CREATE INDEX idx_event_scores_event ON crew.event_scores(event_id)");
        DB::statement("CREATE UNIQUE INDEX idx_event_scores_unique ON crew.event_scores(event_id, user_id)");
        DB::statement("CREATE INDEX idx_user_goals_user_year ON crew.user_goals(user_id, year)");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS crew.event_scores CASCADE");
        DB::statement("DROP TABLE IF EXISTS crew.events CASCADE");
        DB::statement("DROP TABLE IF EXISTS crew.user_goals CASCADE");
        DB::statement("DROP TABLE IF EXISTS crew.running_logs CASCADE");
    }
};
