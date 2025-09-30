<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            $menuItems = MenuItem::where('restaurant_id', $order->restaurant_id)
                    ->inRandomOrder()
                    ->take(rand(1, 5))
                    ->get();

            $total = 0;

            foreach ($menuItems as $item) {
                $qty = rand(1, 3);
                $total += $item->price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item->id,
                    'quantity' => $qty,
                    'price' => $item->price,
                ]);
            }

            $order->update(['total_price' => $total]);
        }
    }
}
