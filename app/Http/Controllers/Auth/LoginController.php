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

        // Intentar con el guard de 'owners'
        if ($token = auth('business_owner')->attempt($credentials)) {
            $user = auth('business_owner')->user();
            return response()->json([
                'token' => $token,
                'role' => 'owner'
            ]);
        }

        // Intentar con el guard de 'users' si no es 'owner'
        if ($token = auth('api')->attempt($credentials)) {
            $user = auth('api')->user();
            return response()->json([
                'token' => $token,
                'role' => 'user'
            ]);
        }

        // Si no coincide, devolver error
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
