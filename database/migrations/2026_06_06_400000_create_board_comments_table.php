<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.board_comments CASCADE');

        DB::statement("
            CREATE TABLE crew.board_comments (
                id         BIGSERIAL PRIMARY KEY,
                board_id   BIGINT         NOT NULL REFERENCES crew.boards(id) ON DELETE CASCADE,
                user_id    BIGINT         NOT NULL,
                content    TEXT           NOT NULL,
                parent_id  BIGINT,
                created_at TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                deleted_at TIMESTAMPTZ(6)
            )
        ");

        DB::statement('CREATE INDEX idx_comments_board ON crew.board_comments(board_id)');
        DB::statement('CREATE INDEX idx_comments_user  ON crew.board_comments(user_id)');
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.board_comments CASCADE');
    }
};
