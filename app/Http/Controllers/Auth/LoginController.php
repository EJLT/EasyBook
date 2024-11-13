<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Intenta autenticarse como owner
        if ($token = auth('owner')->attempt($credentials)) {
            $user = auth('owner')->user();
            return response()->json([
                'token' => $token,
                'role' => $user->role
            ]);
        }

        // Intenta autenticarse como usuario normal
        if ($token = auth('api')->attempt($credentials)) {
            $user = auth('api')->user();
            return response()->json([
                'token' => $token,
                'role' => $user->role
            ]);
        }

        // Si las credenciales no coinciden con ningún rol
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
