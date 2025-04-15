<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        // Create the match
        $match = FootballMatch::create($validated);

        // Create default sections for the stadium based on the SVG diagram
        $this->createDefaultSections($match, $validated['ticket_price']);

        return redirect()->route('admin.matches.index')
            ->with('success', 'Match created successfully. Default sections have been created. You can customize them in the Manage Sections page.');
    }

    /**
     * Create default sections for a match based on the stadium SVG
     */
    private function createDefaultSections(FootballMatch $match, $defaultPrice)
    {
        // Default section IDs from the stadium SVG
        // These must match the IDs or text content visible in the SVG
        $sections = [
            // Numbers (as shown in the text elements in the SVG)
            ['id' => '1', 'name' => 'Section 1', 'type' => 'Standard', 'capacity' => 100],
            ['id' => '2', 'name' => 'Section 2', 'type' => 'Standard', 'capacity' => 100],
            ['id' => '3', 'name' => 'Section 3', 'type' => 'Standard', 'capacity' => 100],
            ['id' => '4', 'name' => 'Section 4', 'type' => 'Standard', 'capacity' => 100],
            ['id' => '5', 'name' => 'Section 5', 'type' => 'Standard', 'capacity' => 100],
            ['id' => '6', 'name' => 'Section 6', 'type' => 'Standard', 'capacity' => 150],
            ['id' => '7', 'name' => 'Section 7', 'type' => 'Standard', 'capacity' => 100],
            ['id' => '8', 'name' => 'Section 8', 'type' => 'Standard', 'capacity' => 100],
            ['id' => '9', 'name' => 'Section 9', 'type' => 'Standard', 'capacity' => 100],
            ['id' => '10', 'name' => 'Section 10', 'type' => 'Standard', 'capacity' => 100],
            ['id' => '11', 'name' => 'Section 11', 'type' => 'Standard', 'capacity' => 200],
            ['id' => '13', 'name' => 'Section 13', 'type' => 'Premium', 'capacity' => 80],
            ['id' => '17', 'name' => 'Section 17', 'type' => 'Premium', 'capacity' => 80],
            ['id' => '19', 'name' => 'Section 19', 'type' => 'Premium', 'capacity' => 80],
            ['id' => '20', 'name' => 'Section 20', 'type' => 'Premium', 'capacity' => 80],
            ['id' => '27', 'name' => 'Section 27', 'type' => 'Premium', 'capacity' => 80],

            // Letters (as shown in the text elements in the SVG)
            ['id' => 'A', 'name' => 'Section A', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'B', 'name' => 'Section B', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'C', 'name' => 'Section C', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'D', 'name' => 'Section D', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'E', 'name' => 'Section E', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'F', 'name' => 'Section F', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'G', 'name' => 'Section G', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'H', 'name' => 'Section H', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'K', 'name' => 'Section K', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'L', 'name' => 'Section L', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'L2', 'name' => 'Section L2', 'type' => 'VIP', 'capacity' => 80],
            ['id' => 'L3', 'name' => 'Section L3', 'type' => 'VIP', 'capacity' => 80],
            ['id' => 'M', 'name' => 'Section M', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'P', 'name' => 'Section P', 'type' => 'VIP', 'capacity' => 120],
            ['id' => 'E2', 'name' => 'Section E2', 'type' => 'VIP', 'capacity' => 80],
            ['id' => 'E3', 'name' => 'Section E3', 'type' => 'VIP', 'capacity' => 80],
        ];

        // Set price adjustments based on section type
        $priceMultipliers = [
            'Standard' => 1,
            'VIP' => 1.5,
            'Premium' => 2
        ];

        // Create each section
        foreach ($sections as $section) {
            // Calculate price based on type
            $price = $defaultPrice * $priceMultipliers[$section['type']];

            // Create the section
            $match->sections()->create([
                'section_id' => $section['id'],
                'name' => $section['name'],
                'capacity' => $section['capacity'],
                'available_seats' => $section['capacity'], // Initially all seats are available
                'price' => $price,
                'section_type' => $section['type'],
                'view_360_url' => null, // Admin can set this later
                'is_active' => true,
            ]);
        }
    }

    public function show(FootballMatch $match)
    {
        return view('admin.matches.show', compact('match'));
    }

    public function edit(FootballMatch $match)
    {
        // Load sections for this match
        $sections = $match->sections()->get();

        return view('admin.matches.edit', compact('match', 'sections'));
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

        // Check if we have sections, if not, create them
        if ($match->sections()->count() === 0) {
            $this->createDefaultSections($match, $validated['ticket_price']);
            $successMessage = 'Match updated successfully. Default sections have been created.';
        } else {
            $successMessage = 'Match updated successfully.';
        }

        $match->update($validated);

        return redirect()->route('admin.matches.index')
            ->with('success', $successMessage);
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

    // Add a new method to manage sections
    public function manageSections(FootballMatch $match)
    {
        $sections = $match->sections;

        return view('admin.matches.sections', compact('match', 'sections'));
    }

    // Add a new method to add/edit sections
    public function storeSections(Request $request, FootballMatch $match)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.section_id' => 'required|string',
            'sections.*.name' => 'required|string',
            'sections.*.capacity' => 'required|integer|min:1',
            'sections.*.available_seats' => 'required|integer|min:0',
            'sections.*.price' => 'required|numeric|min:0',
            'sections.*.section_type' => 'required|in:Standard,VIP,Premium',
            'sections.*.view_360_url' => 'nullable|string',
            'sections.*.is_active' => 'nullable',
        ]);

        foreach ($request->sections as $sectionData) {
            // Debug logging
            Log::info('Section data for ' . $sectionData['section_id'], $sectionData);

            $match->sections()->updateOrCreate(
                ['section_id' => $sectionData['section_id']],
                [
                    'name' => $sectionData['name'],
                    'capacity' => $sectionData['capacity'],
                    'available_seats' => $sectionData['available_seats'],
                    'price' => $sectionData['price'],
                    'section_type' => $sectionData['section_type'],
                    'view_360_url' => $sectionData['view_360_url'] ?? null,
                    'is_active' => isset($sectionData['is_active']),
                ]
            );
        }

        return redirect()->route('admin.matches.sections', $match)
            ->with('success', 'Sections updated successfully.');
    }

    // Add a method to delete a section
    public function deleteSection(FootballMatch $match, $sectionId)
    {
        $match->sections()->where('section_id', $sectionId)->delete();

        return redirect()->route('admin.matches.sections', $match)
            ->with('success', 'Section deleted successfully.');
    }

    public function upload360(Request $request, FootballMatch $match)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // 10MB max
            'section_index' => 'required|integer'
        ]);

        if ($request->hasFile('file')) {
            // Store the file in the 360-views directory
            $path = $request->file('file')->store('360-views', 'public');

            // Generate the public URL
            $url = asset('storage/' . $path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'message' => 'Image uploaded successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to upload image'
        ], 400);
    }
}
