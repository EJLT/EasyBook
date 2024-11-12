<?php

namespace App\Http\Controllers\Auth;


use App\Models\Owner;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:user,business_owner',
        ]);

        // Verificar si el rol es 'business_owner'
        if ($validatedData['role'] === 'business_owner') {
            // Crear un propietario en la tabla 'owners'
            $owner = Owner::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            // Crear un token JWT para el propietario
            $token = JWTAuth::fromUser($owner);

            return response()->json(['token' => $token], 201);
        }

        // Si no es un 'business_owner', crear un usuario normal
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token], 201);
    }
}
