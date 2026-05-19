<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // crew_admin → region_admin, group_admin → operator
        DB::table('users')
            ->where('role', 'crew_admin')
            ->update(['role' => 'region_admin']);

        DB::table('users')
            ->where('role', 'group_admin')
            ->update(['role' => 'operator']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'region_admin')
            ->update(['role' => 'crew_admin']);

        DB::table('users')
            ->where('role', 'operator')
            ->update(['role' => 'group_admin']);
    }
};
