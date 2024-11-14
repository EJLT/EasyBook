<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $token = $request->bearerToken();
        Log::info('Token recibido', ['token' => $token]);

        try {
            if (Auth::guard('api')->check()) {
                $user = Auth::guard('api')->user();
                Log::info('Usuario autenticado', ['user_id' => $user->id, 'role' => $user->role]);

                if ($user->role !== $role) {
                    Log::warning('Usuario sin permisos para realizar esta acción', ['user_id' => $user->id]);
                    return response()->json(['message' => 'You don´t have permissions to perform this action'], 403);
                }

            } else {
                Log::warning('Token inválido o no autenticado');
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        } catch (\Exception $e) {
            Log::error('Error al verificar token', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}
