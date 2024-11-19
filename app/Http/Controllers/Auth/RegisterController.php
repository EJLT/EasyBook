<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // Crear un nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Si es un owner, asignar el owner_id
        if ($user->role === 'owner') {
            $user->owner_id = $user->id;
            $user->save();
        }

        // Retornar respuesta
        return response()->json(['message' => 'User registered successfully', 'user' => $user]);
    }
}
