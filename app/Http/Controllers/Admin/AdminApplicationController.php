<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminApplicationController extends Controller
{
    public function index()
    {
        return view('admin.applications.index');
    }
}
