<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 구 role 값(crew_admin, group_admin) 기준 제약 → 신규 role 값으로 교체
        DB::statement("ALTER TABLE public.users DROP CONSTRAINT IF EXISTS users_role_check");
        DB::statement("
            ALTER TABLE public.users
            ADD CONSTRAINT users_role_check
            CHECK (role IN ('super_admin', 'region_admin', 'operator', 'member'))
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE public.users DROP CONSTRAINT IF EXISTS users_role_check");
        DB::statement("
            ALTER TABLE public.users
            ADD CONSTRAINT users_role_check
            CHECK (role IN ('super_admin', 'crew_admin', 'group_admin', 'member'))
        ");
    }
};
