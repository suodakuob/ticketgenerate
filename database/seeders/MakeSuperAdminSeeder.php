<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class MakeSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::where('id', 1)->update([
            'role' => 'super_admin'
        ]);

        $this->command->info('User with ID 1 has been made super admin successfully!');
    }
}
