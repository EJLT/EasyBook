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

        // Autenticar usando el guard 'api' (el único guard configurado)
        if ($token = auth('api')->attempt($credentials)) {
            $user = auth('api')->user();
            Log::info('Inicio de sesión exitoso', ['user_id' => $user->id, 'role' => $user->role]);

            return response()->json([
                'token' => $token,
                'role' => $user->role, // Devuelve el rol del usuario
            ]);
        }

        // Si las credenciales no coinciden
        Log::warning('Intento de inicio de sesión fallido con las credenciales', $credentials);
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
}
