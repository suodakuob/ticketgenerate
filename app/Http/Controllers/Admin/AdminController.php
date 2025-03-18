<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $stats = [
            'total_matches' => FootballMatch::count(),
            'upcoming_matches' => FootballMatch::where('match_date', '>', now())->count(),
            'total_tickets' => Ticket::count(),
            'total_sales' => Ticket::sum('price'),
        ];

        $latest_matches = FootballMatch::orderBy('match_date', 'desc')
                                    ->take(5)
                                    ->get();

        return view('admin.dashboard', compact('stats', 'latest_matches'));
    }
}
