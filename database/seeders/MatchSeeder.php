<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use Illuminate\Database\Seeder;

class MatchSeeder extends Seeder
{
    public function run()
    {
        $matches = [
            [
                'name' => 'Premier League: Manchester United vs Liverpool',
                'home_team' => 'Manchester United',
                'away_team' => 'Liverpool',
                'match_date' => now()->addDays(5),
                'stadium' => 'Old Trafford',
                'ticket_price' => 50.00,
                'ticket_type' => 'Standard',
                'available_tickets' => 1000,
                'description' => 'A thrilling Premier League match between two historic rivals.',
            ],
            [
                'name' => 'Premier League: Arsenal vs Chelsea',
                'home_team' => 'Arsenal',
                'away_team' => 'Chelsea',
                'match_date' => now()->addDays(7),
                'stadium' => 'Emirates Stadium',
                'ticket_price' => 45.00,
                'ticket_type' => 'Standard',
                'available_tickets' => 800,
                'description' => 'London derby featuring two of the city\'s biggest clubs.',
            ],
            [
                'name' => 'Premier League: Manchester City vs Tottenham',
                'home_team' => 'Manchester City',
                'away_team' => 'Tottenham',
                'match_date' => now()->addDays(10),
                'stadium' => 'Etihad Stadium',
                'ticket_price' => 55.00,
                'ticket_type' => 'Standard',
                'available_tickets' => 900,
                'description' => 'An exciting match between two attacking teams.',
            ],
        ];

        foreach ($matches as $match) {
            FootballMatch::create($match);
        }
    }
}
