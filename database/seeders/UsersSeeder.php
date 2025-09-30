<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 10 مطعم Owners
        User::factory()->count(10)->create([
            'role' => 'owner',
            'password' => Hash::make('password123')
        ]);

        // 40 عميل Customers
        User::factory()->count(40)->create([
            'role' => 'customer',
            'password' => Hash::make('password123')
        ]);
    }
}
