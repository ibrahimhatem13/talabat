<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($restaurantId, $categoryId){
    $category = Category::where('restaurant_id', $restaurantId)
                        ->where('id', $categoryId)
                        ->firstOrFail();

    return response()->json($category->menuItems);    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $restaurantId, $categoryId){
        $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'sometimes|boolean'
    ]);
        $menuItem = MenuItem::create([
            'restaurant_id' => $restaurantId,
            'category_id' => $categoryId,
            'name' => $request->name,
            'description' => $request->description,
            'is_available'=> $request->is_available,
            'price'=> $request->price,
        ]);
        return response()->json($menuItem,201);
    }

    /**
     * Display the specified resource.
     */
    public function show($restaurantId, $categoryId, $id)
    {
        $menuItem = MenuItem::where('restaurant_id', $restaurantId)
                    ->where('category_id', $categoryId)
                    ->where('id', $id)
                    ->firstOrFail();
        return response()->json($menuItem);
        }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $restaurantId, $categoryId, $id)
    {
        $menuItem = MenuItem::where('restaurant_id', $restaurantId)
                    ->where('category_id', $categoryId)
                    ->where('id', $id)
                    ->firstOrFail();

        $request->validate([
        'name' => 'required|string',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'is_available' => 'sometimes|boolean'
    ]);
        $menuItem->update($request->all());
        return response()->json($menuItem,201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($restaurantId, $categoryId, $id){
        $menuItem = MenuItem::where('restaurant_id', $restaurantId)
                    ->where('category_id', $categoryId)
                    ->where('id', $id)
                    ->firstOrFail();
        $menuItem->delete();
        return response()->json(null, 204);
    }
}
