<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Models\Ticket;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_matches' => FootballMatch::count(),
            'upcoming_matches' => FootballMatch::where('match_date', '>', now())->count(),
            'total_tickets' => Ticket::count(),
            'total_sales' => Payment::where('status', 'completed')->sum('amount'),
        ];

        $latest_matches = FootballMatch::latest()->take(5)->get();
        $latest_tickets = Ticket::with(['user', 'match'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latest_matches', 'latest_tickets'));
    }
}
