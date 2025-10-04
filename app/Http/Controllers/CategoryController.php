<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($restaurant_id)
    {
        return Category::where("restaurant_id", $restaurant_id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $restaurantId)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        $category = Category::create([
            'restaurant_id' => $restaurantId,
            'name' => $request->name,
            'description' => $request->description
        ]);
        return response()->json($category,201);
    }

    /**
     * Display the specified resource.
     */
    public function show($restaurantId, $id)
    {
        return Category::where("restaurant_id", $restaurantId)->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $restaurantId, $id)
    {
        $category = Category::where('restaurant_id', $restaurantId)
                    ->where('id', $id)
                    ->firstOrFail();
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);
        $category->update($request->all());
        return response()->json($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($restaurantId, $id)
    {
        $category = Category::where('restaurant_id', $restaurantId)
                            ->where('id', $id)
                            ->firstOrFail();
        $category->delete();
        return response()->json(null, 204);
    }
}
