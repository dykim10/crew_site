<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $activeTheme = Setting::get('active_theme', 'v1');

        return view("home.{$activeTheme}", compact('activeTheme'));
    }

    public function switchTheme(Request $request)
    {
        $user = auth()->user();

        abort_unless(
            $user && in_array($user->role, ['super_admin', 'region_admin']),
            403
        );

        $validated = $request->validate(['theme' => 'required|in:v1,v2']);

        Setting::set('active_theme', $validated['theme']);

        return redirect()->route('home');
    }
}
