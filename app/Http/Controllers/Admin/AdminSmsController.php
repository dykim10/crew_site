<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminSmsController extends Controller
{
    public function index()
    {
        return view('admin.sms.index');
    }
}
