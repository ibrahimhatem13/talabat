<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Restaurant::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $request->validate([
        'name' => 'required|string',
        'description' => 'nullable|string',
        'address' => 'required|string',
        'phone' => 'required|string',
        'opening_hours' => 'nullable|string',
        'image' => 'nullable|string',
    ]);

    $restaurant = Restaurant::create([
        'name' => $request->name,
        'user_id' => 1,
        'description' => $request->description,
        'address' => $request->address,
        'phone' => $request->phone,
        'opening_hours' => $request->opening_hours,
        'image' => $request->image,
    ]);

    return response()->json($restaurant, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Restaurant $restaurant)
    {
        return $restaurant;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'opening_hours' => 'nullable|string',
            'image' => 'nullable|string',
    ]);

        $restaurant->update($data);
        return response()->json($restaurant);

    }


        /**
         * Remove the specified resource from storage.
         */
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();
        return response()->json(null, 204);
    }

}
