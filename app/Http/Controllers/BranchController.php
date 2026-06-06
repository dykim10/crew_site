<?php

namespace App\Http\Controllers;

use App\Models\Branch;

class BranchController extends Controller
{
    public function index()
    {
        $branches    = Branch::where('status', 'active')->with('admin')->orderBy('name')->get();
        $branchCount = $branches->count();

        return view('branch.index', compact('branches', 'branchCount'));
    }
}
