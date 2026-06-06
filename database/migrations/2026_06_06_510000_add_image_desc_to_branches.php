<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE public.branches ADD COLUMN IF NOT EXISTS image_url   TEXT NULL");
        DB::statement("ALTER TABLE public.branches ADD COLUMN IF NOT EXISTS branch_desc VARCHAR(300) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE public.branches DROP COLUMN IF EXISTS image_url");
        DB::statement("ALTER TABLE public.branches DROP COLUMN IF EXISTS branch_desc");
    }
};
