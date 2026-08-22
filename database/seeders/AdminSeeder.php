<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@healthcare.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        $admin->syncRoles(['admin']);

        Profile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'full_name' => 'System Administrator',
                'national_number' => '0000000001',
                'phone' => '+963911000001',
                'gender' => 'male',
                'address' => 'Damascus',
                'date_of_birth' => '1985-01-01',
            ]
        );
    }
}
