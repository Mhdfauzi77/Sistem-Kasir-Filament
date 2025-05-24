<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod; // Ganti dengan model yang sesuai
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::all();

        return response()->json([
            'success' => true,
            'message' => 'Sukses menampilkan data',
            'data' => $paymentMethods,
        ]);
    }
}
