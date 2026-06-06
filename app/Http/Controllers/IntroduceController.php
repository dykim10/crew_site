<?php

namespace App\Http\Controllers;

use App\Models\Branch;

class IntroduceController extends Controller
{
    public function index()
    {
        $branchCount = Branch::where('status', 'active')->count();

        return view('introduce.index', compact('branchCount'));
    }
}
