<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 재설계: type→board_type, author_id→user_id, 불필요 컬럼 제거, is_secret·deleted_at 추가
        DB::statement('DROP TABLE IF EXISTS crew.boards CASCADE');

        DB::statement("
            CREATE TABLE crew.boards (
                id           BIGSERIAL PRIMARY KEY,
                crew_id      BIGINT         NOT NULL DEFAULT 1,
                board_type   VARCHAR(20)    NOT NULL,
                user_id      BIGINT         NOT NULL,
                title        VARCHAR(200)   NOT NULL,
                content      TEXT           NOT NULL,
                view_count   INTEGER        NOT NULL DEFAULT 0,
                is_secret    BOOLEAN        NOT NULL DEFAULT FALSE,
                created_at   TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at   TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                deleted_at   TIMESTAMPTZ(6),

                CONSTRAINT chk_board_type CHECK (board_type IN ('free', 'qna'))
            )
        ");

        DB::statement('CREATE INDEX idx_boards_type       ON crew.boards(board_type)');
        DB::statement('CREATE INDEX idx_boards_user       ON crew.boards(user_id)');
        DB::statement('CREATE INDEX idx_boards_created    ON crew.boards(created_at DESC)');
        DB::statement('CREATE INDEX idx_boards_softdelete ON crew.boards(deleted_at) WHERE deleted_at IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.boards CASCADE');
    }
};
