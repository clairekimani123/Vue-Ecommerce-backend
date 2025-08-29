<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    public function index()
    {
        return Order::with('items.product', 'customer')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,id',
            'products'    => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity'   => 'required|integer|min:1',
        ]);

        // create order
        $order = Order::create([
            'customer_id' => $validated['customer_id'],
            'total_price' => 0,
            'status'      => 'pending',
        ]);

        $total = 0;

        // add order items
        foreach ($validated['products'] as $product) {
            $price = \App\Models\Product::find($product['product_id'])->price;
            $subtotal = $price * $product['quantity'];

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product['product_id'],
                'quantity'   => $product['quantity'],
                'price'      => $subtotal,
            ]);

            $total += $subtotal;
        }

        $order->update(['total_price' => $total]);

        return $order->load('items.product');
    }

    public function show(Order $order)
    {
        return $order->load('items.product', 'customer');
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'in:pending,completed,cancelled',
        ]);

        $order->update($validated);
        return $order->load('items.product', 'customer');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Order deleted']);
    }
}
