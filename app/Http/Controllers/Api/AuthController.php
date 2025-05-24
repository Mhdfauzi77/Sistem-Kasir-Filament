<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            /** 
             * Email 
             * @example fauzi@gmail.com
             */
            'email' => 'required|email',
            /**
             * Password
             * @example 12345
             */
            'password' => 'required|string',
        ]);

        // Ambil user berdasarkan email
        $user = User::where('email', $validated['email'])->first();

        // Cek user dan password
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
                'data' => null,
            ], 422);
        }

        // Buat token
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ],
        ]);
    }
}
