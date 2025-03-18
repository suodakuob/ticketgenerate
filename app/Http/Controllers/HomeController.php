<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $matches = FootballMatch::where('match_date', '>', now())
                            ->orderBy('match_date')
                            ->get();
        return view('welcome', [
            'matches' => $matches
        ]);
    }
}
