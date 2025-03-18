<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FootballMatch;
use Carbon\Carbon;

class MatchesTableSeeder extends Seeder
{
    public function run()
    {
        $matches = [
            [
                'name' => 'Premier League: Manchester United vs Liverpool',
                'home_team' => 'Manchester United',
                'away_team' => 'Liverpool',
                'match_date' => Carbon::create(2025, 2, 23, 20, 22),
                'stadium' => 'Old Trafford',
                'ticket_price' => 50.00,
                'ticket_type' => 'Standard',
                'available_tickets' => 100,
                'description' => 'Experience one of the biggest rivalries in football at the iconic Old Trafford stadium.',
            ],
            [
                'name' => 'Premier League: Arsenal vs Chelsea',
                'home_team' => 'Arsenal',
                'away_team' => 'Chelsea',
                'match_date' => Carbon::create(2025, 2, 25, 20, 22),
                'stadium' => 'Emirates Stadium',
                'ticket_price' => 45.00,
                'ticket_type' => 'Standard',
                'available_tickets' => 150,
                'description' => 'London derby at the Emirates Stadium - Arsenal takes on Chelsea in a crucial Premier League match.',
            ],
            [
                'name' => 'Premier League: Manchester City vs Tottenham',
                'home_team' => 'Manchester City',
                'away_team' => 'Tottenham',
                'match_date' => Carbon::create(2025, 2, 28, 20, 22),
                'stadium' => 'Etihad Stadium',
                'ticket_price' => 55.00,
                'ticket_type' => 'Standard',
                'available_tickets' => 120,
                'description' => 'Watch the defending champions Manchester City face Tottenham in an exciting Premier League clash.',
            ],
        ];

        foreach ($matches as $match) {
            FootballMatch::create($match);
        }
    }
}
