<?php

namespace App\Services;

use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Carbon;

class ApplicationMatchingService
{
    public function __construct(private CryptoService $crypto) {}

    /** 신청 저장 직후 — 동일 email_hash 회원 연결 */
    public function matchApplication(Application $app): void
    {
        if ($app->matched_user_id || ! $app->email_hash) {
            return;
        }

        $user = User::where('email_hash', $app->email_hash)->first();
        if (! $user) {
            return;
        }

        $app->update([
            'matched_user_id' => $user->id,
            'matched_at'      => Carbon::now(),
            'matched_by'      => 'auto',
        ]);

        app(GenerationEnrollmentService::class)->syncFromApplication($app->fresh());
    }

    /** 가입 직후 — 미연결 신청 전부를 연결 */
    public function matchUser(User $user): int
    {
        if (! $user->email_hash) {
            return 0;
        }

        $apps = Application::query()
            ->where('email_hash', $user->email_hash)
            ->whereNull('matched_user_id')
            ->get();

        $enrollment = app(GenerationEnrollmentService::class);

        foreach ($apps as $app) {
            $app->update([
                'matched_user_id' => $user->id,
                'matched_at'      => Carbon::now(),
                'matched_by'      => 'auto',
            ]);
            $enrollment->syncFromApplication($app->fresh());
        }

        return $apps->count();
    }

    public function linkManually(Application $app, User $user): void
    {
        $app->update([
            'matched_user_id' => $user->id,
            'matched_at'      => Carbon::now(),
            'matched_by'      => 'admin',
        ]);

        app(GenerationEnrollmentService::class)->syncFromApplication($app->fresh());
    }

    public function unlink(Application $app): void
    {
        $app->update([
            'matched_user_id' => null,
            'matched_at'      => null,
            'matched_by'      => null,
        ]);
    }
}
