<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $restaurants = Restaurant::all();

        foreach (range(1, 200) as $i) {
            $customer = $customers->random();
            $restaurant = $restaurants->random();

            Order::create([
                'user_id' => $customer->id,
                'restaurant_id' => $restaurant->id,
                'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
                'total_price' => 0, // هيتظبط بعد إضافة order items
            ]);
        }
    }
}
