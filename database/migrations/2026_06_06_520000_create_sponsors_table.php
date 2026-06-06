<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("GRANT USAGE ON SCHEMA crew TO postgres, anon, authenticated, service_role");

        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.sponsors (
                id          BIGSERIAL       NOT NULL,
                name        VARCHAR(100)    NOT NULL,
                logo_url    TEXT            NULL,
                link_url    TEXT            NULL,
                description VARCHAR(300)    NULL,
                sort_order  SMALLINT        NOT NULL DEFAULT 0,
                is_active   BOOLEAN         NOT NULL DEFAULT TRUE,
                created_at  TIMESTAMPTZ(6)  NOT NULL DEFAULT now(),
                updated_at  TIMESTAMPTZ(6)  NOT NULL DEFAULT now(),
                CONSTRAINT sponsors_pkey PRIMARY KEY (id)
            )
        ");

        DB::statement("
            GRANT ALL ON crew.sponsors TO postgres, anon, authenticated, service_role
        ");

        DB::statement("
            GRANT USAGE, SELECT ON SEQUENCE crew.sponsors_id_seq
                TO postgres, anon, authenticated, service_role
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS crew.sponsors");
    }
};
