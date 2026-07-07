<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** 예약 문자 발송 (260707 TASK-scheduled-sms) */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.scheduled_sms (
                id              BIGSERIAL PRIMARY KEY,
                title           VARCHAR(200) NOT NULL,
                message_body    TEXT NOT NULL,
                sender_number   VARCHAR(20) NOT NULL,
                scheduled_at    TIMESTAMPTZ(6) NOT NULL,
                status          VARCHAR(20) NOT NULL DEFAULT 'pending',
                test_sent_at    TIMESTAMPTZ(6) NULL,
                sent_at         TIMESTAMPTZ(6) NULL,
                solapi_group_id VARCHAR(100) NULL,
                error_message   TEXT NULL,
                created_by      BIGINT NOT NULL,
                canceled_by     BIGINT NULL,
                canceled_at     TIMESTAMPTZ(6) NULL,
                created_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at      TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_scheduled_sms_status_at
            ON crew.scheduled_sms (status, scheduled_at)
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.scheduled_sms_recipients (
                id               BIGSERIAL PRIMARY KEY,
                scheduled_sms_id BIGINT NOT NULL REFERENCES crew.scheduled_sms(id) ON DELETE CASCADE,
                user_id          BIGINT NOT NULL,
                status           VARCHAR(20) NOT NULL DEFAULT 'pending',
                created_at       TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                UNIQUE (scheduled_sms_id, user_id)
            )
        ");
        DB::statement("
            CREATE INDEX IF NOT EXISTS idx_scheduled_sms_recipients_sms
            ON crew.scheduled_sms_recipients (scheduled_sms_id)
        ");

        DB::statement("
            CREATE TABLE IF NOT EXISTS crew.sms_test_recipients (
                id         BIGSERIAL PRIMARY KEY,
                user_id    BIGINT NOT NULL UNIQUE,
                is_active  BOOLEAN NOT NULL DEFAULT true,
                created_at TIMESTAMPTZ(6) NOT NULL DEFAULT now(),
                updated_at TIMESTAMPTZ(6) NOT NULL DEFAULT now()
            )
        ");
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS crew.scheduled_sms_recipients');
        DB::statement('DROP TABLE IF EXISTS crew.scheduled_sms');
        DB::statement('DROP TABLE IF EXISTS crew.sms_test_recipients');
    }
};
