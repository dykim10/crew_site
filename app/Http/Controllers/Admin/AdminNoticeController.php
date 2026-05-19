<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class AdminNoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::orderByDesc('is_pinned')->orderByDesc('created_at')->paginate(20);
        return view('admin.notices.index', compact('notices'));
    }

    public function create()
    {
        return view('admin.notices.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:200',
            'content'     => 'required|string',
            'is_pinned'   => 'boolean',
            'target_type' => 'required|in:all,region',
        ]);

        Notice::create([
            'title'       => $validated['title'],
            'content'     => $validated['content'],
            'author_id'   => auth()->id(),
            'is_pinned'   => $request->boolean('is_pinned'),
            'target_type' => $validated['target_type'],
            'target_ids'  => [],
        ]);

        return redirect()->route('admin.notices.index')->with('success', '공지사항이 등록되었습니다.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return redirect()->route('admin.notices.index')->with('success', '공지사항이 삭제되었습니다.');
    }
}
