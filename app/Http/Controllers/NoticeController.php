<?php

namespace App\Http\Controllers;

use App\Models\Notice;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::where(function ($q) {
                $q->where('target_type', 'all');
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('notices.index', compact('notices'));
    }
}
