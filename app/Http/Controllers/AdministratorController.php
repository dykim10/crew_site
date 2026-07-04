<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use Illuminate\View\View;

class AdministratorController extends Controller
{
    public function index(): View
    {
        $members = Administrator::query()
            ->with(['branch', 'user.detail'])
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy('role');

        $roleOrder = array_keys(Administrator::ROLES);

        return view('administrator.index', compact('members', 'roleOrder'));
    }
}
