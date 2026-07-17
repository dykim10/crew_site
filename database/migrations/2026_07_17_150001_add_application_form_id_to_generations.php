<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('generations', 'application_form_id')) {
            Schema::table('generations', function (Blueprint $table) {
                $table->unsignedBigInteger('application_form_id')->nullable();
                $table->index('application_form_id');
            });
        }

        $forms = DB::table('crew.application_forms')
            ->whereNotNull('cohort')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->get(['id', 'cohort', 'is_active']);

        $bestByNumber = [];
        foreach ($forms as $form) {
            if (! preg_match('/(\d+)/', (string) $form->cohort, $m)) {
                continue;
            }
            $num = (int) $m[1];
            if (! isset($bestByNumber[$num])) {
                $bestByNumber[$num] = (int) $form->id;
            }
        }

        foreach ($bestByNumber as $number => $formId) {
            DB::table('generations')
                ->where('number', $number)
                ->whereNull('application_form_id')
                ->update(['application_form_id' => $formId]);
        }

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }

    public function down(): void
    {
        if (Schema::hasColumn('generations', 'application_form_id')) {
            Schema::table('generations', function (Blueprint $table) {
                $table->dropColumn('application_form_id');
            });
        }

        DB::statement("NOTIFY pgrst, 'reload schema'");
    }
};
