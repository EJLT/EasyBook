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
        // Validación de los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:user,owner',
        ]);

        // Si el role es 'owner', creamos el propietario en ambas tablas
        if ($validatedData['role'] === 'owner') {
            // Crear un propietario en la tabla 'owners'
            $owner = Owner::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'owner', // Definir el role como 'owner' para esta tabla
            ]);

            // Crear el mismo usuario en la tabla 'users'
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'owner', // También el role será 'owner' en la tabla 'users'
            ]);

            // Crear el token JWT para el propietario
            $token = JWTAuth::fromUser($user);

            return response()->json(['token' => $token], 201);
        }

        // Si el role es 'user', solo crear en la tabla 'users'
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'user', // Definir el role como 'user' para esta tabla
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token], 201);
    }
}
