<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    public function index()
    {
        $orders = Order::with('items.product', 'customer')->get();

        return $orders->map(function ($order) {
            return [
                'id'             => $order->id,
                'customer_id'    => $order->customer_id,
                'total_quantity' => $order->total_quantity,
                'total_price'    => $order->total_price,
                'items'          => $order->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'name'       => $item->name,   // accessor
                        'quantity'   => $item->quantity,
                        'price'      => $item->price,  // unit price
                    ];
                }),
            ];
        });
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'        => 'required|integer|exists:customers,id',
            'items'              => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        $order = Order::create([
            'customer_id'    => $validated['customer_id'],
            'total_price'    => 0,
            'total_quantity' => 0,
            'status'         => 'pending',
        ]);

        $totalPrice = 0;
        $totalQuantity = 0;

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            $subtotal = $product->price * $item['quantity'];

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'price'      => $product->price, // unit price
            ]);

            $totalPrice += $subtotal;
            $totalQuantity += $item['quantity'];
        }

        $order->update([
            'total_price'    => $totalPrice,
            'total_quantity' => $totalQuantity,
        ]);

        return [
            'id'             => $order->id,
            'customer_id'    => $order->customer_id,
            'total_quantity' => $order->total_quantity,
            'total_price'    => $order->total_price,
            'items'          => $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name'       => $item->name,
                    'quantity'   => $item->quantity,
                    'price'      => $item->price,
                ];
            }),
        ];
    }

    public function show(Order $order)
    {
        return [
            'id'             => $order->id,
            'customer_id'    => $order->customer_id,
            'total_quantity' => $order->total_quantity,
            'total_price'    => $order->total_price,
            'items'          => $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name'       => $item->name,
                    'quantity'   => $item->quantity,
                    'price'      => $item->price,
                ];
            }),
        ];
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'in:pending,completed,cancelled',
        ]);

        $order->update($validated);

        return [
            'id'             => $order->id,
            'customer_id'    => $order->customer_id,
            'total_quantity' => $order->total_quantity,
            'total_price'    => $order->total_price,
            'items'          => $order->items->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name'       => $item->name,
                    'quantity'   => $item->quantity,
                    'price'      => $item->price,
                ];
            }),
        ];
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return response()->json(['message' => 'Order deleted']);
    }
}
