<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $user = auth()->user();
        $activities = $user && $user->isAdmin()
            ? ActivityLog::with('user')->latest('id')->take(6)->get()
            : collect();

        return view('home', compact('activities'));
    }
}
