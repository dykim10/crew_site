<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("GRANT USAGE ON SCHEMA crew TO postgres, anon, authenticated, service_role");

        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.administrators (
                id              BIGSERIAL       NOT NULL,
                name            VARCHAR(100)    NOT NULL,
                profile_image   TEXT            NULL,
                instagram_url   TEXT            NULL,
                youtube_url     TEXT            NULL,
                bio             TEXT            NULL,
                branch_id       BIGINT          NULL,
                branch_custom   VARCHAR(50)     NULL,
                role            VARCHAR(20)     NOT NULL DEFAULT 'crew_ops',
                sort_order      SMALLINT        NOT NULL DEFAULT 0,
                is_active       BOOLEAN         NOT NULL DEFAULT TRUE,
                created_at      TIMESTAMPTZ(6)  NOT NULL DEFAULT now(),
                updated_at      TIMESTAMPTZ(6)  NOT NULL DEFAULT now(),
                CONSTRAINT administrators_pkey PRIMARY KEY (id),
                CONSTRAINT administrators_role_check CHECK (role IN ('branch_leader', 'crew_ops', 'photo', 'other'))
            )
        ");

        DB::statement("
            GRANT ALL ON crew.administrators TO postgres, anon, authenticated, service_role
        ");

        DB::statement("
            GRANT USAGE, SELECT ON SEQUENCE crew.administrators_id_seq
                TO postgres, anon, authenticated, service_role
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.administrators');
    }
};
