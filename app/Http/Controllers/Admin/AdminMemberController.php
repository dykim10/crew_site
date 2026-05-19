<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role   = $request->input('role');

        $query = DB::table('users')
            ->orderByRaw("CASE role WHEN 'super_admin' THEN 1 WHEN 'region_admin' THEN 2 WHEN 'operator' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nickname', 'ilike', "%{$search}%")
                  ->orWhere('email_hash', $search);
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        $members = $query->paginate(30)->withQueryString();

        return view('admin.members.index', compact('members', 'search', 'role'));
    }
}
