<?php

namespace App\Services;

use App\Models\Application;
use App\Models\ApplicationForm;
use App\Models\Generation;
use Illuminate\Support\Facades\DB;

/** 기수 ↔ 신청서 연동 — application_form_id 우선, cohort fallback */
class GenerationRecruitmentService
{
    public function resolveApplicationForm(Generation $generation): ?ApplicationForm
    {
        if ($generation->application_form_id) {
            $linked = ApplicationForm::query()->find($generation->application_form_id);
            if ($linked) {
                return $linked;
            }
        }

        return ApplicationForm::query()
            ->where('cohort', "{$generation->number}기")
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->first();
    }

    public function isFormOpen(Generation $generation): bool
    {
        $form = $this->resolveApplicationForm($generation);

        return $form !== null && $form->isOpen();
    }

    /** @return array{pending:int, approved:int, waitlisted:int, total:int} */
    public function applicationStatusCounts(Generation $generation): array
    {
        $form = $this->resolveApplicationForm($generation);
        $formIds = $form
            ? collect([$form->id])
            : ApplicationForm::query()
                ->where('cohort', "{$generation->number}기")
                ->pluck('id');

        if ($formIds->isEmpty()) {
            return ['pending' => 0, 'approved' => 0, 'waitlisted' => 0, 'total' => 0];
        }

        $rows = Application::query()
            ->whereIn('form_id', $formIds)
            ->select('status', DB::raw('count(*) as cnt'))
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $pending    = (int) ($rows['pending'] ?? 0);
        $approved   = (int) ($rows['approved'] ?? 0);
        $waitlisted = (int) ($rows['waitlisted'] ?? 0);

        return [
            'pending'    => $pending,
            'approved'   => $approved,
            'waitlisted' => $waitlisted,
            'total'      => $pending + $approved + $waitlisted,
        ];
    }
}
