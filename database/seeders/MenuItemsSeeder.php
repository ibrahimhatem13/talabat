<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = Restaurant::all();
        $categories = Category::all();

        foreach ($restaurants as $restaurant) {
            foreach (range(1, 10) as $i) {
                MenuItem::create([
                    'name' => fake()->word(),
                    'description' => fake()->sentence(),
                    'price' => fake()->randomFloat(2, 20, 200),
                    'restaurant_id' => $restaurant->id,
                    'category_id' => $categories->random()->id,
                ]);
            }
        }
    }
}
