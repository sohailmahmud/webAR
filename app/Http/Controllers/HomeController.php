<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $installedFile = dirname(__DIR__, 3) . '/storage/installed';
        if(!file_exists($installedFile)) {
            return redirect('/install');
        }
        return view('auth.login');
    }
}