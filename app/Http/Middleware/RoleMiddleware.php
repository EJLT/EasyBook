<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;


class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Log para verificar el encabezado de autorización
        Log::info('Authorization Header: ', ['header' => $request->header('Authorization')]);

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token no válido'], 401);
        }

        Log::info('User  data: ', ['user' => $user]);

        if (!$user) {
            return response()->json(['error' => 'User  not found'], 404);
        }

        Log::info('User  role: ', ['role' => $user->role]);

        if ($user->role !== $role) {
            return response()->json(['error' => 'Forbidden, you do not have permission to perform this action'], 403);
        }

        return $next($request);
    }
}
