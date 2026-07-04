<?php

namespace App\Http\Controllers;

use App\Models\Branch;

class BranchController extends Controller
{
    public function index()
    {
        $branches    = Branch::where('status', 'active')->with(['admin', 'operator'])->orderBy('name')->get();
        $branchCount = $branches->count();

        return view('branch.index', compact('branches', 'branchCount'));
    }

    public function show(Branch $branch)
    {
        abort_unless($branch->status === 'active', 404);

        $branch->load(['admin', 'operator']);

        return view('branch.show', compact('branch'));
    }
}
