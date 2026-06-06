<?php

namespace App\Http\Controllers;

use App\Services\BugReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BugReportController extends Controller
{
    public function __construct(private BugReportService $service) {}

    public function create(): View
    {
        return view('bug-reports.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:200',
            'path'       => 'required|string|max:500',
            'description'=> 'required|string|max:5000',
            'severity'   => 'nullable|in:low,medium,high',
            'screenshot' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'title.required'       => '제목을 입력해주세요.',
            'path.required'        => '발생 경로를 입력해주세요.',
            'description.required' => '재현 방법을 입력해주세요.',
            'severity.in'          => '심각도는 low / medium / high 중 하나여야 합니다.',
            'screenshot.mimes'     => '이미지 파일(jpg, png, webp)만 업로드 가능합니다.',
            'screenshot.max'       => '파일 크기는 10MB 이하여야 합니다.',
        ]);

        $this->service->create($validated, auth()->user(), $request->file('screenshot'));

        return redirect()->route('bug-reports.create')
            ->with('success', '버그가 접수되었습니다. 빠르게 처리하겠습니다.');
    }
}
