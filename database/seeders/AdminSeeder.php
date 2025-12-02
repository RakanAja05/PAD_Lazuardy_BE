<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@example.com';

        // Skip if admin already exists
        $existing = User::where('email', $email)->first();
        if ($existing) {
            $this->command->info("Admin user already exists: {$email}");
            return;
        }

        $user = User::create([
            'name' => 'Administrator',
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => RoleEnum::ADMIN->value,
            'telephone_number' => null,
            'profile_photo_url' => null,
        ]);

        $this->command->info('Created admin user: ' . $user->email . ' (password: password123)');
    }
}
