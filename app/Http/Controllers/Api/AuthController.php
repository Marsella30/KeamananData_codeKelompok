<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Pembeli;

class AuthController extends Controller
{
    /**
     * LOGIN → Generate JWT Token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = Pembeli::where('email', $request->email)->first();

        if (!$user) {
            Log::warning("Login gagal: email tidak ditemukan", [
                'email' => $request->email
            ]);

            return response()->json([
                "success" => false,
                "message" => "Email atau password salah"
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            Log::warning("Login gagal: password salah", [
                'email' => $request->email
            ]);

            return response()->json([
                "success" => false,
                "message" => "Email atau password salah"
            ], 401);
        }

        // JWT Payload
        $payload = [
            "sub" => $user->id_pembeli,
            "email" => $user->email,
            "iat" => time(),
            "exp" => time() + env("JWT_TTL", 86400)
        ];

        // Buat token JWT
        $token = JWT::encode($payload, env("JWT_SECRET"), 'HS256');

        Log::info("Login sukses", [
            'user_id' => $user->id_pembeli
        ]);

        return response()->json([
            "success" => true,
            "message" => "Login berhasil!",
            "token"   => $token,
            "user"    => $user
        ]);
    }

    /**
     * GET /api/me → Validasi JWT + Get current user
     */
    public function me(Request $request)
    {
        try {
            $token = $this->getBearerToken($request);

            if (!$token) {
                return response()->json([
                    "status"  => false,
                    "message" => "Token tidak ditemukan"
                ], 401);
            }

            // Decode token
            $decoded = JWT::decode($token, new Key(env("JWT_SECRET"), 'HS256'));

            // Ambil user
            $user = Pembeli::find($decoded->sub);

            if (!$user) {
                return response()->json([
                    "status"  => false,
                    "message" => "User tidak ditemukan"
                ], 404);
            }

            return response()->json([
                "status" => true,
                "data"   => $user
            ]);

        } catch (\Firebase\JWT\ExpiredException $e) {

            Log::warning("Token expired", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                "status"  => false,
                "message" => "Token sudah kedaluwarsa"
            ], 401);

        } catch (\Exception $e) {

            Log::warning("Token invalid", [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                "status"  => false,
                "message" => "Token tidak valid atau sudah kedaluwarsa"
            ], 401);
        }
    }

    /**
     * LOGOUT (dummy, karena JWT tidak disimpan server)
     * Disarankan tetap ada untuk keperluan log.
     */
    public function logout(Request $request)
    {
        $token = $this->getBearerToken($request);

        Log::info("Logout", [
            'token' => $token ? substr($token, 0, 20) . "..." : null
        ]);

        return response()->json([
            "success" => true,
            "message" => "Logout berhasil (JWT dihapus di sisi client)"
        ]);
    }

    /**
     * Ekstrak Bearer token dari header Authorization
     */
    private function getBearerToken(Request $request)
    {
        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return null;
        }

        return substr($header, 7);
    }
}
