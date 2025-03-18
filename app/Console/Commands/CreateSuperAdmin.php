<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    protected $signature = 'admin:create-super';
    protected $description = 'Create a super admin user';

    public function handle()
    {
        $name = $this->ask('Enter super admin name');
        $email = $this->ask('Enter super admin email');
        $password = $this->secret('Enter super admin password');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'super_admin',
        ]);

        $this->info('Super admin created successfully!');
        $this->table(
            ['Name', 'Email', 'Role'],
            [[$user->name, $user->email, $user->role]]
        );
    }
}
