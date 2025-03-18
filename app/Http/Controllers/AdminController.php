<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalMatches = FootballMatch::count();
        $totalTickets = Ticket::count();
        $upcomingMatches = FootballMatch::where('match_date', '>', now())
                                      ->orderBy('match_date')
                                      ->take(5)
                                      ->get();

        return view('admin.dashboard', compact('totalUsers', 'totalMatches', 'totalTickets', 'upcomingMatches'));
    }
}
