<?php

use App\Http\Controllers\RestaurantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuItemsController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart & Payments (customer actions)
    Route::get('/cart', [CartController::class,'index']);
    Route::post('/cart/add', [CartController::class,'add']);
    Route::post('/cart/{cart}/checkout', [CartController::class,'checkout']);
    Route::post('/payments', [PaymentController::class,'store']);
    Route::post('/payments/{payment}/confirm', [PaymentController::class,'confirm']);

    // Owner-only routes for managing restaurants
    Route::middleware('role:owner,admin')->group(function() {
        Route::post('/restaurants', [RestaurantController::class,'store']);
        Route::put('/restaurants/{restaurant}', [RestaurantController::class,'update'])->middleware('can:update,restaurant');
        Route::delete('/restaurants/{restaurant}', [RestaurantController::class,'destroy'])->middleware('can:delete,restaurant');
        // categories & menuItems creation protected similarly
        Route::post('/restaurants/{restaurant}/categories', [CategoryController::class,'store'])->middleware('can:create,App\\Models\\Category,restaurant');
        Route::post('/restaurants/{restaurant}/categories/{category}/menuItems', [MenuItemsController::class,'store']);
    });
});

Route::get('/test', function (Request $request) {
    return response()->json([
        'message' => 'API is working!'
    ]);
});

Route::apiResource('restaurants', RestaurantController::class);
Route::put('/restaurants/{restaurant}', [RestaurantController::class,'update'])->middleware('auth:sanctum','can:update,restaurant');
Route::apiResource('restaurants/{restaurant}/categories', CategoryController::class);
Route::apiResource('restaurants/{restaurant}/categories/{category}/menuItems', MenuItemsController::class);
