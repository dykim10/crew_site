<?php

namespace App\Http\Controllers;

use App\Models\Notice;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::where('target_type', 'all')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('notices.index', compact('notices'));
    }

    public function show(Notice $notice)
    {
        return view('notices.show', compact('notice'));
    }
}
