<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MatchController extends Controller
{
    public function index()
    {
        $matches = FootballMatch::latest()->paginate(10);
        return view('admin.matches.index', compact('matches'));
    }

    public function create()
    {
        return view('admin.matches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'home_team' => 'required|string|max:255',
            'away_team' => 'required|string|max:255',
            'match_date' => 'required|date',
            'match_time' => 'required|date_format:H:i',
            'stadium' => 'required|string|max:255',
            'stadium_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ticket_type' => 'required|in:Standard,VIP,Premium',
            'ticket_price' => 'required|numeric|min:0',
            'available_tickets' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'match_status' => 'required|string|in:scheduled,live,completed,cancelled'
        ]);

        // Handle stadium image upload
        if ($request->hasFile('stadium_image')) {
            $imagePath = $request->file('stadium_image')->store('stadium-images', 'public');
            $validated['stadium_image'] = $imagePath;
            
        }

        // Combine date and time
        $validated['match_date'] = date('Y-m-d H:i:s', strtotime($validated['match_date'] . ' ' . $validated['match_time']));
        unset($validated['match_time']);

        FootballMatch::create($validated);

        return redirect()->route('admin.matches.index')
            ->with('success', 'Match created successfully.');
    }

    public function show(FootballMatch $match)
    {
        return view('admin.matches.show', compact('match'));
    }

    public function edit(FootballMatch $match)
    {
        // Split the datetime for the form
        $match->match_time = date('H:i', strtotime($match->match_date));
        $match->match_date = date('Y-m-d', strtotime($match->match_date));
        
        return view('admin.matches.edit', compact('match'));
    }

    public function update(Request $request, FootballMatch $match)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'home_team' => 'required|string|max:255',
            'away_team' => 'required|string|max:255',
            'match_date' => 'required|date',
            'match_time' => 'required|date_format:H:i',
            'stadium' => 'required|string|max:255',
            'stadium_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ticket_type' => 'required|in:Standard,VIP,Premium',
            'ticket_price' => 'required|numeric|min:0',
            'available_tickets' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'match_status' => 'required|string|in:scheduled,live,completed,cancelled'
        ]);

        // Handle stadium image upload
        if ($request->hasFile('stadium_image')) {
            // Delete old image if exists
            if ($match->stadium_image) {
                Storage::disk('public')->delete($match->stadium_image);
            }
            $imagePath = $request->file('stadium_image')->store('stadium-images', 'public');
            $validated['stadium_image'] = $imagePath;
        }

        // Combine date and time
        $validated['match_date'] = date('Y-m-d H:i:s', strtotime($validated['match_date'] . ' ' . $validated['match_time']));
        unset($validated['match_time']);

        $match->update($validated);

        return redirect()->route('admin.matches.index')
            ->with('success', 'Match updated successfully.');
    }

    public function destroy(FootballMatch $match)
    {
        // Delete stadium image if exists
        if ($match->stadium_image) {
            Storage::disk('public')->delete($match->stadium_image);
        }

        $match->delete();

        return redirect()->route('admin.matches.index')
            ->with('success', 'Match deleted successfully.');
    }
}
