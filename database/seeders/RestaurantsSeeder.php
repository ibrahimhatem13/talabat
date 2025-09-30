<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owners = User::where('role', 'owner')->get();

        foreach ($owners as $owner) {
            Restaurant::create([
                'name' => fake()->company(),
                'address' => fake()->address(),
                'phone' => fake()->phoneNumber(),
                'user_id' => $owner->id,
            ]);
        }
    }
}
