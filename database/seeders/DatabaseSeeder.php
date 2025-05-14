<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\MatchSeeder;
use Database\Seeders\MatchesTableSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\FootballMatchSeeder; // <--- This USE statement is present!

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            FootballMatchSeeder::class, // <--- This is where the error is reported
            MatchSeeder::class,
            MatchesTableSeeder::class,
        ]);

        // User::factory(10)->create(); // This is commented out
        // This factory call might also cause duplicate user issues if not handled.
        // Since you're calling AdminUserSeeder which might create a user,
        // and then creating another factory user, ensure their emails are unique or
        // uncomment the User::truncate() in your UserSeeder/AdminUserSeeder if using migrate:refresh --seed

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com', // <--- Ensure this email is unique
        ]);


    }
}