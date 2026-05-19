<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminStatsController extends Controller
{
    public function index()
    {
        return view('admin.stats.index');
    }

    public function export()
    {
        return view('admin.export.index');
    }
}
