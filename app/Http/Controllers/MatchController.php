<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Section;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $matches = FootballMatch::where('match_date', '>', now())
            ->orderBy('match_date')
            ->paginate(12);

        return view('matches.index', compact('matches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(FootballMatch $match)
    {
        // Get available sections for this match
        $sections = $match->sections()->where('is_active', true)->get();

        // Transform sections into a format easier to use in JavaScript
        $sectionData = [];
        foreach ($sections as $section) {
            $sectionData[$section->section_id] = [
                'id' => $section->id,
                'section_id' => $section->section_id,
                'name' => $section->name,
                'capacity' => $section->capacity,
                'available_seats' => $section->available_seats,
                'price' => $section->price,
                'section_type' => $section->section_type,
                'view_360_url' => $section->view_360_url,
                'is_active' => $section->is_active,
            ];
        }

        return view('matches.show', compact('match', 'sections', 'sectionData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Display the sections for the specified match.
     */
    public function sections(FootballMatch $match)
    {
        // Get all sections for this match
        $sections = $match->sections()->get();

        return view('matches.sections', compact('match', 'sections'));
    }
}
