<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    // Método para obtener la información del usuario autenticado
    public function show(Request $request)
    {
        return response()->json(auth()->user());
    }

    // Método para actualizar la información del usuario
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = auth()->user(); // Obtener el usuario autenticado

        // Actualizar el usuario con los nuevos datos
        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password,
        ]);

        return response()->json(['message' => 'User updated successfully']);
    }
}
