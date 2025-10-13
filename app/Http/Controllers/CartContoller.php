<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;

class CartController extends Controller
{
    // Show all user carts with their items
    public function index(Request $request)
    {
        $user = $request->user();
        $carts = Cart::with('cartItems.menuItem', 'restaurant')
                    ->where('user_id', $user->id)
                    ->get();
        return response()->json($carts);
    }

    // Add an item to the cart (or increase quantity)
    public function add(Request $request)
    {
        $data = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'menu_item_id'  => 'required|exists:menu_items,id',
            'quantity'      => 'nullable|integer|min:1',
            'notes'         => 'nullable|string',
        ]);

        $user = $request->user();
        $menu = MenuItem::findOrFail($data['menu_item_id']);

        // Ensure the menu item belongs to the specified restaurant
        if ($menu->restaurant_id != $data['restaurant_id']) {
            return response()->json(['message' => 'menu_item does not belong to restaurant'], 422);
        }

        // Get or create the user's cart for the restaurant
        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'restaurant_id' => $data['restaurant_id']],
            ['user_id' => $user->id, 'restaurant_id' => $data['restaurant_id']]
        );

        $qty = $data['quantity'] ?? 1;

        // If the item exists, increase quantity; otherwise create a new record
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('menu_item_id', $menu->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $qty;
            if (isset($data['notes'])) $cartItem->notes = $data['notes'];
            $cartItem->save();
        } else {
            $cartItem = CartItem::create([
                'cart_id' => $cart->id,
                'menu_item_id' => $menu->id,
                'quantity' => $qty,
                'notes' => $data['notes'] ?? null,
            ]);
        }

        return response()->json($cart->load('cartItems.menuItem'), 201);
    }

    // Update an existing cart item (quantity / notes)
    public function updateItem(Request $request, $itemId)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $user = $request->user();
        $item = CartItem::findOrFail($itemId);

        if ($item->cart->user_id != $user->id) return response()->json(['message' => 'Unauthorized'], 403);

        $item->quantity = $data['quantity'];
        if (isset($data['notes'])) $item->notes = $data['notes'];
        $item->save();

        return response()->json($item);
    }

    // Remove an item from the cart
    public function removeItem(Request $request, $itemId)
    {
        $user = $request->user();
        $item = CartItem::findOrFail($itemId);

        if ($item->cart->user_id != $user->id) return response()->json(['message' => 'Unauthorized'], 403);

        $cart = $item->cart;
        $item->delete();

        // If the cart is empty, delete it
        if ($cart->cartItems()->count() === 0) $cart->delete();

        return response()->json(null, 204);
    }

    // Clear the entire cart
    public function clear(Request $request, $cartId)
    {
        $user = $request->user();
        $cart = Cart::findOrFail($cartId);

        if ($cart->user_id != $user->id) return response()->json(['message' => 'Unauthorized'], 403);

        $cart->cartItems()->delete();
        $cart->delete();

        return response()->json(null, 204);
    }

    // Convert cart into an order (Checkout)
    public function checkout(Request $request, $cartId)
    {
        $data = $request->validate([
            'payment_method' => 'nullable|in:cash,card',
            'address_id' => 'nullable|exists:addresses,id'
        ]);

        $user = $request->user();
        $cart = Cart::with('cartItems.menuItem')->findOrFail($cartId);

        if ($cart->user_id != $user->id) return response()->json(['message' => 'Unauthorized'], 403);

        if ($cart->cartItems->isEmpty()) return response()->json(['message' => 'Cart is empty'], 422);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($cart->cartItems as $ci) {
                $subtotal += ($ci->menuItem->price * $ci->quantity);
            }

            $deliveryFee = 0; // Simple or fixed fee
            $total = $subtotal + $deliveryFee;

            $order = Order::create([
                'user_id' => $user->id,
                'restaurant_id' => $cart->restaurant_id,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total_price' => $total,
                'status' => 'pending',
                'payment_method' => $data['payment_method'] ?? 'cash',
                'notes' => null,
            ]);

            foreach ($cart->cartItems as $ci) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $ci->menu_item_id,
                    'name' => $ci->menuItem->name,
                    'quantity' => $ci->quantity,
                    'unit_price' => $ci->menuItem->price,
                    'total_price' => $ci->menuItem->price * $ci->quantity,
                ]);
            }

            // If payment method is cash: create a pending payment record
            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'method' => $order->payment_method,
                'status' => ($order->payment_method === 'cash') ? 'pending' : 'pending',
            ]);

            // Clear the cart after checkout
            $cart->cartItems()->delete();
            $cart->delete();

            DB::commit();
            return response()->json($order->load('orderItems'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Checkout failed', 'error' => $e->getMessage()], 500);
        }
    }
}
