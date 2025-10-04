<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($restaurantId)
    {
        return Order::where('restaurant_id', $restaurantId);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $restaurantId)
    {
        $request->validate([
            'total_price' => 'nullable|numeric',
            'payment_method' => 'in:cash,card',
        ]);

        $order = Order::create([
            'user_id'=> 1,
            'restaurant_id'=> $request->$restaurantId,
            'status'=> 'pending',
            'total_price'=> $request->total_price,
            'payment_method'=> $request->payment_method,
        ]);
        return response()->json($order,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
