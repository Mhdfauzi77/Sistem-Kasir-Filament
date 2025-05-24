<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['orderProducts.product', 'paymentMethod'])->get();

        $orders->transform(function ($order) {
            return [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'payment_method' => $order->paymentMethod->name ?? '-',
                'total' => $order->total,
                'created_at' => $order->created_at,
                'order_products' => $order->orderProducts->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? '-',
                        'quantity' => $item->quantity ?? 0,
                        'unit_price' => $item->unit_price ?? 0,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Daftar pesanan berhasil diambil',
            'data' => $orders,
        ]);
    }

    public function store(Request $request)
{
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'gender' => 'required|string',
            'birthday' => 'required|date',
            'phone' => 'required|string',
            'total_price' => 'required|numeric',
            'note' => 'nullable|string',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'paid_amount' => 'required|numeric|min:0',
            'change_amount' => 'required|numeric|min:0',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ada kesalahan validasi',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Validasi stok
        foreach ($request->items as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            if (!$product || $product->stock < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak cukup untuk produk: ' . ($product->name ?? 'tidak ditemukan'),
                ], 422);
            }
        }

        // Simpan order
        $order = \App\Models\Order::create([
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'phone' => $request->phone,
            'total_price' => $request->total_price,
            'note' => $request->note,
            'payment_method_id' => $request->payment_method_id,
            'paid_amount' => $request->paid_amount,
            'change_amount' => $request->change_amount,
        ]);

        // Simpan detail order dan kurangi stok
        foreach ($request->items as $item) {
            $product = \App\Models\Product::find($item['product_id']);

            $order->orderProducts()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);

            $product->decrement('stock', $item['quantity']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sukses melakukan pemesanan',
            'data' => $order->load('orderProducts'),
        ]);
    }

}
