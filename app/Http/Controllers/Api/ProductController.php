<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
    public function index()
    {
        $products = Product::all();

        return response()->json([
            'success' => true,
            'message' => 'Sukses',
            'data' => $products
        ]);
    }

    // public function store(Request $request)
    // {
    //     // // Validasi input
    //     // $validated = $request->validate([
    //     //     'name' => 'required|string|max:255',
    //     //     'description' => 'required|string',
    //     //     'price' => 'required|numeric',
    //     //     'stock' => 'required|integer',
    //     // ]);

    //     // // Buat produk baru
    //     // $product = Product::create($validated);

    //     // return response()->json([
    //     //     'success' => true,
    //     //     'message' => 'Produk berhasil ditambahkan',
    //     //     'data' => $product
    //     // ]);
    // }

    // public function show(string $id)
    // {
    //     // Implementasi detail produk di sini
    // }

    // public function update(Request $request, string $id)
    // {
    //     // Implementasi update produk di sini
    // }

    // public function destroy(string $id)
    // {
    //     // Implementasi hapus produk di sini
    // }

    public function showByBarcode($barcode)
{
    $product = Product::where('Barcode', $barcode)->first();

    if (!$product) {
        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan',
            'data' => null
        ], 404); // not found
    }

    return response()->json([
        'success' => true,
        'message' => 'Sukses',
        'data' => $product
    ]);
}

    }
