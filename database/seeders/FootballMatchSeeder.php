<?php // This should be the first line

namespace Database\Seeders; // <--- MUST match the directory structure

use Illuminate\Database\Seeder;
// Add other use statements needed in this specific seeder (like App\Models\FootballMatch;)
use App\Models\FootballMatch; // <-- You need this assuming you use the model

class FootballMatchSeeder extends Seeder // <--- MUST match the filename and use statement in DatabaseSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Your seeding logic goes here
         FootballMatch::truncate(); // Recommended for migrate:refresh --seed

         FootballMatch::create([
            'home_team' => 'Raja CA',
            'away_team' => 'Wydad AC',
            'match_time' => \Carbon\Carbon::parse('2024-07-20 20:00:00'),
            'ticket_price' => 150.00,
        ]);

         FootballMatch::create([
            'home_team' => 'AS FAR',
            'away_team' => 'RS Berkane',
            'match_time' => \Carbon\Carbon::parse('2024-07-21 18:00:00'),
            'ticket_price' => 120.00,
        ]);
        // ... add more matches ...
    }
}