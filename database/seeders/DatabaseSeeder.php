<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@DReaploaDR.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
            'subscription_plan' => 'premium',
            'is_onboarding_completed' => true,
        ]);

        $this->command->info('Super admin created: admin@DReaploaDR.com / admin123');
    }
}
