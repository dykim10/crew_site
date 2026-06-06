<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Supabase 대시보드 직접 생성 테이블 복구 마이그레이션.
 *
 * 원래 마이그레이션 파일 없이 Supabase 대시보드에서 생성됐던 테이블들.
 * migrate:fresh 등으로 삭제된 경우를 대비해 CREATE TABLE IF NOT EXISTS 로 복구.
 * 이후 ALTER 마이그레이션이 정상 실행되도록 기반을 마련한다.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── crew.users_detail ──────────────────────────────────────────────────
        // crew 전용 회원 추가정보 (2026_06_04_200000 에서 skin_select 추가)
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.users_detail (
                id              BIGSERIAL PRIMARY KEY,
                user_id         BIGINT        NOT NULL UNIQUE,
                generation_id   BIGINT,
                region_id       BIGINT,
                grade           VARCHAR(5),
                training_group  VARCHAR(20),
                skin_select     VARCHAR(20)   NOT NULL DEFAULT '_skin_v1',
                badges          JSONB         NOT NULL DEFAULT '{}',
                admin_memo      TEXT,
                join_date       DATE,
                gender          VARCHAR(10),
                shirt_size      VARCHAR(10),
                memo            TEXT,
                group_id        BIGINT,
                created_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        DB::statement("CREATE INDEX IF NOT EXISTS idx_users_detail_user ON crew.users_detail(user_id)");

        // ── crew.event_groups ──────────────────────────────────────────────────
        // A타입 이벤트 지부별 조 그룹 (2026_06_04_100000 에서 branch_id 추가)
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.event_groups (
                id             BIGSERIAL PRIMARY KEY,
                crew_id        BIGINT,
                generation_id  BIGINT,
                branch_id      INTEGER,
                event_id       BIGINT        NOT NULL,
                group_no       SMALLINT      NOT NULL,
                group_name     VARCHAR(50),
                leader_user_id BIGINT,
                created_at     TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at     TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");

        // ── crew.event_group_members ───────────────────────────────────────────
        // 그룹 구성원 (1인 1조 보장 — UNIQUE(group_id, user_id))
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.event_group_members (
                id         BIGSERIAL PRIMARY KEY,
                crew_id    BIGINT,
                group_id   BIGINT        NOT NULL,
                user_id    BIGINT        NOT NULL,
                created_at TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                UNIQUE(group_id, user_id)
            )
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS crew.event_group_members CASCADE");
        DB::statement("DROP TABLE IF EXISTS crew.event_groups CASCADE");
        DB::statement("DROP TABLE IF EXISTS crew.users_detail CASCADE");
    }
};
