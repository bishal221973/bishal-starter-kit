<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'bishalcodeslaravel@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                // 'role' => 'admin', // Uncomment if you use a simple role column
            ]
        );
    }
}
