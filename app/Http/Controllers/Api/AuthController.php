<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Fitur Registrasi User
     */
    public function register(Request $request)
    {
        try {
            // Validasi Input
            $validator = Validator::make($request->all(), [
                'name'     => 'required|string|max:255',
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Validasi gagal.',
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Buat User Baru
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Generate Token Sanctum
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'       => true,
                'message'      => 'Registrasi berhasil.',
                'data'         => $user,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fitur Login User
     */
    public function login(Request $request)
    {
        try {
            // Cek Autentikasi Email & Password
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Email atau password salah.'
                ], 401);
            }

            // Ambil Data User & Generate Token
            $user  = User::where('email', $request->email)->firstOrFail();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status'       => true,
                'message'      => 'Login berhasil.',
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fitur Profile User (Melihat Profil Sendiri)
     */
    public function profile(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'status'  => true,
                'message' => 'Data profile berhasil diambil.',
                'data'    => $user,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fitur Logout User (Hapus Token Saat Ini)
     */
    public function logout(Request $request)
    {
        try {
            // Hapus Token Sanctum yang Sedang Digunakan
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Logout berhasil.'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}