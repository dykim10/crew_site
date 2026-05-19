<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminGenerationController extends Controller
{
    public function index()
    {
        return view('admin.generations.index');
    }
}
