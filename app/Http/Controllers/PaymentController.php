<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Order;

class PaymentController extends Controller
{
    // Store a new payment (e.g., after checkout or to update payment info)
    public function store(Request $request)
    {
        // Validate incoming request data
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,card,gateway',
        ]);

        // Get the authenticated user
        $user = $request->user();

        // Find the related order
        $order = Order::findOrFail($data['order_id']);

        // Make sure the order belongs to the authenticated user
        if ($order->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Create a new payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount' => $data['amount'],
            'method' => $data['method'],
            'status' => 'pending',
        ]);

        // Return JSON response with created payment
        return response()->json($payment, 201);
    }

    // Confirm a payment (e.g., after a webhook or manual confirmation)
    public function confirm(Request $request, $paymentId)
    {
        // Validate the request data
        $data = $request->validate([
            'transaction_id' => 'nullable|string',
            'status' => 'required|in:completed,failed,pending',
        ]);

        // Find the payment record
        $payment = Payment::findOrFail($paymentId);

        // Update payment transaction ID and status
        $payment->transaction_id = $data['transaction_id'] ?? $payment->transaction_id;
        $payment->status = $data['status'];
        $payment->save();

        // If payment is completed, update the related order status
        if ($payment->status === 'completed') {
            $order = $payment->order;
            $order->status = 'accepted'; // or another status that fits your system
            $order->save();
        }

        // Return updated payment info
        return response()->json($payment);
    }
}
