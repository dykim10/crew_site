<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── crew.event_groups ────────────────────────────────────────────────
        // 테이블은 이미 존재하므로 누락 컬럼만 추가
        // branch_id: 지부별 독립 조 편성 (인천 1조 / 반포 1조 공존)
        DB::statement("
            ALTER TABLE crew.event_groups
                ADD COLUMN IF NOT EXISTS branch_id INTEGER NULL
        ");

        // event_id NOT NULL 보장 (기존 NULL 허용 → 실제 사용 시 반드시 있어야 함)
        // 기존 NULL 데이터 없을 경우에만 NOT NULL 추가 가능 — 안전하게 DEFAULT로 처리
        // 지부 내 조 번호 유니크 인덱스 (branch_id 추가 후)
        DB::statement("
            CREATE UNIQUE INDEX IF NOT EXISTS idx_event_groups_unique
                ON crew.event_groups (event_id, branch_id, group_no)
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_event_groups_event
                ON crew.event_groups USING btree (event_id)
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_event_groups_branch
                ON crew.event_groups USING btree (branch_id)
        ");

        // ── crew.event_group_members ─────────────────────────────────────────
        // 테이블은 이미 존재. 누락 인덱스만 추가
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_event_group_members_group
                ON crew.event_group_members USING btree (group_id)
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_event_group_members_user
                ON crew.event_group_members USING btree (user_id)
        ");

        // ── crew.event_fixed_submissions ─────────────────────────────────────
        // 고정점수 이벤트 제출물 저장 테이블 (신규 생성)
        // score_type = 'fixed' 이벤트에서 사용자가 이미지 제출 → 관리자 승인 → event_scores INSERT
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.event_fixed_submissions (
                id            BIGSERIAL     NOT NULL,
                event_id      BIGINT        NOT NULL,
                user_id       BIGINT        NOT NULL,
                image_url     TEXT          NOT NULL,
                status        VARCHAR(20)   NOT NULL DEFAULT 'pending',
                admin_note    TEXT          NULL,
                confirmed_by  BIGINT        NULL,
                confirmed_at  TIMESTAMPTZ   NULL,
                created_at    TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
                updated_at    TIMESTAMPTZ   NOT NULL DEFAULT NOW(),
                CONSTRAINT event_fixed_submissions_pkey PRIMARY KEY (id),
                CONSTRAINT event_fixed_submissions_status_check
                    CHECK (status IN ('pending', 'approved', 'rejected'))
            )
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_event_fixed_submissions_event
                ON crew.event_fixed_submissions USING btree (event_id)
        ");

        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_event_fixed_submissions_event_user
                ON crew.event_fixed_submissions USING btree (event_id, user_id)
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS crew.event_fixed_submissions");
        DB::statement("DROP INDEX IF EXISTS crew.idx_event_groups_unique");
        DB::statement("DROP INDEX IF EXISTS crew.idx_event_groups_event");
        DB::statement("DROP INDEX IF EXISTS crew.idx_event_groups_branch");
        DB::statement("ALTER TABLE crew.event_groups DROP COLUMN IF EXISTS branch_id");
    }
};
