<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // email → email_enc 컬럼명 변경 + 타입을 TEXT로 변경 (암호화 값 저장)
        DB::statement("ALTER TABLE crew.event_registrations RENAME COLUMN email TO email_enc");
        DB::statement("ALTER TABLE crew.event_registrations ALTER COLUMN email_enc TYPE TEXT");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE crew.event_registrations RENAME COLUMN email_enc TO email");
        DB::statement("ALTER TABLE crew.event_registrations ALTER COLUMN email TYPE VARCHAR(255)");
    }
};
