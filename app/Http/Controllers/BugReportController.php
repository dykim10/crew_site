<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use App\Services\BugReportService;
use Illuminate\Http\Request;

class BugReportController extends Controller
{
    public function __construct(private BugReportService $service) {}

    public function index()
    {
        $reports = BugReport::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('bug-reports.index', compact('reports'));
    }

    public function create()
    {
        return view('bug-reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'description' => 'required|string|max:5000',
            'priority'    => 'required|in:low,medium,high,critical',
            'attachment'  => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:5120',
        ], [
            'title.required'       => '제목을 입력해주세요.',
            'description.required' => '내용을 입력해주세요.',
            'attachment.mimes'     => '이미지(jpg,png,gif,webp) 또는 PDF 파일만 업로드 가능합니다.',
            'attachment.max'       => '파일 크기는 5MB 이하여야 합니다.',
        ]);

        $this->service->create($validated, auth()->user(), $request->file('attachment'));

        return redirect()->route('bug-reports.index')
            ->with('success', '버그가 접수되었습니다. 확인 후 빠르게 처리하겠습니다.');
    }

    public function show(BugReport $bugReport)
    {
        abort_if($bugReport->user_id !== auth()->id(), 403);

        return view('bug-reports.show', compact('bugReport'));
    }
}
