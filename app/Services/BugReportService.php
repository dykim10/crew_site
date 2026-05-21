<?php

namespace App\Services;

use App\Models\BugReport;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BugReportService
{
    public function create(array $validated, User $user, ?UploadedFile $file = null): BugReport
    {
        $attachmentUrl  = null;
        $attachmentName = null;

        if ($file) {
            $path           = $file->store('bug-reports', 'public');
            $attachmentUrl  = Storage::url($path);
            $attachmentName = $file->getClientOriginalName();
        }

        return BugReport::create([
            'user_id'         => $user->id,
            'title'           => $validated['title'],
            'description'     => $validated['description'],
            'priority'        => $validated['priority'] ?? 'medium',
            'status'          => 'pending',
            'attachment_url'  => $attachmentUrl,
            'attachment_name' => $attachmentName,
        ]);
    }

    public function updateByAdmin(BugReport $report, array $data, User $admin): BugReport
    {
        $updates = [
            'status'     => $data['status'],
            'admin_note' => $data['admin_note'] ?? null,
        ];

        if (in_array($data['status'], ['resolved', 'closed']) && !$report->resolved_at) {
            $updates['resolved_by'] = $admin->id;
            $updates['resolved_at'] = now();
        }

        $report->update($updates);
        return $report->fresh();
    }
}
