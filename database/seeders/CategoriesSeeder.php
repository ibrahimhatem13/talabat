<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Pizza', 'Drinks', 'Desserts', 'Sandwiches', 'Salads'];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat,
            ]);
        }
    }
}
