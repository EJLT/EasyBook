<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        Log::info('Intentando iniciar sesión con las credenciales:', $request->only('email', 'password'));

        $credentials = $request->only('email', 'password');

        // Intenta autenticarse como owner
        if ($token = auth('owner')->attempt($credentials)) {
            $user = auth('owner')->user();
            Log::info('Inicio de sesión exitoso como owner', ['user_id' => $user->id, 'role' => $user->role]);

            return response()->json([
                'token' => $token,
                'role' => $user->role
            ]);
        }

        // Intenta autenticarse como usuario normal
        if ($token = auth('api')->attempt($credentials)) {
            $user = auth('api')->user();
            Log::info('Inicio de sesión exitoso como usuario', ['user_id' => $user->id, 'role' => $user->role]);

            return response()->json([
                'token' => $token,
                'role' => $user->role
            ]);
        }

        // Si las credenciales no coinciden con ningún rol
        Log::warning('Intento de inicio de sesión fallido con las credenciales', $credentials);
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
