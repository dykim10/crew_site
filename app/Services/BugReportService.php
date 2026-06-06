<?php

namespace App\Services;

use App\Models\BugReport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BugReportService
{
    public function create(array $validated, User $user, ?UploadedFile $screenshot = null): BugReport
    {
        $screenshotUrl = null;

        if ($screenshot) {
            $path          = 'bug-reports/' . date('Y/m') . '/' . Str::uuid() . '.' . $screenshot->extension();
            Storage::disk('s3')->put($path, $screenshot->get());
            $screenshotUrl = Storage::disk('s3')->url($path);
        }

        return BugReport::create([
            'user_id'        => $user->id,
            'title'          => $validated['title'],
            'path'           => $validated['path'],
            'description'    => $validated['description'],
            'severity'       => $validated['severity'] ?? 'medium',
            'status'         => 'open',
            'screenshot_url' => $screenshotUrl,
        ]);
    }

    public function updateByAdmin(BugReport $report, array $data, User $admin): BugReport
    {
        $updates = [
            'status'     => $data['status'],
            'admin_note' => $data['admin_note'] ?? null,
        ];

        if ($data['status'] === 'resolved' && !$report->resolved_at) {
            $updates['resolved_by'] = $admin->id;
            $updates['resolved_at'] = now();
        }

        $report->update($updates);
        return $report->fresh();
    }
}
