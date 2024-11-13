<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


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
        // Si el guard es 'owner', usa el guard correcto
        if (Auth::guard('owner')->check()) {
            $user = Auth::guard('owner')->user();
        } else {
            $user = Auth::guard('api')->user();
        }

        if (!$user || $user->role !== $role) {
            return response()->json(['message' => 'You don´t have permissions to perform this action'], 403);
        }

        return $next($request);
    }
}
