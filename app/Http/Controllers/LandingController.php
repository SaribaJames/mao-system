<?php

namespace App\Http\Controllers;

use App\Models\ProgramAchievement;

class LandingController extends Controller
{
    public function index()
    {
        $achievements = ProgramAchievement::with('program')
            ->latest()
            ->take(9)
            ->get();

        return view('landing', compact('achievements'));
    }
}